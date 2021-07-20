<?php

namespace HDSSolutions\Laravel\DataTables;

use HDSSolutions\Laravel\Models\MaterialReturn as Resource;
use HDSSolutions\Laravel\Traits\DatatableWithPartnerable;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Column;

class MaterialReturnDataTable extends Base\DataTable {
    use DatatableWithPartnerable;

    protected array $with = [
        'invoice.currency',
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
            route('backend.material_returns'),
        );
    }

    protected function getColumns() {
        return [
            Column::computed('id')
                ->title( __('inventory::material_return.id.0') )
                ->hidden(),

            Column::make('document_number')
                ->title( __('inventory::material_return.document_number.0') )
                ->renderRaw('bold:document_number'),

            Column::make('invoice.document_number')
                ->title( __('inventory::material_return.invoice_id.0') ),

            Column::make('transacted_at')
                ->title( __('inventory::material_return.transacted_at.0') )
                ->renderRaw('datetime:transacted_at;F j, Y H:i'),

            Column::make('warehouse.name')
                ->title( __('inventory::material_return.warehouse_id.0') )
                ->renderRaw('view:in_out')
                ->data( view('inventory::material_returns.datatable.warehouse')->render() ),

            Column::make('partnerable.full_name')
                ->title( __('inventory::material_return.partnerable_id.0') ),

            Column::make('document_status_pretty')
                ->title( __('inventory::material_return.document_status.0') ),

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
