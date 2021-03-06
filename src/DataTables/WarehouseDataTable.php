<?php

namespace HDSSolutions\Finpar\DataTables;

use HDSSolutions\Finpar\Models\Warehouse as Resource;
use Yajra\DataTables\Html\Column;

class WarehouseDataTable extends Base\DataTable {

    protected array $with = [
        'branch'
    ];

    public function __construct() {
        parent::__construct(
            Resource::class,
            route('backend.warehouses'),
        );
    }

    protected function getColumns() {
        return [
            Column::computed('id')
                ->title( __('inventory::warehouse.id.0') )
                ->hidden(),

            Column::make('branch.name')
                ->title( __('backend::warehouse.branch_id.0') ),

            Column::make('name')
                ->title( __('inventory::warehouse.name.0') ),

            Column::make('actions'),
        ];
    }

}
