@extends('backend::layouts.master')

@section('page-name', __('inventory::material_returns.title'))
@section('description', __('inventory::material_returns.description'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center cursor-pointer"
                data-toggle="collapse" data-target="#filters">
                <i class="fas fa-table mr-2"></i>
                @lang('inventory::material_returns.index')
            </div>
            <div class="col-6 d-flex justify-content-end">
                <a href="{{ route('backend.material_returns.create') }}"
                   class="btn btn-sm btn-outline-primary">@lang('inventory::material_returns.create')</a>
            </div>
        </div>
        <div class="row collapse @if (request()->has('filters')) show @endif" id="filters">
            <form action="{{ route('backend.material_returns') }}"
                class="col mt-2 pt-3 pb-2 border-top">

                <x-backend-form-foreign name="filters[branch]" id="filter-branch"
                    :values="backend()->branches()" default="{{ request('filters.branch') }}"

                    label="inventory::in_out.branch_id.0"
                    placeholder="inventory::in_out.branch_id._"
                    {{-- helper="inventory::in_out.branch_id.?" --}} />

                <x-backend-form-foreign name="filters[warehouse]"
                    :values="backend()->warehouses()" default="{{ request('filters.warehouse') }}"

                    filtered-by="#filter-branch" filtered-using="branch"

                    label="inventory::in_out.warehouse_id.0"
                    placeholder="inventory::in_out.warehouse_id._"
                    {{-- helper="inventory::in_out.warehouse_id.?" --}} />

                <x-backend-form-foreign name="filters[partnerable]"
                    :values="$customers" show="full_name" default="{{ request('filters.partnerable') }}"

                    label="inventory::in_out.partnerable_id.0"
                    placeholder="inventory::in_out.partnerable_id._"
                    {{-- helper="inventory::in_out.partnerable_id.?" --}} />

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
                    'label'     => '{resource.document_number}',
                ])
            </div>
        @else
            <div class="text-center m-t-30 m-b-30 p-b-10">
                <h2><i class="fas fa-table text-custom"></i></h2>
                <h3>@lang('inventory::material_returns.title')</h3>
                <p class="text-muted">
                    @lang('inventory::material_returns.description')
                    <a href="{{ route('backend.material_returns.create') }}" class="text-custom">
                        <ins>@lang('inventory::material_returns.create')</ins>
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
