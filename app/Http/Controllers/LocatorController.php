<?php

namespace HDSSolutions\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Laravel\DataTables\LocatorDataTable as DataTable;
use HDSSolutions\Laravel\Http\Request;
use HDSSolutions\Laravel\Models\Locator as Resource;
use HDSSolutions\Laravel\Models\Warehouse;

class LocatorController extends Controller {

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
        return $dataTable->render('inventory::locators.index', [ 'count' => Resource::count() ]);
    }

    public function create(Request $request) {
        // get warehouses
        $warehouses = Warehouse::all();

        // show create form
        return view('inventory::locators.create', compact('warehouses'));
    }

    public function store(Request $request) {
        // cast values to boolean
        if ($request->has('default'))   $request->merge([ 'default' => $request->default == 'true' ]);

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
            redirect()->route('backend.locators');
    }

    public function show(Request $request, Resource $resource) {
        // redirect to list
        return redirect()->route('backend.locators');
    }

    public function edit(Request $request, Resource $resource) {
        // get warehouses
        $warehouses = Warehouse::all();

        // show edit form
        return view('inventory::locators.edit', compact('warehouses', 'resource'));
    }

    public function update(Request $request, Resource $resource) {
        // cast values to boolean
        if ($request->has('default'))   $request->merge([ 'default' => $request->default == 'true' ]);

        // update resource
        if (!$resource->update( $request->input() ))
            // redirect with errors
            return back()->withInput()
                ->withErrors( $resource->errors() );

        // redirect to list
        return redirect()->route('backend.locators');
    }

    public function destroy(Request $request, Resource $resource) {
        // delete resource
        if (!$resource->delete())
            // redirect with errors
            return back()
                ->withErrors( $resource->errors() );

        // redirect to list
        return redirect()->route('backend.locators');
    }

}
