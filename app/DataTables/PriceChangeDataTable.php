<?php

namespace HDSSolutions\Finpar\DataTables;

use HDSSolutions\Finpar\Models\PriceChange as Resource;
use Yajra\DataTables\Html\Column;

class PriceChangeDataTable extends Base\DataTable {

    protected array $with = [
        // 'warehouse.branch',
    ];

    public function __construct() {
        parent::__construct(
            Resource::class,
            route('backend.pricechanges'),
        );
    }

    protected function getColumns() {
        return [
            Column::computed('id')
                ->title( __('inventory::pricechange.id.0') )
                ->hidden(),

            Column::make('warehouse.branch.name')
                ->title( __('inventory::pricechange.branch_id.0') ),

            Column::make('description')
                ->title( __('inventory::pricechange.description.0') ),

            Column::make('document_status')
                ->title( __('inventory::pricechange.document_status.0') ),

            Column::make('actions'),
        ];
    }

}
