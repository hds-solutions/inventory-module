<?php

namespace HDSSolutions\Finpar\Imports;

use HDSSolutions\Finpar\Models\Currency;
use HDSSolutions\Finpar\Models\PriceChange;
use HDSSolutions\Finpar\Models\PriceChangeLine;
use HDSSolutions\Finpar\Models\Product;
use HDSSolutions\Finpar\Models\Variant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PriceChangeLinesImporter implements ToModel, WithChunkReading, WithHeadingRow {
    use RemembersChunkOffset;

    public function __construct(
        private PriceChange $resource,
        private Collection $headers,
        private Currency $currency,
        private bool $diff = false,
    ) {
        // log matches
        logger('Matches: '.json_encode($this->headers));
    }

    public function model(array $row) {
        // check if columns has values
        foreach ($this->headers as $field => $header)
            // exit model creation
            if ($row[$header] === null) return null;

        logger('Finding Product|Variant with sku|code: '.$row[ $this->headers['sku'] ]);

        // find variant by SKU
        $variant = Variant::where('sku', trim($row[ $this->headers['sku'] ]) )->first();

        // use product from variant or find by code
        $product = $variant !== null ? $variant->product : Product::where('code', trim($row[ $this->headers['sku'] ]) )->first();

        // skip if product nor variant where not found
        if ($product === null) return null;

        // find existing PriceChangeLine
        $pricechangeLine = $this->resource->lines->first(function($existingLine) use ($product, $variant,) {
            // filter product + variant
            return $existingLine->product_id == $product->id && $existingLine->variant_id == ($variant->id ?? null) &&
                // filter currency
                $existingLine->currency_id == $this->currency->id;

        // create a new line
        }) ?? PriceChangeLine::make([
            'price_change_id'   => $this->resource->id,
            'product_id'        => $product->id,
            'variant_id'        => $variant->id ?? null,
            'currency_id'       => $this->currency->id,
        ]);

        // compare excel price with current product|variant price
        if ($row[ $this->headers['price'] ] == ($current_price = (($variant ?? $product)->price($this->currency)?->pivot))?->price &&
            // check difference only flag
            $this->diff)
            // ignore this line, is equal to current
            return null;

        // update values
        $pricechangeLine->fill([
            // link to pricechange
            'price_change_id'   => $this->resource->id,
            // set current prices
            'current_cost'      => $current_price->cost,
            'current_price'     => $current_price->price,
            'current_limit'     => $current_price->limit,
            // copy cost and limit
            'cost'              => $current_price->cost,
            'limit'             => $current_price->limit,
            // set price from excel
            'price'             => $row[ $this->headers['price'] ],
        ]);

        // return pricechange line
        return $pricechangeLine;
    }

    public function chunkSize():int { return 1000; }
}
