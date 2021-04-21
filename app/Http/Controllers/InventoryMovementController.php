<?php

namespace HDSSolutions\Finpar\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Finpar\DataTables\InventoryMovementDataTable as DataTable;
use HDSSolutions\Finpar\Http\Request;
use HDSSolutions\Finpar\Models\Branch;
use HDSSolutions\Finpar\Models\File;
use HDSSolutions\Finpar\Models\InventoryMovement as Resource;
use HDSSolutions\Finpar\Models\InventoryMovementLine;
use HDSSolutions\Finpar\Models\Locator;
use HDSSolutions\Finpar\Models\Product;
use HDSSolutions\Finpar\Models\Storage;
use HDSSolutions\Finpar\Models\Variant;
use HDSSolutions\Finpar\Models\Warehouse;
use HDSSolutions\Finpar\Traits\CanProcessDocument;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class InventoryMovementController extends Controller {
    use CanProcessDocument;

    protected function documentClass():string {
        // return class
        return Resource::class;
    }

    protected function redirectTo():string {
        // go to inventory view
        return 'backend.inventory_movements.show';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DataTable $dataTable) {
        // check only-form flag
        if ($request->has('only-form'))
            // redirect to popup callback
            return view('backend::components.popup-callback', [ 'resource' => new Resource ]);

        // load resources
        if ($request->ajax()) return $dataTable->ajax();

        // return view with dataTable
        return $dataTable->render('inventory::inventory_movements.index', [ 'count' => Resource::count() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        // get branches with warehouses
        $branches = Branch::with([ 'warehouses.locators' ])->get();
        // get products
        $products = Product::with([ 'images', 'variants' ])->get();
        // show create form
        return view('inventory::inventory_movements.create', compact('branches', 'products'));
    }

    public function stock(Request $request) {
        // get resources
        $warehouse = $request->has('warehouse') ? Warehouse::findOrFail( $request->warehouse ) : null;
        $product = $request->has('product') ? Product::findOrFail( $request->product ) : null;
        $variant = $request->has('variant') ? Variant::findOrFail( $request->variant ) : null;
        $locator = $request->has('locator') ? Locator::findOrFail( $request->locator ) : null;
        // return stock for requested product
        return response()->json([
            'stock'     => $locator ?
                Storage::getFromProductOnLocator( $product, $variant, $locator )->available :
                Storage::getQtyAvailable( $product, $variant, $warehouse->branch ?? null ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // start a transaction
        DB::beginTransaction();

        // create resource
        $resource = Resource::create( $request->input() );

        // check for errors
        if (count($resource->errors()) > 0)
            // redirect with errors
            return back()
                ->withInput()
                ->withErrors( $resource->errors() );

        // sync inventory lines
        if (($redirect = $this->syncLines($resource, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // confirm transaction
        DB::commit();

        // check return type
        if ($request->has('only-form'))
            // redirect to popup callback
            return view('backend::components.popup-callback', compact('resource'));

        // redirect to list
        return redirect()->route('backend.inventory_movements');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function show(Resource $resource) {
        // load inventory data
        $resource->load([
            'warehouse.branch',
            'lines.product.images',
            'lines.variant.images',
            'lines.variant.values.option',
            'lines.variant.values.option_value',
            'lines.locator',
        ]);
        // redirect to list
        return view('inventory::inventory_movements.show', compact('resource'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function edit(Resource $resource) {
        // check if inventory is already approved or completed
        if ($resource->isApproved() || $resource->isCompleted())
            // redirect to show route
            return redirect()->route('backend.inventory_movements.show', $resource);

        // get branches with warehouses
        $branches = Branch::with([ 'warehouses.locators' ])->get();
        // get products
        $products = Product::with([ 'images', 'variants' ])->get();
        // load inventory lines
        $resource->load([
            'warehouse.locators',
            'lines.product.images',
            'lines.variant.images',
        ]);

        // show edit form
        return view('inventory::inventory_movements.edit', compact('branches', 'products', 'resource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // find resource
        $resource = Resource::findOrFail($id);

        // check if inventory is already approved or completed
        if ($resource->isApproved() || $resource->isCompleted())
            // return back with errors
            return back()
                ->withInput()
                ->withErrors([ __('models/inventory.already-completed') ]);

        // start a transaction
        DB::beginTransaction();

        // update resource
        if (!$resource->update( $request->input() ))
            // redirect with errors
            return back()
                ->withInput()
                ->withErrors( $resource->errors() );

        // sync inventory lines
        if (($redirect = $this->syncLines($resource, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // confirm transaction
        DB::commit();

        // redirect to list
        return redirect()->route('backend.inventory_movements.show', $resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resource $resource) {
        // delete resource
        if (!$resource->delete())
            // redirect back with errors
            return back()
                ->withErrors($resource->errors()->any() ? $resource->errors() : [ $resource->getDocumentError() ]);
        // redirect to list
        return redirect()->route('backend.inventory_movements');
    }

    private function syncLines(Resource $resource, array $lines) {
        // load inventory lines
        $resource->load([ 'lines' ]);

        // foreach new/updated lines
        foreach (($lines = array_group( $lines )) as $line) {
            // ignore line if product wasn't specified
            if (!isset($line['product_id']) || !isset($line['locator_id'])) continue;
            // load product
            $product = Product::find($line['product_id']);
            // load variant, if was specified
            $variant = $product->variants->firstWhere('id', $line['variant_id'] ?? null);
            // load locator
            $locator = Locator::find($line['locator_id']);
            $toLocator = Locator::find($line['to_locator_id'] ?? null);

            // find existing line
            $inventoryLine = $resource->lines->first(function($iLine) use ($product, $variant, $locator) {
                return $iLine->product_id == $product->id &&
                    $iLine->variant_id == ($variant->id ?? null) &&
                    $iLine->locator_id == $locator->id;
            // create a new line
            }) ?? InventoryMovementLine::make([
                'inventory_movement_id' => $resource->id,
                'product_id'    => $product->id,
                'variant_id'    => $variant->id ?? null,
                'locator_id'    => $locator->id,
            ]);

            // update line values
            $inventoryLine->fill([
                'quantity'      => $line['quantity'] ?? 0,
                'to_locator_id' => $toLocator->id ?? null,
            ]);
            // save inventory line
            if (!$inventoryLine->save())
                return back()
                    ->withInput()
                    ->withErrors( $inventoryLine->errors() );
        }

        // find removed inventory lines
        foreach ($resource->lines as $line) {
            // deleted flag
            $deleted = true;
            // check against $request->lines
            foreach ($lines as $rLine) {
                // ignore empty lines
                if (!isset($rLine['product_id']) || !isset($rLine['locator_id'])) continue;
                // check if line exists
                if ($line->product_id == $rLine['product_id'] &&
                    $line->variant_id == ($rLine['variant_id'] ?? null) &&
                    $line->locator_id == $rLine['locator_id'])
                    // change flag to keep line
                    $deleted = false;
            }
            // remove line if was deleted
            if ($deleted) $line->delete();
        }

        // return success
        return true;
    }

}
