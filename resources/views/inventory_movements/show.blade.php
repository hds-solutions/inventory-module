@extends('backend::layouts.master')

@section('page-name', __('inventory::inventory_movements.title'))
@section('description', __('inventory::inventory_movements.description'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <i class="fas fa-user-plus"></i>
                @lang('inventory::inventory_movements.show')
            </div>
            <div class="col-6 d-flex justify-content-end">
                @if (!$resource->isCompleted())
                <a href="{{ route('backend.inventory_movements.edit', $resource) }}"
                    class="btn btn-sm ml-2 btn-info">@lang('inventory::inventory_movements.edit')</a>
                @endif
                <a href="{{ route('backend.inventory_movements.create') }}"
                    class="btn btn-sm ml-2 btn-primary">@lang('inventory::inventory_movements.create')</a>
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('backend::components.errors')

        <div class="row">
            <div class="col">
                <h2>@lang('inventory::inventory_movement.details.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory_movement.branch_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->warehouse->branch->name }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory_movement.warehouse_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->warehouse->name }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory_movement.description.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->description }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory_movement.created_at.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ pretty_date($resource->created_at, true) }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::inventory_movement.document_status.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ Document::__($resource->document_status) }}</div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col">
                <h2>@lang('inventory::inventory_movement.lines.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-borderless table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="w-150px">@lang('inventory::inventory_movement.lines.image.0')</th>
                                <th class="">@lang('inventory::inventory_movement.lines.product_id.0')</th>
                                <th>@lang('inventory::inventory_movement.lines.variant_id.0')</th>
                                <th class="text-center">@lang('inventory::inventory_movement.lines.locator_id.0')</th>
                                <th class="w-150px text-center">@lang('inventory::inventory_movement.lines.quantity.0')</th>
                                <th class="text-center">@lang('inventory::inventory_movement.lines.to_locator_id.0')</th>
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
                                            ) }}" class="img-fluid mh-50px">
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
                                    <td class="align-middle text-center">{{ $line->locator->name ?? '--' }}</td>
                                    <td class="align-middle text-center h4 font-weight-bold">{{ $line->quantity }}</td>
                                    <td class="align-middle text-center">{{ $line->toLocator->name ?? '--' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        @include('backend::components.document-actions', [
            'route'     => 'backend.inventory_movements.process',
            'resource'  => $resource,
        ])

    </div>
</div>

@endsection
