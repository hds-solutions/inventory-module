@extends('inventory::layouts.master')

@section('page-name', __('inventory::inventory_movements.title'))
@section('description', __('inventory::inventory_movements.description'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center cursor-pointer"
                data-toggle="collapse" data-target="#filters">
                <i class="fas fa-table mr-2"></i>
                @lang('inventory::inventory_movements.index')
            </div>
            <div class="col-6 d-flex justify-content-end">
                <a href="{{ route('backend.inventory_movements.create') }}"
                    class="btn btn-sm btn-outline-primary">@lang('inventory::inventory_movements.create')</a>
            </div>
        </div>
        <div class="row collapse @if (request()->has('filters')) show @endif" id="filters">
            <form action="{{ route('backend.inventory_movements') }}"
                class="col mt-2 pt-3 pb-2 border-top">

                <x-backend-form-foreign name="filters[branch]" id="filter-branch"
                    :values="backend()->branches()" default="{{ request('filters.branch') }}"

                    label="inventory::inventory_movement.branch_id.0"
                    placeholder="inventory::inventory_movement.branch_id._"
                    {{-- helper="inventory::inventory_movement.branch_id.?" --}}>

                    <x-backend-form-foreign name="filters[warehouse]" secondary
                        :values="backend()->warehouses()" default="{{ request('filters.warehouse') }}"

                        filtered-by="#filter-branch" filtered-using="branch"

                        label="inventory::inventory_movement.warehouse_id.0"
                        placeholder="inventory::inventory_movement.warehouse_id._"
                        {{-- helper="inventory::inventory_movement.warehouse_id.?" --}} />

                </x-backend-form-foreign>

                <x-backend-form-foreign name="filters[to-branch]" id="filter-to-branch"
                    :values="backend()->branches()" default="{{ request('filters.to-branch') }}"

                    label="inventory::inventory_movement.to_branch_id.0"
                    placeholder="inventory::inventory_movement.to_branch_id._"
                    {{-- helper="inventory::inventory_movement.to_branch_id.?" --}}>

                    <x-backend-form-foreign name="filters[to-warehouse]" secondary
                        :values="backend()->warehouses()" default="{{ request('filters.to-warehouse') }}"

                        filtered-by="#filter-to-branch" filtered-using="branch"

                        label="inventory::inventory_movement.to_warehouse_id.0"
                        placeholder="inventory::inventory_movement.to_warehouse_id._"
                        {{-- helper="inventory::inventory_movement.to_warehouse_id.?" --}} />

                </x-backend-form-foreign>

                <button type="submit"
                    class="btn btn-sm btn-outline-primary">Filtrar</button>

                <button type="reset"
                    class="btn btn-sm btn-outline-secondary btn-hover-danger">Limpiar filtros</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        @if ($count)
            <div class="table-responsive">
                {{ $dataTable->table() }}
                @include('backend::components.datatable-actions', [
                    'actions'   => [ 'show', 'update', 'delete' ],
                    'label'     => '{resource.description}',
                ])
            </div>
        @else
            <div class="text-center m-t-30 m-b-30 p-b-10">
                <h2><i class="fas fa-table text-custom"></i></h2>
                <h3>@lang('backend.empty.title')</h3>
                <p class="text-muted">
                    @lang('backend.empty.description')
                    <a href="{{ route('backend.inventory_movements.create') }}" class="text-custom">
                        <ins>@lang('inventory::inventory_movements.create')</ins>
                    </a>
                </p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('config-scripts')
{{ $dataTable->scripts() }}
@endpush
