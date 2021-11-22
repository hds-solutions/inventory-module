<?php

namespace HDSSolutions\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Laravel\DataTables\WarehouseDataTable as DataTable;
use HDSSolutions\Laravel\Http\Request;
use HDSSolutions\Laravel\Models\Branch;
use HDSSolutions\Laravel\Models\Warehouse as Resource;

class WarehouseController extends Controller {

    public function __construct() {
        // check resource Policy
        $this->authorizeResource(Resource::class, 'resource');
    }

    public function index(Request $request, DataTable $dataTable) {
        // check only-form flag
        if ($request->has('only-form'))
            // redirect to popup callback
            return view('backend::components.popup-callback', [ 'resource' => new Resource ]);

        // load resources
        if ($request->ajax()) return $dataTable->ajax();

        // return view with dataTable
        return $dataTable->render('inventory::warehouses.index', [
            'count'                 => Resource::count(),
            'show_company_selector' => !backend()->companyScoped(),
        ]);
    }

    public function create(Request $request) {
        // force company selection
        if (!backend()->companyScoped()) return view('backend::layouts.master', [ 'force_company_selector' => true ]);

        // get branches
        $branches = Branch::all();

        // show create form
        return view('inventory::warehouses.create', compact('branches'));
    }

    public function store(Request $request) {
        // create resource
        $resource = Resource::create( $request->input() );

        // save resource
        if (count($resource->errors()) > 0)
            // redirect with errors
            return back()->withInput()
                ->withErrors( $resource->errors() );

        // check return type
        return $request->has('only-form') ?
            // redirect to popup callback
            view('backend::components.popup-callback', compact('resource')) :
            // redirect to resources list
            redirect()->route('backend.warehouses');
    }

    public function show(Request $request, Resource $resource) {
        // redirect to list
        return redirect()->route('backend.warehouses');
    }

    public function edit(Request $request, Resource $resource) {
        // get branches
        $branches = Branch::all();

        // show edit form
        return view('inventory::warehouses.edit', compact('branches', 'resource'));
    }

    public function update(Request $request, Resource $resource) {
        // update resource
        if (!$resource->update( $request->input() ))
            // redirect with errors
            return back()->withInput()
                ->withErrors( $resource->errors() );

        // redirect to list
        return redirect()->route('backend.warehouses');
    }

    public function destroy(Request $request, Resource $resource) {
        // delete resource
        if (!$resource->delete())
            // redirect with errors
            return back()
                ->withErrors( $resource->errors() );

        // redirect to list
        return redirect()->route('backend.warehouses');
    }
}
