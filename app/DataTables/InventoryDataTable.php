<?php

namespace HDSSolutions\Finpar\DataTables;

use HDSSolutions\Finpar\Models\Inventory as Resource;
use Yajra\DataTables\Html\Column;

class InventoryDataTable extends Base\DataTable {

    protected array $with = [
        'warehouse.branch',
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

            Column::make('warehouse.branch.name')
                ->title( __('inventory::inventory.branch_id.0') ),

            Column::make('description')
                ->title( __('inventory::inventory.description.0') ),

            Column::make('document_status_pretty')
                ->title( __('inventory::inventory.document_status.0') ),

            Column::make('actions'),
        ];
    }

}
