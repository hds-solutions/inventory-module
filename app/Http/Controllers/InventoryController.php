<?php

namespace HDSSolutions\Finpar\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Finpar\DataTables\InventoryDataTable as DataTable;
use HDSSolutions\Finpar\Http\Request;
use HDSSolutions\Finpar\Models\Inventory as Resource;

use App\Events\InventoryLinesImport;
use App\Models\Branch;
use App\Models\File;
use App\Models\Inventory;
use App\Models\InventoryLine;
use App\Models\Locator;
use App\Models\PriceChange;
use App\Models\Product;
use App\Models\Storage;
use App\Models\Variant;
use App\Models\Warehouse;
use App\Traits\CanProcessDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class InventoryController extends Controller {
    use CanProcessDocument;

    protected function documentClass():string {
        // return class
        return Resource::class;
    }

    protected function redirectTo():string {
        // go to inventory view
        return 'backend.inventories.show';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        // fetch all objects
        $inventories = Resource::with([ 'warehouse.branch' ])->get();
        // show a list of objects
        return view('inventories.index', compact('inventories'));
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
        return view('inventories.create', compact('branches', 'products'));
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
        $inventory = Inventory::create( $request->input() );
        // check for errors
        if (count($inventory->errors()) > 0)
            // redirect with errors
            return back()
                ->withInput()
                ->withErrors( $inventory->errors() );

        // sync inventory lines
        if (($redirect = $this->syncLines($inventory, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // check for file import
        if ($request->input('import') == true && ($spreadsheet = $request->file('inventory')) !== null) {
            // // check max 5 inventories
            // if (Inventory::count() > 5 || PriceChange::count() > 5)
            //     return back()->withInput()
            //         ->withErrors([ 'Se ha alcanzado el limite máximo de importación de Excel en modo prueba' ]);

            // save file to disk
            if (!($file = File::upload( $request, $spreadsheet, $this ))->save())
                // redirect back with errors
                return back()
                    ->withInput()
                    ->withErrors( $file->errors() );

            // confirm transaction
            DB::commit();

            // redirect to import headers configuration
            return redirect()->route('backend.inventories.import', [ $inventory, 'import' => $file ]);
        }

        // confirm transaction
        DB::commit();

        // check if import was specified
        return $request->input('import') == true ?
            // redirect to inventory
            redirect()->route('backend.inventories.edit', $inventory) :
            // redirect to list
            redirect()->route('backend.inventories');
    }

    public function import(Request $request, Inventory $inventory, File $import) {
        // get excel headers
        $headers = (new HeadingRowImport)->toCollection( $import->file() )->flatten()->filter();
        // show view to match headers
        return view('inventories.import', compact('inventory', 'import', 'headers'));
    }

    public function doImport(Request $request, Inventory $inventory, File $import) {
        // check if selected headers are different from each other
        if (count( array_unique($request->input('headers')) ) !== count($request->input('headers')))
            // return back with errors
            return back()
                ->withInput()
                ->withErrors([ 'headers' => 'Selected headers must be different from each other' ]);

        // get excel headers
        $headers = (new HeadingRowImport)->toCollection( $import->file() )->flatten()->filter();
        // build matches
        $matches = collect();
        foreach ($request->input('headers') as $field => $header)
            // get match for field from selected header
            $matches->put($field, $headers->get($header));

        // register event to create inventory lines
        InventoryLinesImport::dispatch($inventory, $matches, $import, $request->diff == 'true');

        // return to inventory
        return redirect()->route('backend.inventories.show', $inventory);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory) {
        // load inventory data
        $inventory->load([
            'warehouse.branch',
            'lines.product.images',
            'lines.variant.images',
            'lines.variant.values.option',
            'lines.variant.values.option_value',
            'lines.locator',
        ]);
        // redirect to list
        return view('inventories.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory) {
        // check if inventory is already approved or completed
        if ($inventory->isApproved() || $inventory->isCompleted())
            // redirect to show route
            return redirect()->route('backend.inventories.show', $inventory);

        // get branches with warehouses
        $branches = Branch::with([ 'warehouses.locators' ])->get();
        // get products
        $products = Product::with([ 'images', 'variants' ])->get();
        // load inventory lines
        $inventory->load([
            'warehouse.locators',
            'lines.product.images',
            'lines.variant.images',
        ]);

        // show edit form
        return view('inventories.edit', compact('branches', 'products', 'inventory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // find resource
        $inventory = Inventory::findOrFail($id);

        // check if inventory is already approved or completed
        if ($inventory->isApproved() || $inventory->isCompleted())
            // return back with errors
            return back()
                ->withInput()
                ->withErrors([ __('models/inventory.already-completed') ]);

        // start a transaction
        DB::beginTransaction();

        // update resource
        if (!$inventory->update( $request->input() ))
            // redirect with errors
            return back()
                ->withInput()
                ->withErrors( $inventory->errors() );

        // sync inventory lines
        if (($redirect = $this->syncLines($inventory, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // check for file import
        if ($request->input('import') == true && ($spreadsheet = $request->file('inventory')) !== null) {
            // save file to disk
            if (!($file = File::upload( $request, $spreadsheet, $this ))->save())
                // redirect back with errors
                return back()
                    ->withInput()
                    ->withErrors( $file->errors() );

            // confirm transaction
            DB::commit();

            // redirect to import headers configuration
            return redirect()->route('backend.inventories.import', [ $inventory, 'import' => $file ]);
        }

        // confirm transaction
        DB::commit();

        // check if import was specified
        return $request->input('import') == true ?
            // redirect to inventory
            redirect()->route('backend.inventories.edit', $inventory) :
            // redirect to list
            redirect()->route('backend.inventories.show', $inventory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory) {
        // delete resource
        if (!$inventory->delete())
            // redirect back with errors
            return back()
                ->withErrors($inventory->errors()->any() ? $inventory->errors() : [ $inventory->getDocumentError() ]);
        // redirect to list
        return redirect()->route('backend.inventories');
    }

    private function syncLines(Inventory $inventory, array $lines) {
        // load inventory lines
        $inventory->load([ 'lines' ]);

        // foreach new/updated lines
        foreach (($lines = array_group( $lines )) as $line) {
            // ignore line if product wasn't specified
            if (!isset($line['product_id']) || !isset($line['locator_id'])) continue;
            // load product
            $product = Product::find($line['product_id']);
            // load variant, if was specified
            $variant = isset($line['variant_id']) ? $product->variants->firstWhere('id', $line['variant_id']) : null;
            // load locator
            $locator = Locator::find($line['locator_id']);

            // find existing line
            $inventoryLine = $inventory->lines->first(function($iLine) use ($product, $variant, $locator) {
                return $iLine->product_id == $product->id &&
                    $iLine->variant_id == ($variant->id ?? null) &&
                    $iLine->locator_id == $locator->id;
            // create a new line
            }) ?? InventoryLine::make([
                'inventory_id'  => $inventory->id,
                'product_id'    => $product->id,
                'variant_id'    => $variant->id ?? null,
                'locator_id'    => $locator->id,
            ]);

            // update line values
            $inventoryLine->fill([
                'current'       => $line['current'] ?? 0,
                'counted'       => $line['counted'] ?? null,
                'expire_at'     => $line['expire_at'] ?? null,
            ]);
            // save inventory line
            if (!$inventoryLine->save())
                return back()
                    ->withInput()
                    ->withErrors( $inventoryLine->errors() );
        }

        // find removed inventory lines
        foreach ($inventory->lines as $line) {
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
