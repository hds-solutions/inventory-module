@extends('backend::layouts.master')

@section('page-name', __('inventory::in_outs.sales.title'))
@section('description', __('inventory::in_outs.sales.description'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center">
                <i class="fas fa-user-plus mr-2"></i>
                @lang('inventory::in_outs.sales.show')
            </div>
            <div class="col-6 d-flex justify-content-end">
                @if (!$resource->isCompleted())
                <a href="{{ route('backend.sales.in_outs.edit', $resource) }}"
                    class="btn btn-sm ml-2 btn-outline-info">@lang('inventory::in_outs.sales.edit')</a>
                @endif
                {{-- <a href="{{ route('backend.sales.in_outs.create') }}"
                    class="btn btn-sm ml-2 btn-primary">@lang('inventory::in_outs.sales.create')</a> --}}
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('backend::components.errors')

        <div class="row">
            <div class="col">
                <h2>@lang('inventory::in_out.details.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xl-6">

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.document_number.0'):</div>
                    <div class="col-8 col-lg-6 h4 font-weight-bold">{{ $resource->document_number }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.warehouse_id.0'):</div>
                    <div class="col-8 col-lg-6  h4">{{ $resource->warehouse->name }} <small class="font-weight-light">[{{ $resource->branch->name }}]</small></div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.customer_id.0'):</div>
                    <div class="col-8 col-lg-6 h4 font-weight-bold">{{ $resource->partnerable->fullname }} <small class="font-weight-light">[{{ $resource->partnerable->ftid }}]</small></div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.order_id.0'):</div>
                    <div class="col-8 col-lg-6 h4"><a href="{{ route('backend.sales.orders.show', $resource->order) }}"
                        class="text-decoration-none text-muted">{{ $resource->order?->document_number }}</a></div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.transacted_at.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ pretty_date($resource->transacted_at, true) }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.document_status.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ Document::__($resource->document_status) }}</div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col">
                <h2>@lang('inventory::in_out.lines.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-borderless table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="w-150px">@lang('inventory::in_out.lines.image.0')</th>
                                <th>@lang('inventory::in_out.lines.product_id.0')</th>
                                <th>@lang('inventory::in_out.lines.variant_id.0')</th>
                                <th class="w-150px text-center">@lang('inventory::in_out.lines.quantity_movement.0')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($resource->lines as $line)
                                <tr data-toggle="collapse" data-target=".line-{{ $line->id }}-details">
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <img src="{{ asset(
                                                // has variant and variant has images
                                                $line->variant !== null && $line->variant->images->count() ?
                                                // first variant image
                                                $line->variant->images->first()->url :
                                                // first product image or default as fallback
                                                ($line->product->images->first()->url ?? 'backend-module/assets/images/default.jpg')
                                            ) }}" class="img-fluid mh-50px">
                                        </div>
                                    </td>
                                    <td class="align-middle pl-3">
                                        <a href="{{ route('backend.products.edit', $line->product) }}"
                                            class="font-weight-bold text-decoration-none">{{ $line->product->name }}</a>
                                    </td>
                                    <td class="align-middle pl-3">
                                        <div>
                                            @if ($line->variant)
                                            <a href="{{ route('backend.variants.edit', $line->variant) }}"
                                                class="font-weight-bold text-decoration-none">{{ $line->variant->sku }}</a>
                                            @else
                                                --
                                            @endif
                                        </div>
                                        @if ($line->variant && $line->variant->values->count())
                                        <div class="small pl-2">
                                            @foreach($line->variant->values as $value)
                                                @if ($value->option_value === null) @continue @endif
                                                <div>{{ $value->option->name }}: <b>{{ $value->option_value->value }}</b></div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center h4 font-weight-bold">{{ $line->quantity_movement }}</td>
                                </tr>

                                <tr class="d-none"></tr>
                                <tr class="collapse line-{{ $line->id }}-details">
                                    <td class="py-0"></td>
                                    <td class="py-0 pl-3" colspan="2">
                                        <a href="{{ route('backend.sales.orders.show', $line->orderLine->order) }}"
                                            class="text-dark font-weight-bold text-decoration-none">{{ $line->orderLine->order->document_number }} <small class="ml-1">{{ $line->orderLine->order->transacted_at_pretty }}</small></a>
                                    </td>
                                    <td class="py-0 text-center">{{ $line->orderLine->quantity_ordered }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        @include('backend::components.document-actions', [
            'route'     => 'backend.sales.in_outs.process',
            'resource'  => $resource,
        ])

    </div>
</div>

@endsection
