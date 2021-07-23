<?php

namespace HDSSolutions\Laravel\DataTables;

use HDSSolutions\Laravel\Models\PriceChange as Resource;
use HDSSolutions\Laravel\Traits\DatatableAsDocument;
use Yajra\DataTables\Html\Column;

class PriceChangeDataTable extends Base\DataTable {
    use DatatableAsDocument;

    protected array $orderBy = [
        'document_status'       => 'asc',
        'document_completed_at' => 'desc',
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

            Column::make('document_number')
                ->title( __('inventory::price_change.document_number.0') ),

            Column::make('description')
                ->title( __('inventory::price_change.description.0') ),

            Column::make('document_status_pretty')
                ->title( __('inventory::price_change.document_status.0') ),

            Column::computed('actions'),
        ];
    }

}
