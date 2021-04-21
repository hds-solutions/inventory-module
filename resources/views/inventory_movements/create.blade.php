@extends('backend::layouts.master')

@section('page-name', __('inventory::inventory_movements.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <i class="fas fa-company-plus"></i>
                @lang('inventory::inventory_movements.create')
            </div>
            <div class="col-6 d-flex justify-content-end">
                {{-- <a href="{{ route('backend.inventory_movements.create') }}"
                    class="btn btn-sm btn-primary">@lang('inventory::companieies.add')</a> --}}
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('backend.inventory_movements.store') }}" enctype="multipart/form-data">
            @csrf
            @onlyform
            @include('inventory::inventory_movements.form')
        </form>
    </div>
</div>

@endsection
