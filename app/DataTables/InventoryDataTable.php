<?php

namespace HDSSolutions\Laravel\DataTables;

use HDSSolutions\Laravel\Models\Inventory as Resource;
use HDSSolutions\Laravel\Traits\DatatableAsDocument;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Column;

class InventoryDataTable extends Base\DataTable {
    use DatatableAsDocument;

    protected array $with = [
        'warehouse.branch',
    ];

    protected array $orderBy = [
        'document_status'   => 'asc',
        'created_at'        => 'desc',
    ];

    public function __construct() {
        parent::__construct(
            Resource::class,
            route('backend.inventories'),
        );
    }

    protected function getColumns() {
        return [
            Column::computed('id')
                ->title( __('inventory::inventory.id.0') )
                ->hidden(),

            Column::make('document_number')
                ->title( __('inventory::inventory.document_number.0') )
                ->renderRaw('bold:document_number'),

            Column::make('created_at')
                ->title( __('inventory::inventory.created_at.0') )
                ->renderRaw('datetime:created_at;F j, Y H:i'),

            Column::make('warehouse.name')
                ->title( __('inventory::inventory.warehouse_id.0') )
                ->renderRaw('view:inventory')
                ->data( view('inventory::inventories.datatable.warehouse')->render() ),

            Column::make('description')
                ->title( __('inventory::inventory.description.0') ),

            Column::make('document_status_pretty')
                ->title( __('inventory::inventory.document_status.0') ),

            Column::computed('actions'),
        ];
    }

    protected function joins(Builder $query):Builder {
        // add custom JOIN to Warehouse.branch
        return $query
            // JOIN to Warehouse
            ->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
                // JOIN to Branch
                ->join('branches', 'branches.id', 'warehouses.branch_id');
    }

    protected function filterBranch(Builder $query, $branch_id):Builder {
        // filter only from Branch
        return $query->where('branch_id', $branch_id);
    }

    protected function filterWarehouse(Builder $query, $warehouse_id):Builder {
        // filter only from Warehouse
        return $query->where('warehouse_id', $warehouse_id);
    }

    protected function searchWarehouseName(Builder $query, string $value):Builder {
        // return custom search for Warehouse.name
        return $query
            // search on Branch
            ->where('branches.name', 'like', "%$value%")
            // search on Warehouse
            ->orWhere('warehouses.name', 'like', "%$value%");
    }

}
