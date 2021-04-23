@extends('backend::layouts.master')

@section('page-name', __('inventory::pricechanges.title'))
@section('description', __('inventory::pricechanges.description'))

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
                <h2>@lang('inventory::pricechange.lines.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-borderless table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="w-150px">@lang('inventory::pricechange.lines.image.0')</th>
                                <th>@lang('inventory::pricechange.lines.product_id.0')</th>
                                <th>@lang('inventory::pricechange.lines.variant_id.0')</th>
                                <th class="w-150px text-right pr-3">@lang('inventory::pricechange.lines.cost.0')</th>
                                <th class="w-150px text-right pr-3">@lang('inventory::pricechange.lines.price.0')</th>
                                <th class="w-150px text-right pr-3">@lang('inventory::pricechange.lines.limit.0')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($resource->lines as $line)
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <img src="{{ asset(
                                                // has variant and variant has images
                                                $line->variant !== null && $line->variant->images->count() ?
                                                // first variant image
                                                $line->variant->images->first()->url :
                                                // first product image or default as fallback
                                                ($line->product->images->first()->url ?? 'backend-module/assets/images/default.jpg')
                                            ) }}" class="img-fluid mh-75px">
                                        </div>
                                    </td>
                                    <td class="align-middle pl-3">{{ $line->product->name }}</td>
                                    <td class="align-middle pl-3">
                                        <div>{{ $line->variant->sku ?? '--' }}</div>
                                        @if ($line->variant && $line->variant->values->count())
                                        <div class="small pl-2">
                                            @foreach($line->variant->values as $value)
                                                @if ($value->option_value === null) @continue @endif
                                                <div>{{ $value->option->name }}: <b>{{ $value->option_value->value }}</b></div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </td>
                                    <td class="align-middle text-right pr-3">
                                        <span>{{ amount($line->current_cost ?? 0, $line->currency, true) }}</span>
                                        <h4 class="text-nowrap">{{ $line->cost ? amount($line->cost, $line->currency, true) : '--' }}</h4>
                                    </td>
                                    <td class="align-middle text-right pr-3">
                                        <span>{{ amount($line->current_price ?? 0, $line->currency, true) }}</span>
                                        <h4 class="text-nowrap">{{ $line->price ? amount($line->price, $line->currency, true) : '--' }}</h4>
                                    </td>
                                    <td class="align-middle text-right pr-3">
                                        <span>{{ amount($line->current_limit ?? 0, $line->currency, true) }}</span>
                                        <h4 class="text-nowrap">{{ $line->limit ? amount($line->limit, $line->currency, true) : '--' }}</h4>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        @include('backend::components.document-actions', [
            'route'     => 'backend.pricechanges.process',
            'resource'  => $resource,
        ])

    </div>
</div>

@endsection
