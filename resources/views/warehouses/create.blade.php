@extends('backend::layouts.master')

@section('page-name', __('inventory::warehouses.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center">
                <i class="fas fa-company-plus"></i>
                @lang('inventory::warehouses.create')
            </div>
            <div class="col-6 d-flex justify-content-end">
                {{-- <a href="{{ route('backend.warehouses.create') }}"
                    class="btn btn-sm btn-outline-primary">@lang('inventory::warehouses.create')</a> --}}
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('backend.warehouses.store') }}" enctype="multipart/form-data">
            @csrf
            @onlyform
            @include('inventory::warehouses.form')
        </form>
    </div>
</div>

@endsection
