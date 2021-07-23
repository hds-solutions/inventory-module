<?php

namespace HDSSolutions\Laravel\DataTables;

use HDSSolutions\Laravel\Models\InventoryMovement as Resource;
use HDSSolutions\Laravel\Traits\DatatableAsDocument;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Column;

class InventoryMovementDataTable extends Base\DataTable {
    use DatatableAsDocument;

    protected array $with = [
        'warehouse.branch',
        'toWarehouse.branch',
    ];

    protected array $orderBy = [
        'document_status'       => 'asc',
        'document_completed_at' => 'desc',
    ];

    public function __construct() {
        parent::__construct(
            Resource::class,
            route('backend.inventory_movements'),
        );
    }

    protected function getColumns() {
        return [
            Column::computed('id')
                ->title( __('inventory::inventory_movement.id.0') )
                ->hidden(),

            Column::make('warehouse.name')
                ->title( __('inventory::inventory_movement.warehouse_id.0') )
                ->renderRaw('view:inventory_movement')
                ->data( view('inventory::inventory_movements.datatable.warehouse')->render() ),

            Column::make('to_warehouse.name')
                ->title( __('inventory::inventory_movement.to_warehouse_id.0') )
                ->renderRaw('view:inventory_movement')
                ->data( view('inventory::inventory_movements.datatable.to_warehouse')->render() ),

            Column::make('description')
                ->title( __('inventory::inventory_movement.description.0') ),

            Column::make('document_status_pretty')
                ->title( __('inventory::inventory_movement.document_status.0') ),

            Column::computed('actions'),
        ];
    }

    protected function joins(Builder $query):Builder {
        // add custom JOIN to Warehouse.branch
        return $query
            // JOIN to Warehouse
            ->join('warehouses', 'warehouses.id', 'inventory_movements.warehouse_id')
                // JOIN to Branch
                ->join('branches', 'branches.id', 'warehouses.branch_id')
            // JOIN to WarehouseTo
            ->join('warehouses as to_warehouses', 'to_warehouses.id', 'inventory_movements.to_warehouse_id')
                // JOIN to Branch
                ->join('branches as to_branches', 'to_branches.id', 'to_warehouses.branch_id');
    }

    protected function orderWarehouseName(Builder $query, string $order):Builder {
        // add custom orderBy for column Warehouse.name
        return $query->orderBy('warehouses.name', $order);
    }

    protected function searchWarehouseName(Builder $query, string $value):Builder {
        // return custom search for Warehouse.name
        return $query
            // search on origin Branch
            ->where('branches.name', 'like', "%$value%")
            // search on origin Warehouse
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

    protected function orderToWarehouseName(Builder $query, string $order):Builder {
        // add custom orderBy for column ToWarehouse.name
        return $query->orderBy('to_warehouses.name', $order);
    }

    protected function searchToWarehouseName(Builder $query, string $value):Builder {
        // return custom search for ToWarehouse.name
        return $query
            // search on destination Branch
            ->where('to_branches.name', 'like', "%$value%")
            // search on destination Warehouse
            ->orWhere('to_warehouses.name', 'like', "%$value%");
    }

    protected function filterToBranch(Builder $query, $branch_id):Builder {
        // filter only from Branch
        return $query->where('to_branches.id', $branch_id);
    }

    protected function filterToWarehouse(Builder $query, $warehouse_id):Builder {
        // filter only from Warehouse
        return $query->where('to_warehouses.id', $warehouse_id);
    }

}
