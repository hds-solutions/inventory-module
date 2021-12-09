<?php

namespace HDSSolutions\Laravel\Http\Controllers;

use HDSSolutions\Laravel\DataTables\SaleInOutsDataTable as DataTable;
use HDSSolutions\Laravel\Http\Request;
use HDSSolutions\Laravel\Models\InOut as Resource;
use HDSSolutions\Laravel\Models\Customer;
use HDSSolutions\Laravel\Models\Employee;
use HDSSolutions\Laravel\Models\Product;

class SaleInOutController extends InOutController {

    protected function documentType():string { return 'sale'; }

    protected function getPartnerable($partnerable) {
        return Customer::findOrFail( $partnerable );
    }

    public function index(Request $request, DataTable $dataTable) {
        // check only-form flag
        if ($request->has('only-form'))
            // redirect to popup callback
            return view('backend::components.popup-callback', [ 'resource' => new Resource ]);

        // load resources
        if ($request->ajax()) return $dataTable->ajax();

        // load customers
        $customers = Customer::ordered()->with([
            // 'addresses', // TODO: Partnerable.addresses
        ])->get();

        // return view with dataTable
        return $dataTable->render('inventory::sales.in_outs.index', compact('customers') + [
            'count'                 => Resource::isSale()->count(),
            'show_company_selector' => !backend()->companyScoped(),
        ]);
    }

    public function edit(Request $request, Resource $resource) {
        // check if document is already approved or processed
        if ($resource->isApproved() || $resource->wasProcessed())
            // redirect to show route
            return redirect()->route('backend.sales.in_outs.show', $resource);

        // load resource relations
        $resource->load([
            'order',
            'lines' => fn($line) => $line->with([
                'product',
            ]),
        ]);

        // load customers
        $customers = Customer::ordered()->with([
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
        $employees = Employee::ordered()->get();
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
        return view('inventory::sales.in_outs.edit', compact('resource',
            'customers',
            'branches',
            'employees',
            'products',
        ));
    }

}
