@extends('inventory::layouts.master')

@section('page-name', __('inventory::inventory_movements.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center">
                <i class="fas fa-company-plus"></i>
                @lang('inventory::inventory_movements.edit')
            </div>
            <div class="col-6 d-flex justify-content-end">
                <a href="{{ route('backend.inventory_movements.create') }}"
                    class="btn btn-sm btn-outline-primary">@lang('inventory::inventory_movements.create')</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('backend.inventory_movements.update', $resource) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('inventory::inventory_movements.form')
        </form>
    </div>
</div>

@endsection
