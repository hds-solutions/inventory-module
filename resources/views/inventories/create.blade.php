@extends('inventory::layouts.master')

@section('page-name', __('inventory::inventories.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <i class="fas fa-company-plus"></i>
                @lang('inventory::inventories.create')
            </div>
            <div class="col-6 d-flex justify-content-end">
                {{-- <a href="{{ route('backend.inventories.create') }}"
                    class="btn btn-sm btn-primary">@lang('inventory::inventories.create')</a> --}}
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('backend.inventories.store') }}" enctype="multipart/form-data">
            @csrf
            @onlyform
            @include('inventory::inventories.form')
        </form>
    </div>
</div>

@endsection
