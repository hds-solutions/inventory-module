<?php

namespace HDSSolutions\Laravel\DataTables;

use HDSSolutions\Laravel\Models\Warehouse as Resource;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Html\Column;

class WarehouseDataTable extends Base\DataTable {

    protected array $with = [
        'branch',
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
                ->title( __('inventory::warehouse.branch_id.0') ),

            Column::make('name')
                ->title( __('inventory::warehouse.name.0') ),

            Column::computed('actions'),
        ];
    }

    protected function filterBranch(Builder $query, $branch_id):Builder {
        // filter only from Branch
        return $query->where('branch_id', $branch_id);
    }

}
