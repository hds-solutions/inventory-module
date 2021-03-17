<?php

namespace HDSSolutions\Finpar\DataTables;

use HDSSolutions\Finpar\Models\Locator as Resource;
use Yajra\DataTables\Html\Column;

class LocatorDataTable extends Base\DataTable {

    protected array $with = [
        'warehouse'
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
                ->title( __('inventory::locator.warehouse_id.0') ),

            Column::make('x')
                ->title( __('inventory::locator.x.0') )
                ->renderRaw('concat:x,y,z; : '),

            Column::make('default')
                ->title( __('inventory::locator.default.0') )
                ->renderRaw('boolean'),

            Column::make('actions'),
        ];
    }

}
