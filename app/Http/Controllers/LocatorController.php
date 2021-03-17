<?php

namespace HDSSolutions\Finpar\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Finpar\DataTables\LocatorDataTable as DataTable;
use HDSSolutions\Finpar\Http\Request;
use HDSSolutions\Finpar\Models\Locator as Resource;
use HDSSolutions\Finpar\Models\Warehouse;

class LocatorController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DataTable $dataTable) {
        // load resources
        if ($request->ajax()) return $dataTable->ajax();
        // return view with dataTable
        return $dataTable->render('inventory::locators.index', [ 'count' => Resource::count() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        // get warehouses
        $warehouses = Warehouse::all();
        // show create form
        return view('inventory::locators.create', compact('warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
        if ($request->has('default'))   $request->merge([ 'default' => $request->default == 'true' ]);

        // create resource
        $resource = Resource::create( $request->input() );

        // save resource
        if (count($resource->errors()) > 0)
            // redirect with errors
            return back()
                ->withInput()
                ->withErrors( $resource->errors() );

        // redirect to list
        return redirect()->route('backend.locators');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function show(Resource $resource) {
        // redirect to list
        return redirect()->route('backend.locators');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function edit(Resource $resource) {
        // get warehouses
        $warehouses = Warehouse::all();
        // show edit form
        return view('inventory::locators.edit', compact('warehouses', 'resource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
        if ($request->has('default'))   $request->merge([ 'default' => $request->default == 'true' ]);

        // find resource
        $resource = Resource::findOrFail($id);

        // update resource
        if (!$resource->update( $request->input() ))
            // redirect with errors
            return back()
                ->withInput()
                ->withErrors( $resource->errors() );

        // redirect to list
        return redirect()->route('backend.locators');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // find resource
        $resource = Resource::findOrFail($id);
        // delete resource
        if (!$resource->delete())
            // redirect with errors
            return back()
                ->withErrors( $resource->errors() );
        // redirect to list
        return redirect()->route('backend.locators');
    }
}
