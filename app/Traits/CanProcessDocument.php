<?php

namespace HDSSolutions\Finpar\Traits;

use HDSSolutions\Finpar\Interfaces\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait CanProcessDocument {

    protected function redirectTo():string {
        // fallback to dashboard
        return 'backend.dashboard';
    }

    protected abstract function documentClass():string;

    public final function processIt(Request $request, $resource) {
        // get action
        $action = $request->input('action') ?? null;

        // find resource
        $resource = $this->documentClass()::findOrFail($resource);

        // check if resource is instance of Document
        if (!$resource instanceof Document)
            // return with errors
            return back()
                ->withInput()
                ->withErrors([ 'Invalid request' ]);

        // start a transaction
        DB::beginTransaction();

        // execute document action
        if (!$resource->processIt( $action ))
            // redirect back with errors
            return back()->withErrors( $resource->getDocumentError() );

        // confirm transaction
        DB::commit();

        // redirect to document
        return redirect()->route( $this->redirectTo(), $resource );
    }
}