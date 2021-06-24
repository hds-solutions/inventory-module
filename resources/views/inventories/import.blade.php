@extends('inventory::layouts.master')

@section('page-name', __('inventory::inventories.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <i class="fas fa-user-plus"></i>
                @lang('inventory::inventories.show')
            </div>
            <div class="col-6 d-flex justify-content-end">
                @if (!$resource->isCompleted())
                <a href="{{ route('backend.inventories.edit', $resource) }}"
                    class="btn btn-sm ml-2 btn-info">@lang('inventory::inventories.edit')</a>
                @endif
                <a href="{{ route('backend.inventories.create') }}"
                    class="btn btn-sm ml-2 btn-primary">@lang('inventory::inventories.create')</a>
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('backend::components.errors')

        <div class="row">
            <div class="col">
                <h2>@lang('inventory::inventory.details.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory.branch_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->warehouse->branch->name }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory.warehouse_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->warehouse->name }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory.description.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->description }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory.created_at.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ pretty_date($resource->created_at, true) }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory.document_status.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ Document::__($resource->document_status) }}</div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col">
                <h2>@lang('Import Excel')</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('backend.inventories.import', [ $resource, $import ]) }}">
            @csrf

            <div class="row">
                @foreach ([
                    'sku'       => 'SKU',
                    'warehouse' => 'Warehouse',
                    'stock'     => 'Stock',
                ] as $field => $name)
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">@lang('inventory::inventory.import.'.$field)</span>
                            </div>
                            <select name="headers[{{ $field }}]" required
                                class="form-control selectpicker">
                                <option value="" selected disabled hidden></option>
                                @foreach ($headers as $idx => $header)
                                <option value="{{ $idx }}">{{ $header }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach

                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="diff" value="true">
                        <input type="checkbox" id="diff"
                            onchange="this.previousElementSibling.value = this.checked ? 'true' : 'false'"
                            @if (!old('diff') || old('diff') == 'true') checked @endif
                            class="form-check-input {{ $errors->has('diff') ? 'is-danger' : '' }}" placeholder="@lang('Difference only')">
                        <label for="diff" class="form-check-label">@lang('Yes, import only lines with difference')</label>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <input type="submit" class="btn btn-lg btn-primary" value="@lang('Import')">
                </div>
            </div>

        </form>

    </div>
</div>

@endsection
