<?php

namespace HDSSolutions\Laravel\DataTables;

use HDSSolutions\Laravel\Models\PriceChange as Resource;
use Yajra\DataTables\Html\Column;

class PriceChangeDataTable extends Base\DataTable {

    protected array $with = [
        // 'warehouse.branch',
    ];

    public function __construct() {
        parent::__construct(
            Resource::class,
            route('backend.price_changes'),
        );
    }

    protected function getColumns() {
        return [
            Column::computed('id')
                ->title( __('inventory::price_change.id.0') )
                ->hidden(),

            Column::make('warehouse.branch.name')
                ->title( __('inventory::price_change.branch_id.0') ),

            Column::make('description')
                ->title( __('inventory::price_change.description.0') ),

            Column::make('document_status_pretty')
                ->title( __('inventory::price_change.document_status.0') ),

            Column::make('actions'),
        ];
    }

}
