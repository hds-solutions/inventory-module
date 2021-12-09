@extends('backend::layouts.master')

@section('page-name', __('inventory::in_outs.sales.title'))
@section('description', __('inventory::in_outs.sales.description'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center cursor-pointer"
                data-toggle="collapse" data-target="#filters">
                <i class="fas fa-table mr-2"></i>
                @lang('inventory::in_outs.sales.index')
            </div>
            <div class="col-6 d-flex justify-content-end">
                {{-- <a href="{{ route('backend.sales.in_outs.create') }}"
                   class="btn btn-sm btn-primary">@lang('inventory::in_outs.sales.create')</a> --}}
            </div>
        </div>
        <div class="row collapse @if (request()->has('filters')) show @endif" id="filters">
            <form action="{{ route('backend.sales.in_outs') }}"
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

                    label="inventory::in_out.customer_id.0"
                    placeholder="inventory::in_out.customer_id._"
                    {{-- helper="inventory::in_out.customer_id.?" --}} />

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
                    'resource'  => 'in_outs',
                    'actions'   => [ 'show', 'update', 'delete' ],
                    'label'     => '{resource.document_number}',
                ])
            </div>
        @else
            <div class="text-center m-t-30 m-b-30 p-b-10">
                <h2><i class="fas fa-table text-custom"></i></h2>
                <h3>@lang('backend.empty.title')</h3>
                {{-- <p class="text-muted">
                    @lang('backend.empty.description')
                    <a href="{{ route('backend.sales.in_outs.create') }}" class="text-custom">
                        <ins>@lang('inventory::in_outs.sales.create')</ins>
                    </a>
                </p> --}}
            </div>
        @endif
    </div>
</div>

@endsection

@push('config-scripts')
    {{ $dataTable->scripts() }}
@endpush
