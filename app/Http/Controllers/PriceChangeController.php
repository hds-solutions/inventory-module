<?php

namespace HDSSolutions\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Laravel\DataTables\PriceChangeDataTable as DataTable;
use HDSSolutions\Laravel\Http\Request;
use HDSSolutions\Laravel\Jobs\PriceChangeLinesImportJob;
use HDSSolutions\Laravel\Models\Currency;
use HDSSolutions\Laravel\Models\File;
use HDSSolutions\Laravel\Models\PriceChange as Resource;
use HDSSolutions\Laravel\Models\PriceChangeLine;
use HDSSolutions\Laravel\Models\Product;
use HDSSolutions\Laravel\Models\Variant;
use HDSSolutions\Laravel\Traits\CanProcessDocument;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class PriceChangeController extends Controller {
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
        // go to pricechange view
        return 'backend.price_changes.show';
    }

    public function index(Request $request, DataTable $dataTable) {
        // check only-form flag
        if ($request->has('only-form'))
            // redirect to popup callback
            return view('backend::components.popup-callback', [ 'resource' => new Resource ]);

        // load resources
        if ($request->ajax()) return $dataTable->ajax();

        // return view with dataTable
        return $dataTable->render('inventory::price_changes.index', [ 'count' => Resource::count() ]);
    }

    public function create(Request $request) {
        // get products
        $products = Product::with([ 'images', 'variants' ])->get();
        // get currencies
        $currencies = backend()->currencies();

        $highs = [
            'document_number'   => Resource::nextDocumentNumber(),
        ];

        // show create form
        return view('inventory::price_changes.create', compact('products', 'currencies', 'highs'));
    }

    public function price(Request $request) {
        // get resources
        $product = $request->has('product') ? Product::findOrFail( $request->product ) : null;
        $variant = $request->has('variant') ? Variant::findOrFail( $request->variant ) : null;
        $currency = $request->has('currency') ? Currency::findOrFail( $request->currency ) : null;

        // return stock for requested product
        return response()->json( $variant?->price( $currency )?->pivot ?? $product?->price( $currency )?->pivot );
    }

    public function store(Request $request) {
        // start a transaction
        DB::beginTransaction();

        // create resource
        $resource = Resource::create( $request->input() );
        // check for errors
        if (count($resource->errors()) > 0)
            // redirect with errors
            return back()->withInput()
                ->withErrors( $resource->errors() );

        // sync pricechange lines
        if (($redirect = $this->syncLines($resource, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // check for file import
        if ($request->input('import') == true && ($spreadsheet = $request->file('price_change')) !== null) {

            // save file to disk
            if (!($file = File::upload( $request, $spreadsheet, $this ))->save())
                // redirect back with errors
                return back()->withInput()
                    ->withErrors( $file->errors() );

            // confirm transaction
            DB::commit();

            // redirect to import headers configuration
            return redirect()->route('backend.price_changes.import', [ $resource, 'import' => $file ]);
        }

        // confirm transaction
        DB::commit();

        // check if import was specified
        return $request->input('import') == true ?
            // redirect to price_change
            redirect()->route('backend.price_changes.edit', $resource) :
            // redirect to list
            redirect()->route('backend.price_changes');
    }

    public function import(Request $request, Resource $resource, File $import) {
        // load currencies
        $currencies = backend()->currencies();
        // get excel headers
        $headers = (new HeadingRowImport)->toCollection( $import->file() )->flatten()->filter();

        // show view to match headers
        return view('inventory::price_changes.import', compact('resource', 'import', 'currencies', 'headers'));
    }

    public function doImport(Request $request, Resource $resource, File $import) {
        // check if selected headers are different from each other
        if (count( array_unique($request->input('headers')) ) !== count($request->input('headers')))
            // return back with errors
            return back()->withInput()
                ->withErrors([ 'headers' => 'Selected headers must be different from each other' ]);

        // get excel headers
        $headers = (new HeadingRowImport)->toCollection( $import->file() )->flatten()->filter();
        // build matches
        $matches = collect();
        foreach ($request->input('headers') as $field => $header)
            // get match for field from selected header
            $matches->put($field, $headers->get($header));

        // dispatch import job
        PriceChangeLinesImportJob::dispatch($resource, $matches, $import, $request->currency_id, $request->diff == 'true');

        // return to price_change
        return redirect()->route('backend.price_changes.show', $resource);
    }

    public function show(Request $request, Resource $resource) {
        // load pricechange data
        $resource->load([
            'lines.product.images',
            'lines.variant.images',
            'lines.variant.values.option',
            'lines.variant.values.optionValue',
        ]);

        // redirect to list
        return view('inventory::price_changes.show', compact('resource'));
    }

    public function edit(Request $request, Resource $resource) {
        // check if pricechange is already approved or completed
        if ($resource->isApproved() || $resource->isCompleted())
            // redirect to show route
            return redirect()->route('backend.price_changes.show', $resource)
                ->withErrors([ __('models/price_change.already-completed') ]);

        // get products
        $products = Product::with([ 'images', 'variants' ])->get();
        // get currencies
        $currencies = backend()->currencies();

        // load pricechange lines
        $resource->load([
            // 'warehouse.locators',
            'lines.product.images',
            'lines.variant.images',
        ]);

        // show edit form
        return view('inventory::price_changes.edit', compact('products', 'currencies', 'resource'));
    }

    public function update(Request $request, Resource $resource) {
        // check if pricechange is already approved or completed
        if ($resource->isApproved() || $resource->isCompleted())
            // return back with errors
            return back()->withInput()
                ->withErrors([ __('models/price_change.already-completed') ]);

        // start a transaction
        DB::beginTransaction();

        // update resource
        if (!$resource->update( $request->input() ))
            // redirect with errors
            return back()->withInput()
                ->withErrors( $resource->errors() );

        // sync pricechange lines
        if (($redirect = $this->syncLines($resource, $request->get('lines'))) !== true)
            // return redirection
            return $redirect;

        // check for file import
        if ($request->input('import') == true && ($spreadsheet = $request->file('price_change')) !== null) {
            // save file to disk
            if (!($file = File::upload( $request, $spreadsheet, $this ))->save())
                // redirect back with errors
                return back()->withInput()
                    ->withErrors( $file->errors() );

            // confirm transaction
            DB::commit();

            // redirect to import headers configuration
            return redirect()->route('backend.price_changes.import', [ $resource, 'import' => $file ]);
        }

        // confirm transaction
        DB::commit();

        // check if import was specified
        return $request->input('import') == true ?
            // redirect to price_change
            redirect()->route('backend.price_changes.edit', $resource) :
            // redirect to list
            redirect()->route('backend.price_changes.show', $resource);
    }

    public function destroy(Request $request, Resource $resource) {
        // delete resource
        if (!$resource->delete())
            // redirect back with errors
            return back()
                ->withErrors($resource->errors()->any() ? $resource->errors() : [ $resource->getDocumentError() ]);

        // redirect to list
        return redirect()->route('backend.price_changes');
    }

    private function syncLines(Resource $resource, array $lines) {
        // load pricechange lines
        $resource->load([ 'lines' ]);

        // foreach new/updated lines
        foreach (($lines = array_group( $lines )) as $line) {
            // ignore line if product wasn't specified
            if (!isset($line['product_id']) || !isset($line['currency_id'])) continue;

            // load product
            $product = Product::find($line['product_id']);
            // load currency
            $currency = Currency::find($line['currency_id']);
            // load variant, if was specified
            $variant = isset($line['variant_id']) ? $product->variants->firstWhere('id', $line['variant_id']) : null;

            // find existing line
            $pricechangeLine = $resource->lines->first(function($existingLine) use ($product, $variant, $currency) {
                // filter product + variant
                return $existingLine->product_id == $product->id && $existingLine->variant_id == ($variant->id ?? null) &&
                    // filter currency
                    $existingLine->currency_id == $currency->id;

            // create a new line
            }) ?? PriceChangeLine::make([
                'price_change_id'   => $resource->id,
                'product_id'        => $product->id,
                'currency_id'       => $currency->id,
                'variant_id'        => $variant->id ?? null,
            ]);

            // update line values
            $pricechangeLine->fill([
                'current_cost'  => $line['current_cost'] ?? 0,
                'current_price' => $line['current_price'] ?? 0,
                'current_limit' => $line['current_limit'] ?? 0,
                'cost'          => $line['cost'] ?? null,
                'price'         => $line['price'] ?? null,
                'limit'         => $line['limit'] ?? null,
            ]);
            // save pricechange line
            if (!$pricechangeLine->save())
                return back()->withInput()
                    ->withErrors( $pricechangeLine->errors() );
        }

        // find removed pricechange lines
        foreach ($resource->lines as $line) {
            // deleted flag
            $deleted = true;
            // check against $request->lines
            foreach ($lines as $rLine) {
                // ignore empty lines
                if (!isset($rLine['product_id']) || !isset($rLine['currency_id'])) continue;
                // check if line exists
                if ($line->product_id == $rLine['product_id'] &&
                    $line->currency_id == $rLine['currency_id'] &&
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
