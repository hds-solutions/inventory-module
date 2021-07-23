<?php

namespace HDSSolutions\Laravel\DataTables;

use HDSSolutions\Laravel\Models\Locator as Resource;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Column;

class LocatorDataTable extends Base\DataTable {

    protected array $with = [
        'warehouse.branch',
    ];

    protected array $orderBy = [
        'warehouse.name'    => 'asc',
        'x'                 => 'asc',
        'y'                 => 'asc',
        'z'                 => 'asc',
    ];

    public function __construct() {
        parent::__construct(
            Resource::class,
            route('backend.locators'),
        );
    }

    protected function getColumns() {
        return [
            Column::computed('id')
                ->title( __('inventory::locator.id.0') )
                ->hidden(),

            Column::make('warehouse.name')
                ->title( __('inventory::locator.warehouse_id.0') )
                ->renderRaw('view:locator')
                ->data( view('inventory::locators.datatable.warehouse')->render() ),

            Column::make('x')
                ->title( __('inventory::locator.x.0') )
                ->renderRaw('concat:x,y,z; : '),
            Column::make('y')->visible(false),
            Column::make('z')->visible(false),

            Column::make('default')
                ->title( __('inventory::locator.default.0') )
                ->renderRaw('boolean'),

            Column::computed('actions'),
        ];
    }

    protected function joins(Builder $query):Builder {
        // add custom JOIN to Warehouse
        return $query
            // JOIN to Warehouse
            ->join('warehouses', 'warehouses.id', 'locators.warehouse_id')
                // JOIN to Branch
                ->join('branches', 'branches.id', 'warehouses.branch_id');
    }

    protected function orderWarehouseName(Builder $query, string $order):Builder {
        // add custom orderBy for column Warehouse.name
        return $query->orderBy('warehouses.name', $order);
    }

    protected function searchWarehouseName(Builder $query, string $value):Builder {
        // return custom search for Warehouse.name
        return $query
            // search on Branch
            ->where('branches.name', 'like', "%$value%")
            // search on Warehouse
            ->orWhere('warehouses.name', 'like', "%$value%");
    }

    protected function filterBranch(Builder $query, $branch_id):Builder {
        // filter only from Branch
        return $query->where('branches.id', $branch_id);
    }

    protected function filterWarehouse(Builder $query, $warehouse_id):Builder {
        // filter only from Warehouse
        return $query->where('warehouses.id', $warehouse_id);
    }

}
