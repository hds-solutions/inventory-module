@extends('backend::layouts.master')

@section('page-name', __('inventory::in_outs.sales.title'))

@section('content')

    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col-6 d-flex align-items-center">
                    <i class="fas fa-company-plus"></i>
                    @lang('inventory::in_outs.sales.edit')
                </div>
                <div class="col-6 d-flex justify-content-end">
                     {{-- <a href="{{ route('backend.sales.in_outs.create') }}"
                        class="btn btn-sm btn-outline-primary">@lang('inventory::in_outs.sales.create')</a> --}}
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('backend.sales.in_outs.update', $resource) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('inventory::sales.in_outs.form')
            </form>
        </div>
    </div>

@endsection
