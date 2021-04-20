@extends('backend::layouts.master')

@section('page-name', __('inventory::pricechanges.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <i class="fas fa-company-plus"></i>
                @lang('inventory::pricechanges.create')
            </div>
            <div class="col-6 d-flex justify-content-end">
                {{-- <a href="{{ route('backend.pricechanges.create') }}"
                    class="btn btn-sm btn-primary">@lang('inventory::companieies.add')</a> --}}
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('backend.pricechanges.store') }}" enctype="multipart/form-data">
            @csrf
            @onlyform
            @include('inventory::pricechanges.form')
        </form>
    </div>
</div>

@endsection
