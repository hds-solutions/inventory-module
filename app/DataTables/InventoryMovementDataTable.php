<?php

namespace HDSSolutions\Finpar\DataTables;

use HDSSolutions\Finpar\Models\InventoryMovement as Resource;
use Yajra\DataTables\Html\Column;

class InventoryMovementDataTable extends Base\DataTable {

    protected array $with = [
        'warehouse.branch',
        'toWarehouse.branch',
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

            Column::make('warehouse.branch.name')
                ->title( __('inventory::inventory_movement.branch_id.0') ),

            Column::make('to_warehouse.branch.name')
                ->title( __('inventory::inventory_movement.to_branch_id.0') ),

            Column::make('description')
                ->title( __('inventory::inventory_movement.description.0') ),

            Column::make('document_status_pretty')
                ->title( __('inventory::inventory_movement.document_status.0') ),

            Column::make('actions'),
        ];
    }

}
