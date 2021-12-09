@extends('backend::layouts.master')

@section('page-name', __('inventory::in_outs.purchases.title'))

@section('content')

    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col-6 d-flex align-items-center">
                    <i class="fas fa-company-plus"></i>
                    @lang('inventory::in_outs.purchases.edit')
                </div>
                <div class="col-6 d-flex justify-content-end">
                     {{-- <a href="{{ route('backend.purchases.in_outs.create') }}"
                        class="btn btn-sm btn-outline-primary">@lang('inventory::in_outs.purchases.create')</a> --}}
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('backend.purchases.in_outs.update', $resource) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('inventory::purchases.in_outs.form')
            </form>
        </div>
    </div>

@endsection
