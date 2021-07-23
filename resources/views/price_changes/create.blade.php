@extends('inventory::layouts.master')

@section('page-name', __('inventory::price_changes.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center">
                <i class="fas fa-company-plus"></i>
                @lang('inventory::price_changes.create')
            </div>
            <div class="col-6 d-flex justify-content-end">
                {{-- <a href="{{ route('backend.price_changes.create') }}"
                    class="btn btn-sm btn-outline-primary">@lang('inventory::price_changes.create')</a> --}}
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('backend.price_changes.store') }}" enctype="multipart/form-data">
            @csrf
            @onlyform
            @include('inventory::price_changes.form')
        </form>
    </div>
</div>

@endsection
