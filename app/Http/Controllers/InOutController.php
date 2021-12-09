<?php

namespace HDSSolutions\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Laravel\Http\Request;
use HDSSolutions\Laravel\Models\InOut as Resource;
use HDSSolutions\Laravel\Models\Currency;
use HDSSolutions\Laravel\Models\InOutLine;
use HDSSolutions\Laravel\Models\Product;
use HDSSolutions\Laravel\Models\Variant;
use HDSSolutions\Laravel\Traits\CanProcessDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class InOutController extends Controller {
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
        return 'backend.'.$this->prefix().'.in_outs.show';
    }

    protected abstract function documentType():string;

    protected final function isPurchaseDocument():bool { return $this->documentType() === 'purchase'; }
    protected final function isSaleDocument():bool { return $this->documentType() === 'sale'; }
    protected final function prefix():string { return Str::plural($this->documentType()); }

    // public abstract function index(Request $request, DataTableContract $dataTable);

    protected abstract function getPartnerable($partnerable);

    public final function show(Request $request, Resource $resource) {
        // load inOut data
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
        return view('inventory::'.$this->prefix().'.in_outs.show', compact('resource'));
    }

    public final function update(Request $request, Resource $resource) {
        // set is_purchase flag
        $request->merge([ 'is_purchase' => $this->isPurchaseDocument() ]);

        // start a transaction
        DB::beginTransaction();

        // associate Partner
        $resource->partnerable()->associate( $this->getPartnerable($request->partnerable_id) );

        // save resource
        if (!$resource->update( $request->input() ))
            // redirect with errors
            return back()->withInput()
                ->withErrors( $resource->errors() );

        // sync inOut lines
        if (($redirect = $this->syncLines($resource, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // confirm transaction
        DB::commit();

        // redirect to resource details
        return redirect()->route('backend.'.$this->prefix().'.in_outs.show', $resource);
    }

    public final function destroy(Request $request, Resource $resource) {
        // delete resource
        if (!$resource->delete())
            // redirect with errors
            return back()
                ->withErrors($resource->errors()->any() ? $resource->errors() : [ $resource->getDocumentError() ]);

        // redirect to list
        return redirect()->route('backend.in_outs');
    }

    private function syncLines(Resource $resource, array $lines) {
        // load inOut lines
        $resource->load(['lines']);

        // foreach new/updated lines
        foreach (($lines = array_group( $lines )) as $line) {
            // ignore line if product wasn't specified
            if (!isset($line['product_id']) || is_null($line['locator_id']) || is_null($line['quantity_movement'])) continue;
            // load product
            $product = Product::find($line['product_id']);
            // load variant, if was specified
            $variant = isset($line['variant_id']) ? $product->variants->firstWhere('id', $line['variant_id']) : null;

            // find existing line
            $inOutLine = $resource->lines->first(function($iLine) use ($product, $variant) {
                return $iLine->product_id == $product->id &&
                    $iLine->variant_id == ($variant->id ?? null);
            // create a new line
            }) ?? InOutLine::make([
                'in_out_id'     => $resource->id,
                'product_id'    => $product->id,
                'variant_id'    => $variant->id ?? null,
            ]);

            // update line values
            $inOutLine->fill([
                'locator_id'        => $line['locator_id'],
                'quantity_movement' => $line['quantity_movement'],
            ]);
            // save inOut line
            if (!$inOutLine->save())
                return back()->withInput()
                    ->withErrors( $inOutLine->errors() );
        }

        // find removed inOut lines
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
