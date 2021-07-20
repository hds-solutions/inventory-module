<?php

namespace HDSSolutions\Laravel\DataTables;

use HDSSolutions\Laravel\Models\InOut as Resource;
use HDSSolutions\Laravel\Traits\DatatableWithPartnerable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Column;

class InOutDataTable extends Base\DataTable {
    use DatatableWithPartnerable;

    protected array $with = [
        'order.currency',
        'partnerable',
        'warehouse.branch',
    ];

    protected array $orderBy = [
        'document_status'   => 'asc',
        'transacted_at'     => 'desc',
    ];

    public function __construct() {
        parent::__construct(
            Resource::class,
            route('backend.in_outs'),
        );
    }

    protected function getColumns() {
        return [
            Column::computed('id')
                ->title( __('inventory::in_out.id.0') )
                ->hidden(),

            Column::make('document_number')
                ->title( __('inventory::in_out.document_number.0') )
                ->renderRaw('bold:document_number'),

            Column::make('order.document_number')
                ->title( __('inventory::in_out.order_id.0') ),

            Column::make('transacted_at')
                ->title( __('inventory::in_out.transacted_at.0') )
                ->renderRaw('datetime:transacted_at;F j, Y H:i'),

            Column::make('warehouse.name')
                ->title( __('inventory::in_out.warehouse_id.0') )
                ->renderRaw('view:in_out')
                ->data( view('inventory::in_outs.datatable.warehouse')->render() ),

            Column::make('partnerable.full_name')
                ->title( __('inventory::in_out.partnerable_id.0') ),

            Column::make('document_status_pretty')
                ->title( __('inventory::in_out.document_status.0') ),

            Column::make('actions'),
        ];
    }

    protected function joins(Builder $query):Builder {
        // add custom JOIN to customers + people for Partnerable
        return $query
            // join to partnerable
            ->leftJoin('customers', 'customers.id', 'in_outs.partnerable_id')
            // join to people
            ->join('people', 'people.id', 'customers.id');
    }

}
