<?php

namespace HDSSolutions\Laravel\Imports;

use HDSSolutions\Laravel\Models\Inventory;
use HDSSolutions\Laravel\Models\InventoryLine;
use HDSSolutions\Laravel\Models\Product;
use HDSSolutions\Laravel\Models\Storage;
use HDSSolutions\Laravel\Models\Variant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InventoryLinesImporter implements ToModel, WithChunkReading, WithHeadingRow {
    use RemembersChunkOffset;

    public function __construct(
        private Inventory $resource,
        private Collection $headers,
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

        // check if row is for current warehouse
        if (isset($this->headers['warehouse']) && $this->resource->warehouse->name !== $row[ $this->headers['warehouse'] ]) return null;

        logger('Finding Product|Variant with sku|code: '.$row[ $this->headers['sku'] ]);

        // find variant by SKU
        $variant = Variant::where('sku', $row[ $this->headers['sku'] ] )->first();

        // use product from variant or find by code
        $product = $variant !== null ? $variant->product : Product::where('code', $row[ $this->headers['sku'] ] )->first();

        // skip if product nor variant where not found
        if ($product === null) return null;

        // get locator
        $locator = $variant->locators()->where('warehouse_id', $this->resource->warehouse_id)->first() ??
            // fallback to location from product
            $product->locators()->where('warehouse_id', $this->resource->warehouse_id)->first() ??
            // fallback to default location for warehouse
            $this->resource->warehouse->locators->first();

        // check if no locator were found
        if ($locator === null) return null;

        // get storage
        $storage = Storage::getFromProductOnLocator($product, $variant, $locator);

        // check flag and compare stock
        if ($this->diff && $storage->onhand == $row[ $this->headers['stock'] ])
            // ignore this line, is equal to current
            return null;

        // create new inventory line
        $inventoryLine = InventoryLine::make([
            // link to inventory
            'inventory_id'  => $this->resource->id,
            // set location from variant
            'locator_id'    => $locator->id,
            // link product + variant
            'product_id'    => $product->id,
            'variant_id'    => $variant->id,
            // set current stock and counted stock from excel
            'current'       => $storage->onhand,
            'counted'       => $row[ $this->headers['stock'] ],
        ]);

        // return inventory line
        return $inventoryLine;
    }

    public function chunkSize():int { return 1000; }
}
