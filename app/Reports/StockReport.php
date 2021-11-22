<?php

namespace HDSSolutions\Laravel\Reports;

use HDSSolutions\Laravel\DataTables\Base\DataTable;
use HDSSolutions\Laravel\Models\Product as Resource;
use Illuminate\Database\Eloquent\Builder;
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
        'variant.sku'   => 'asc',
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
                'products.name',
                'products.code',
                'products.tax',
                'products.type_id',
                'products.brand_id',
                'products.line_id',
                'products.family_id',
                'variants.sku',
                'storages.pending',
                'storages.reserved',
                'storages.onhand',
                'locators.x',
                'locators.y',
                'locators.z',
                'branches.name AS b_name',
                'warehouses.name AS w_name',
            );
    }

    protected function joins(Builder $query):Builder {
        // load Products
        return $query
            // join to Type
            ->join('types', 'types.id', 'products.type_id')
            // JOIN to Variants table
            ->leftJoin('variants', 'variants.product_id', 'products.id')

            // join to Brand
            ->leftJoin('brands', 'brands.id', 'products.brand_id')
                // join to Models
                ->leftJoin('models', 'models.id', 'products.model_id')
            // join to Line
            ->leftJoin('lines', 'lines.id', 'products.line_id')

            // JOIN to Storage
            ->leftJoin('storages', fn($join) => $join
                ->on('storages.product_id', '=', 'products.id')
                ->orOn(fn($on) => $on
                    ->where('storages.product_id', '=', 'products.id')
                    ->whereNull('storages.variant_id')
                )
            )
                // JOIN to Locator
                ->leftJoin('locators', 'locators.id', 'storages.locator_id')
                    // JOIN to Warehouse
                    ->leftJoin('warehouses', 'warehouses.id', 'locators.warehouse_id')
                    // JOIN to Branch
                    ->leftJoin('branches', 'branches.id', 'warehouses.branch_id');
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
