@extends('backend::layouts.master')

@section('page-name', __('inventory::material_returns.title'))
@section('description', __('inventory::material_returns.description'))

@section('content')

    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col-6 d-flex align-items-center">
                    <i class="fas fa-table mr-2"></i>
                    @lang('inventory::material_returns.index')
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <a href="{{ route('backend.material_returns.create') }}"
                       class="btn btn-sm btn-outline-primary">@lang('inventory::material_returns.create')</a>
                </div>
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
