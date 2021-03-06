<?php

namespace HDSSolutions\Finpar\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Finpar\DataTables\WarehouseDataTable as DataTable;
use HDSSolutions\Finpar\Http\Request;
use HDSSolutions\Finpar\Models\Branch;
use HDSSolutions\Finpar\Models\Warehouse as Resource;

class WarehouseController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DataTable $dataTable) {
        // load resources
        if ($request->ajax()) return $dataTable->ajax();
        // return view with dataTable
        return $dataTable->render('inventory::warehouses.index', [ 'count' => Resource::count() ]);

        // fetch all objects
        $warehouses = Warehouse::with([ 'branch' ])->get();
        // show a list of objects
        return view('warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        // get branches
        $branches = Branch::all();
        // show create form
        return view('inventory::warehouses.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // create resource
        $resource = Resource::create( $request->input() );

        // save resource
        if (count($resource->errors()) > 0)
            // redirect with errors
            return back()
                ->withInput()
                ->withErrors( $resource->errors() );

        // redirect to list
        return redirect()->route('backend.warehouses');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function show(Resource $resource) {
        // redirect to list
        return redirect()->route('backend.warehouses');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function edit(Resource $resource) {
        // get branches
        $branches = Branch::all();
        // show edit form
        return view('inventory::warehouses.edit', compact('branches', 'resource'));
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

        // update resource
        if (!$resource->update( $request->input() ))
            // redirect with errors
            return back()
                ->withInput()
                ->withErrors( $resource->errors() );

        // redirect to list
        return redirect()->route('backend.warehouses');
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
            return back();
        // redirect to list
        return redirect()->route('backend.warehouses');
    }
}
