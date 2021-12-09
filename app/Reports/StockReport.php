<?php

namespace HDSSolutions\Laravel\Reports;

use HDSSolutions\Laravel\DataTables\Base\DataTable;
use HDSSolutions\Laravel\Models\Product as Resource;
use HDSSolutions\Laravel\Models\Variant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Column;

class StockReport extends DataTable {

    protected array $with = [
        // 'locator.warehouse.branch',
        // 'product',
        // 'variant',
        'type',
        'brand',
        'line',
        'family',
    ];

    protected array $orderBy = [
        'name'          => 'asc',
        'sku'           => 'asc',
        'locator.name'  => 'asc',
    ];

    public function __construct() {
        parent::__construct(
            Resource::class,
            route('backend.reports.inventory.stock'),
        );
    }

    protected function newQuery():Builder {
        // return new query for current eloquent model
        return (new Resource)->newQuery()
            // select only resource table data (custom joins breaks data)
            ->select(
                'products.id AS product_id',
                'products.name',
                'products.tax',
                'products.type_id',
                'products.brand_id',
                'products.line_id',
                'products.family_id',
                'variants.id AS variant_id',
                'variants.sku',

                'branches.name AS b_name',
                'warehouses.name AS w_name',
                'locators.x',
                'locators.y',
                'locators.z',

                'storages.pending',
                'storages.reserved',
                'storages.onhand',

                // 'purchase_currency.code AS purchase_code',
                // 'purchase_currency.decimals AS purchase_decimals',
                // 'purchase_price.price AS purchase_price',

                // 'sale_currency.code AS sale_code',
                // 'sale_currency.decimals AS sale_decimals',
                // 'sale_price.price AS sale_price',

                // 'inventory_lines.expire_at',
            );
    }

    protected function joins(Builder $query):Builder {
        // load Products
        return $query
            // join to Type
            ->join('types', 'types.id', 'products.type_id')
            // JOIN to Variants table
            ->leftJoin('variants', 'variants.product_id', 'products.id')
                ->whereNull('variants.deleted_at')

            // join to Brand
            ->leftJoin('brands', 'brands.id', 'products.brand_id')
                // join to Models
                ->leftJoin('models', 'models.id', 'products.model_id')
            // join to Line
            ->leftJoin('lines', 'lines.id', 'products.line_id')

            // JOIN to Storage
            ->leftJoin('storages', fn($join) => $join
                ->on('storages.product_id', '=', 'products.id')
                ->on('storages.variant_id', '=', 'variants.id')
            )
                // JOIN to Locator
                ->leftJoin('locators', 'locators.id', 'storages.locator_id')
                    // JOIN to Warehouse
                    ->leftJoin('warehouses', 'warehouses.id', 'locators.warehouse_id')
                    // JOIN to Branch
                    ->leftJoin('branches', 'branches.id', 'warehouses.branch_id')

            // // JOIN to InventoryLines
            // ->leftJoin('inventory_lines', fn($join) => $join
            //     ->on('inventory_lines.product_id', '=', 'products.id')
            //     ->on('inventory_lines.variant_id', '=', 'variants.id')
            //     ->whereNotNull('inventory_lines.expire_at')
            // )
            //     // JOIN to Inventories
            //     ->leftJoin('inventories', 'inventories.id', 'inventory_lines.inventory_id')
        ;
    }

    protected function results($results) {
        // get variants
        $variants = Variant::whereIn('id', $results->pluck('variant_id'));
        // load variants purchase prices
        $purchase_prices = $this->variantPrices($variants->get(), 'purchase_price_list');
        // load variants sale prices
        $sale_prices = $this->variantPrices($variants->get(), 'sale_price_list');

        // get latest inventory
        $inventory_lines = $variants->get()->load([
            'inventories'   => fn($inventory) => $inventory
                ->with([ 'lines' ])
                ->whereHas('lines', fn($line) => $line->whereNotNull('expire_at'))
                ->latest()->first(),
        ]);

        // transform results, append prices
        return $results->transform(function($variant) use ($purchase_prices, $sale_prices, $inventory_lines) {
            // get prices
            $purchase_price = $purchase_prices->firstWhere('id', $variant->variant_id)?->prices->first();
            $sale_price = $sale_prices->firstWhere('id', $variant->variant_id)?->prices->first();
            // get inventory
            $inventory = $inventory_lines->firstWhere('id', $variant->variant_id)->inventories->first();

            // add prices
            $variant->purchase_code = $purchase_price?->priceList->currency->code;
            $variant->purchase_decimals = $purchase_price?->priceList->currency->decimals;
            $variant->purchase_price = $purchase_price?->price->price;

            $variant->sale_code = $sale_price?->priceList->currency->code;
            $variant->sale_decimals = $sale_price?->priceList->currency->decimals;
            $variant->sale_price = $sale_price?->price->price;

            // add inventory expire_at
            $variant->expire_at = $inventory?->lines->firstWhere('variant_id', $variant->variant_id)?->expire_at;

            // return variant with prices
            return $variant;
        });
    }

    private function variantPrices($variants, string $price_list) {
        return $variants->load([
            // get valid prices
            'prices' => fn($price) => $price->ordered()->valid()
                // filter purchase
                ->where('price_list_id', request('filters')[ $price_list ] ?? -1)
                ->with([ 'priceList.currency' ])
        ])->transform(fn($variant) => $variant
            // modify prices relation, link to parent resources manually to avoid more queries
            ->setRelation('prices', $variant->prices->take(1)->transform(fn($priceListVersion) => $priceListVersion
                ->setRelation('price', $priceListVersion->price
                    // reset PriceListVersion relation without relations
                    ->setRelation('priceListVersion', $priceListVersion->withoutRelations()
                        // set PriceList relation
                        ->setRelation('priceList', $priceListVersion->priceList)
                    )
                )
            ))
        );
    }

    protected function getTableId():string {
        return class_basename($this->resource).'-report';
    }

    protected function parameters():array {
        return [
            'info'      => false,
            'paging'    => false,
            'searching' => false,
        ];
    }

    protected function getColumns() {
        return [
            Column::make('name')
                ->title( __('products-catalog::product.name.0') ),

            Column::make('sku')
                ->title( __('products-catalog::variant.sku.0') ),

            Column::computed('warehouse.name')
                ->title( __('inventory::warehouse.name.0') )
                ->renderRaw('view:data')
                ->data( view('inventory::reports.stock.warehouse-name')->render() ),

            Column::computed('locator.name')
                ->title( __('inventory::locator.x.0') )
                ->renderRaw('view:data')
                ->data( view('inventory::reports.stock.locator-name')->render() )
                ->addClass('text-center'),

            Column::make('purchase')
                ->title( __('products-catalog::product.prices.purchase.0') )
                ->renderRaw('view:data')
                ->data( view('inventory::reports.stock.purchase')->render() )
                ->class('text-right'),

            Column::make('sale')
                ->title( __('products-catalog::product.prices.sale.0') )
                ->renderRaw('view:data')
                ->data( view('inventory::reports.stock.sale')->render() )
                ->class('text-right'),

            Column::make('pending')
                ->title( __('inventory::storage.pending.0') )
                ->sortable( false )
                ->addClass('w-100px text-center'),

            Column::make('reserved')
                ->title( __('inventory::storage.reserved.0') )
                ->sortable( false )
                ->addClass('w-100px text-center'),

            Column::make('onhand')
                ->title( __('inventory::storage.onhand.0') )
                ->sortable( false )
                ->addClass('w-100px text-center'),

            Column::make('expire_at')
                ->title( __('inventory::inventory_line.expire_at.0') )
                ->renderRaw('datetime:expire_at;F j, Y')
                ->sortable( false )
                ->addClass('text-center'),
        ];
    }

    protected function orderProductName(Builder $query, string $order):Builder {
        // order by Product.name
        return $query->orderBy('products.name', $order);
    }

    protected function orderVariantSku(Builder $query, string $order):Builder {
        // order by Variant.sku
        return $query->orderBy('variants.sku', $order);
    }

    protected function orderLocatorName(Builder $query, string $order):Builder {
        // order by Locator.{x, y, z}
        return $query
            ->orderBy('locators.x', $order)
            ->orderBy('locators.y', $order)
            ->orderBy('locators.z', $order)
            ;
    }

    protected function filterBranch(Builder $query, $branch_id):Builder {
        // filter only from Branch
        return $query->where('branches.id', $branch_id);
    }

    protected function filterWarehouse(Builder $query, $warehouse_id):Builder {
        // filter only from Warehouse
        return $query->where('locators.warehouse_id', $warehouse_id);
    }

    protected function filterType(Builder $query, $type_id):Builder {
        // filter only from Type
        return $query->where('products.type_id', $type_id);
    }

    protected function filterBrand(Builder $query, $brand_id):Builder {
        // filter only from Brand
        return $query->where('products.brand_id', $brand_id);
    }

    protected function filterModel(Builder $query, $model_id):Builder {
        // filter only from Model
        return $query->where('products.model_id', $model_id);
    }

    protected function filterFamily(Builder $query, $family_id):Builder {
        // filter only from Family
        return $query->where('products.family_id', $family_id);
    }

    protected function filterSubFamily(Builder $query, $sub_family_id):Builder {
        // filter only from SubFamily
        return $query->where('products.sub_family_id', $sub_family_id);
    }

    protected function filterLine(Builder $query, $line_id):Builder {
        // filter only from Line
        return $query->where('products.line_id', $line_id);
    }

    protected function filterGama(Builder $query, $gama_id):Builder {
        // filter only from Gama
        return $query->where('products.gama_id', $gama_id);
    }

}
