<?php

namespace HDSSolutions\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Laravel\DataTables\InOutDataTable as DataTable;
use HDSSolutions\Laravel\Http\Request;
use HDSSolutions\Laravel\Models\InOut as Resource;
use HDSSolutions\Laravel\Models\Branch;
use HDSSolutions\Laravel\Models\Currency;
use HDSSolutions\Laravel\Models\Customer;
use HDSSolutions\Laravel\Models\Employee;
use HDSSolutions\Laravel\Models\InOutLine;
use HDSSolutions\Laravel\Models\Product;
use HDSSolutions\Laravel\Models\Variant;
use HDSSolutions\Laravel\Traits\CanProcessDocument;
use Illuminate\Support\Facades\DB;

class InOutController extends Controller {
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
        return 'backend.in_outs.show';
    }

    public function index(Request $request, DataTable $dataTable) {
        // check only-form flag
        if ($request->has('only-form'))
            // redirect to popup callback
            return view('backend::components.popup-callback', [ 'resource' => new Resource ]);

        // load resources
        if ($request->ajax()) return $dataTable->ajax();

        // load customers
        $customers = Customer::all();

        // return view with dataTable
        return $dataTable->render('inventory::in_outs.index', compact('customers') + [ 'count' => Resource::count() ]);
    }

    public function show(Request $request, Resource $resource) {
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
        return view('inventory::in_outs.show', compact('resource'));
    }

    public function edit(Request $request, Resource $resource) {
        // check if document is already approved or processed
        if ($resource->isApproved() || $resource->isProcessed())
            // redirect to show route
            return redirect()->route('backend.in_outs.show', $resource);

        // load resource relations
        $resource->load([
            'order',
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
        return view('inventory::in_outs.edit', compact('customers', 'branches', 'employees', 'products', 'resource'));
    }

    public function update(Request $request, Resource $resource) {
        // cast values to boolean
        if ($request->has('is_purchase'))   $request->merge([ 'is_purchase' => $request->is_purchase == 'true' ]);

        // start a transaction
        DB::beginTransaction();

        // associate Partner
        $resource->partnerable()->associate( Customer::findOrFail($request->get('partnerable_id')) );

        // save resource
        if (!$resource->update( $request->input() ))
            // redirect with errors
            return back()->withInput()
                ->withErrors( $resource->errors() );

        // sync inventory lines
        if (($redirect = $this->syncLines($resource, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // confirm transaction
        DB::commit();

        // redirect to resource details
        return redirect()->route('backend.in_outs.show', $resource);
    }

    public function destroy(Request $request, Resource $resource) {
        // delete resource
        if (!$resource->delete())
            // redirect with errors
            return back()
                ->withErrors($resource->errors()->any() ? $resource->errors() : [ $resource->getDocumentError() ]);

        // redirect to list
        return redirect()->route('backend.in_outs');
    }

    public function price(Request $request) {
        // get resources
        $product = $request->has('product') ? Product::findOrFail($request->product) : null;
        $variant = $request->has('variant') ? Variant::findOrFail($request->variant) : null;
        $currency = $request->has('currency') ? Currency::findOrFail($request->currency) : null;

        // return price for requested product
        return response()->json($variant?->price($currency)?->pivot ?? $product?->price($currency)?->pivot);
    }

    private function syncLines(Resource $resource, array $lines) {
        // load inventory lines
        $resource->load(['lines']);

        // foreach new/updated lines
        foreach (($lines = array_group( $lines )) as $line) {
            // ignore line if product wasn't specified
            if (!isset($line['product_id']) || is_null($line['price']) || is_null($line['quantity'])) continue;
            // load product
            $product = Product::find($line['product_id']);
            // load variant, if was specified
            $variant = isset($line['variant_id']) ? $product->variants->firstWhere('id', $line['variant_id']) : null;

            // find existing line
            $orderLine = $resource->lines->first(function($iLine) use ($product, $variant) {
                return $iLine->product_id == $product->id &&
                    $iLine->variant_id == ($variant->id ?? null);
            // create a new line
            }) ?? InOutLine::make([
                'order_id'      => $resource->id,
                'currency_id'   => $resource->currency_id,
                'product_id'    => $product->id,
                'variant_id'    => $variant->id ?? null,
            ]);

            // update line values
            $orderLine->fill([
                'price'     => $line['price'],
                'quantity'  => $line['quantity'],
                'total'     => $line['total'],
            ]);
            // save inventory line
            if (!$orderLine->save())
                return back()->withInput()
                    ->withErrors( $orderLine->errors() );
        }

        // find removed inventory lines
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
