@extends('inventory::layouts.master')

@section('page-name', __('inventory::reports.stock.title'))
@section('description', __('inventory::reports.stock.description'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center cursor-pointer"
                data-toggle="collapse" data-target="#filters">
                <i class="fas fa-table mr-2"></i>
                @lang('inventory::reports.stock.filters')
            </div>
            <div class="col-6 d-flex justify-content-end" id="report-buttons"></div>
        </div>
        <div class="row collapse @if (request()->has('filters')) show @endif" id="filters">
            <form action="{{ route('backend.reports.inventory.stock') }}"
                class="col mt-2 pt-3 pb-2 border-top">

                <x-backend-form-foreign name="filters[branch]" id="filter-branch"
                    :values="backend()->branches()" default="{{ request('filters.branch') }}"

                    label="inventory::in_out.branch_id.0"
                    placeholder="inventory::in_out.branch_id._"
                    {{-- helper="inventory::in_out.branch_id.?" --}}>

                    <x-backend-form-foreign name="filters[warehouse]" secondary
                        :values="backend()->warehouses()" default="{{ request('filters.warehouse') }}"

                        filtered-by="#filter-branch" filtered-using="branch"

                        label="inventory::in_out.warehouse_id.0"
                        placeholder="inventory::in_out.warehouse_id._"
                        {{-- helper="inventory::in_out.warehouse_id.?" --}} />
                </x-backend-form-foreign>

                <x-backend-form-foreign name="filters[brand]"
                    :values="$brands" default="{{ request('filters.brand') }}"

                    label="products-catalog::product.brand_id.0"
                    placeholder="products-catalog::product.brand_id._"
                    {{-- helper="products-catalog::product.brand_id.?" --}}>

                    <x-backend-form-foreign name="filters[model]" secondary
                        :values="$brands->pluck('models')->flatten()" default="{{ request('filters.model') }}"

                        filtered-by='[name="filters[brand]"]' filtered-using="brand"

                        label="products-catalog::product.model_id.0"
                        placeholder="products-catalog::product.model_id._"
                        {{-- helper="products-catalog::product.model_id.?" --}} />

                </x-backend-form-foreign>

                <x-backend-form-foreign name="filters[family]"
                    :values="$families" default="{{ request('filters.family') }}"

                    label="products-catalog::product.family_id.0"
                    placeholder="products-catalog::product.family_id._"
                    {{-- helper="products-catalog::product.family_id.?" --}}>

                    <x-backend-form-foreign name="filters[sub_family]" secondary
                        :values="$families->pluck('subFamilies')->flatten()" default="{{ request('filters.sub_family') }}"

                        filtered-by='[name="filters[family]"]' filtered-using="family"

                        label="products-catalog::product.sub_family_id.0"
                        placeholder="products-catalog::product.sub_family_id._"
                        {{-- helper="products-catalog::product.sub_family_id.?" --}} />

                </x-backend-form-foreign>

                <x-backend-form-foreign name="filters[line]"
                    :values="$lines" default="{{ request('filters.line') }}"

                    label="products-catalog::product.line_id.0"
                    placeholder="products-catalog::product.line_id._"
                    {{-- helper="products-catalog::product.line_id.?" --}}>

                    <x-backend-form-foreign name="filters[gama]" secondary
                        :values="$lines->pluck('gamas')->flatten()" default="{{ request('filters.gama') }}"

                        filtered-by='[name="filters[line]"]' filtered-using="line"

                        label="products-catalog::product.gama_id.0"
                        placeholder="products-catalog::product.gama_id._"
                        {{-- helper="products-catalog::product.gama_id.?" --}} />

                </x-backend-form-foreign>

                <x-backend-form-foreign name="filters[purchase_price_list]" required
                    :values="$purchase_price_lists" default="{{ $purchase_price_lists->firstWhere('is_default')->id }}"

                    label="inventory::reports.stock.purchase_price_list_id.0"
                    placeholder="inventory::reports.stock.purchase_price_list_id._"
                    {{-- helper="inventory::reports.stock.purchase_price_list_id.?" --}} />

                <x-backend-form-foreign name="filters[sale_price_list]" required
                    :values="$sale_price_lists" default="{{ $sale_price_lists->firstWhere('is_default')->id }}"

                    label="inventory::reports.stock.sale_price_list_id.0"
                    placeholder="inventory::reports.stock.sale_price_list_id._"
                    {{-- helper="inventory::reports.stock.sale_price_list_id.?" --}} />

                <button type="submit"
                    class="btn btn-sm btn-outline-primary">Filtrar</button>

                <button type="reset"
                    class="btn btn-sm btn-outline-secondary btn-hover-danger">Limpiar filtros</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        @if ($count)
            <div class="table-responsive">
                {{ $dataTable->table([ 'class' => 'table table-sm table-bordered table-hover table-striped border-0 m-0' ]) }}
            </div>
        @else
            <div class="text-center m-t-30 m-b-30 p-b-10">
                <h2><i class="fas fa-table text-custom"></i></h2>
                <h3>@lang('backend.empty.title')</h3>
                {{-- <p class="text-muted">
                    @lang('backend.empty.description')
                    <a href="{{ route('backend.inventories.create') }}" class="text-custom">
                        <ins>@lang('inventory::inventories.create')</ins>
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
