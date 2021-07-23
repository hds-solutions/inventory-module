@extends('backend::layouts.master')

@section('page-name', __('inventory::material_returns.title'))

@section('content')

    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col-6 d-flex align-items-center">
                    <i class="fas fa-company-plus mr-2"></i>
                    @lang('inventory::material_returns.edit')
                </div>
                <div class="col-6 d-flex justify-content-end">
                     <a href="{{ route('backend.material_returns.create') }}"
                        class="btn btn-sm btn-outline-primary">@lang('inventory::material_returns.create')</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('backend.material_returns.update', $resource) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('inventory::material_returns.form')
            </form>
        </div>
    </div>

@endsection
