@extends('backend::layouts.master')

@section('page-name', __('inventory::pricechanges.title'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <i class="fas fa-user-plus"></i>
                @lang('inventory::pricechange.show')
            </div>
            <div class="col-6 d-flex justify-content-end">
                @if (!$resource->isCompleted())
                <a href="{{ route('backend.pricechanges.edit', $resource) }}"
                    class="btn btn-sm ml-2 btn-info">@lang('inventory::pricechange.edit')</a>
                @endif
                <a href="{{ route('backend.pricechanges.create') }}"
                    class="btn btn-sm ml-2 btn-primary">@lang('inventory::pricechange.create')</a>
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('backend::components.errors')

        <div class="row">
            <div class="col">
                <h2>@lang('inventory::pricechange.details.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                {{-- <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::pricechange.branch_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->warehouse->branch->name }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::pricechange.warehouse_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->warehouse->name }}</div>
                </div> --}}

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::pricechange.description.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->description }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::pricechange.created_at.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ pretty_date($resource->created_at, true) }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::pricechange.document_status.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ Document::__($resource->document_status) }}</div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col">
                <h2>@lang('Import Excel')</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('backend.pricechanges.import', [ $resource, $import ]) }}">
            @csrf

            @foreach ([
                'sku'       => 'SKU',
                'price'     => 'Price',
            ] as $field => $name)

            <x-backend-form-select name="headers[{{ $field }}]" required
                :values="$headers" default="null"

                label="{{ 'inventory::inventory.import.'.$field.'.0' }}"
                placeholder="{{ 'inventory::inventory.import.'.$field.'._' }}"
                {{-- helper="{{ 'inventory::inventory.import.'.$field.'.?' }}" --}} />

            @endforeach

            <x-backend-form-foreign :resource="$resource ?? null" name="currency_id" required
                foreign="currencies" :values="$currencies" foreign-add-label="{{ __('cash::currencies.add') }}"

                label="{{ __('cash::cash_book.currency_id.0') }}"
                placeholder="{{ __('cash::cash_book.currency_id._') }}"
                {{-- helper="{{ __('cash::cash_book.currency_id.?') }}" --}} />

            <div class="row mb-2">
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
