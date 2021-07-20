<?php

namespace HDSSolutions\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Laravel\DataTables\MaterialReturnDataTable as DataTable;
use HDSSolutions\Laravel\Http\Request;
use HDSSolutions\Laravel\Models\Branch;
use HDSSolutions\Laravel\Models\Currency;
use HDSSolutions\Laravel\Models\Customer;
use HDSSolutions\Laravel\Models\Employee;
use HDSSolutions\Laravel\Models\MaterialReturn as Resource;
use HDSSolutions\Laravel\Models\InOutLine;
use HDSSolutions\Laravel\Models\Product;
use HDSSolutions\Laravel\Models\Variant;
use HDSSolutions\Laravel\Traits\CanProcessDocument;
use Illuminate\Support\Facades\DB;

class MaterialReturnController extends Controller {
    use CanProcessDocument;

    public function __construct() {
        // check resource Policy
        $this->authorizeResource(Resource::class, 'resource');
    }

    protected function documentClass():string {
        // return class
        return Resource::class;
    }

    protected function redirectTo():string {
        // go to resource view
        return 'backend.material_returns.show';
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
            return view('inventory::components.popup-callback', [ 'resource' => new Resource ]);

        // load resources
        if ($request->ajax()) return $dataTable->ajax();

        // return view with dataTable
        return $dataTable->render('inventory::material_returns.index', [ 'count' => Resource::count() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        // load customers
        $customers = Customer::with([
            // 'addresses', // TODO: Customer.addresses
            'invoices',
        ])->get();
        // load current company branches with warehouses
        $branches = backend()->company()->branches()->with([
            'warehouses'    => fn($warehouse) => $warehouse->with([
                'locators',
            ]),
        ])->get()->transform(fn($branch) => $branch
            // override loaded warehouses, add relation to parent manually
            ->setRelation('warehouses', $branch->warehouses->transform(fn($warehouse) => $warehouse
                // set Warehouse.branch relation manually to avoid more queries
                ->setRelation('branch', $branch)
                // override loaded locators, add relation to parent manually
                ->setRelation('locators', $warehouse->locators->transform(fn($locator) => $locator
                    // set Locator.warehouse relation manuallty to avoid more queries
                    ->setRelation('warehouse', $warehouse)
                ))
            ))
        );
        // load employees
        $employees = Employee::all();
        // load products
        $products = Product::with([
            'images',
            'variants',
        ])->get()->transform(fn($product) => $product
            // override loaded variants, add relation to parent manually
            ->setRelation('variants', $product->variants->transform(fn($variant) => $variant
                // set Variant.product relation manually to avoid more queries
                ->setRelation('product', $product)
            ))
        );

        $highs = [
            'document_number'   => Resource::nextDocumentNumber(),
        ];

        // show create form
        return view('inventory::material_returns.create', compact('customers', 'branches', 'employees', 'products', 'highs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // start a transaction
        DB::beginTransaction();

        // create resource from Invoice
        $resource = Resource::createFromInvoice( $request->input('invoice_id'), $request->except([
            'partnerable_id',
            'invoice_id',
        ]) );
        // associate Partner
        $resource->partnerable()->associate( Customer::findOrFail($request->partnerable_id) );

        // save resource
        if (!$resource->exists || $resource->getDocumentError() !== null)
            // redirect with errors
            return back()
                ->withErrors( $resource->getDocumentError() )
                ->withInput();

        // confirm transaction
        DB::commit();

        // check return type
        return $request->has('only-form') ?
            // redirect to popup callback
            view('inventory::components.popup-callback', compact('resource')) :
            // redirect to resource details
            redirect()->route('backend.material_returns.show', $resource);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Resource $resource
     * @return \Illuminate\Http\Response
     */
    public function show(Resource $resource) {
        // load inventory data
        $resource->load([
            'branch',
            'partnerable',
            'lines' => fn($line) => $line->with([
                'product.images',
                'variant' => fn($variant) => $variant->with([
                    'images',
                    'values',
                ]),
            ]),
        ]);

        // redirect to list
        return view('inventory::material_returns.show', compact('resource'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Resource $resource
     * @return \Illuminate\Http\Response
     */
    public function edit(Resource $resource) {
        // check if document is already approved or processed
        if ($resource->isApproved() || $resource->isProcessed())
            // redirect to show route
            return redirect()->route('backend.material_returns.show', $resource);

        // load resource relations
        $resource->load([
            'invoice',
            'lines' => fn($line) => $line->with([
                'product',
            ]),
        ]);

        // load customers
        $customers = Customer::with([
            // 'addresses', // TODO: Customer.addresses
        ])->get();
        // load current company branches with warehouses
        $branches = backend()->company()->branches()->with([
            'warehouses'    => fn($warehouse) => $warehouse->with([
                'locators',
            ]),
        ])->get()->transform(fn($branch) => $branch
            // override loaded warehouses, add relation to parent manually
            ->setRelation('warehouses', $branch->warehouses->transform(fn($warehouse) => $warehouse
                // set Warehouse.branch relation manually to avoid more queries
                ->setRelation('branch', $branch)
                // override loaded locators, add relation to parent manually
                ->setRelation('locators', $warehouse->locators->transform(fn($locator) => $locator
                    // set Locator.warehouse relation manuallty to avoid more queries
                    ->setRelation('warehouse', $warehouse)
                ))
            ))
        );
        // load employees
        $employees = Employee::all();
        // load products
        $products = Product::with([
            'images',
            'variants',
        ])->get()->transform(fn($product) => $product
            // override loaded variants, add relation to parent manually
            ->setRelation('variants', $product->variants->transform(fn($variant) => $variant
                // set Variant.product relation manually to avoid more queries
                ->setRelation('product', $product)
            ))
        );

        // show edit form
        return view('inventory::material_returns.edit', compact('customers', 'branches', 'employees', 'products', 'resource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Resource $resource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // find resource
        $resource = Resource::findOrFail($id);

        // start a transaction
        DB::beginTransaction();

        // save resource
        if (!$resource->update( $request->except([
            'partnerable_id',
            'invoice_id',
        ]) ))
            // redirect with errors
            return back()
                ->withErrors( $resource->errors() )
                ->withInput();

        // sync inventory lines
        if (($redirect = $this->syncLines($resource, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // confirm transaction
        DB::commit();

        // redirect to resource details
        return redirect()->route('backend.material_returns.show', $resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Resource $resource
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // find resource
        $resource = Resource::findOrFail($id);
        // delete resource
        if (!$resource->delete())
            // redirect with errors
            return back()
                ->withErrors($resource->errors()->any() ? $resource->errors() : [ $resource->getDocumentError() ]);
        // redirect to list
        return redirect()->route('backend.material_returns');
    }

    private function syncLines(Resource $resource, array $lines) {
        // load resource lines
        $resource->load([ 'lines' ]);

        // foreach new/updated lines
        foreach (($lines = array_group( $lines )) as $line) {
            // ignore line if product wasn't specified
            if (!isset($line['product_id']) || is_null($line['locator_id']) || is_null($line['quantity_movement'])) continue;
            // load product
            $product = Product::find($line['product_id']);
            // load variant, if was specified
            $variant = isset($line['variant_id']) ? $product->variants->firstWhere('id', $line['variant_id']) : null;

            // find existing line
            $materialReturnLine = $resource->lines->first(function($mrLine) use ($product, $variant) {
                return $mrLine->product_id == $product->id &&
                    $mrLine->variant_id == ($variant->id ?? null);
            });
            //
            if ($materialReturnLine === null)
                return back()->withInput()
                    ->withErrors([ 'inventory::material_return.lines.inexistent-line' ]);

            // update line values
            $materialReturnLine->fill([
                'quantity_movement'  => $line['quantity_movement'],
            ]);
            // save resource line
            if (!$materialReturnLine->save())
                return back()->withInput()
                    ->withErrors( $materialReturnLine->errors() );
        }

        // find removed resource lines
        foreach ($resource->lines as $line) {
            // deleted flag
            $deleted = true;
            // check against $request->lines
            foreach ($lines as $rLine) {
                // ignore empty lines
                if (!isset($rLine['product_id'])) continue;
                // check if line exists
                if ($line->product_id == $rLine['product_id'] &&
                    $line->variant_id == ($rLine['variant_id'] ?? null))
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
