@extends('inventory::layouts.master')

@section('page-name', __('inventory::inventories.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center">
                <i class="fas fa-company-plus"></i>
                @lang('inventory::inventories.edit')
            </div>
            <div class="col-6 d-flex justify-content-end">
                <a href="{{ route('backend.inventories.create') }}"
                    class="btn btn-sm btn-outline-primary">@lang('inventory::inventories.create')</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('backend.inventories.update', $resource) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('inventory::inventories.form')
        </form>
    </div>
</div>

@endsection
