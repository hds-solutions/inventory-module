<?php

namespace HDSSolutions\Finpar\DataTables;

use HDSSolutions\Finpar\Models\Inventory as Resource;
use Yajra\DataTables\Html\Column;

class InventoryDataTable extends Base\DataTable {

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

            Column::make('actions'),
        ];
    }

}
