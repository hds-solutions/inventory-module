@extends('backend::layouts.master')

@section('page-name', __('inventory::inventories.title'))
@section('description', __('inventory::inventories.description'))

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
                    class="btn btn-sm ml-2 btn-primary">@lang('inventory::inventories.add')</a>
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
                <h2>@lang('inventory::inventory.lines.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-borderless table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="w-150px">@lang('inventory::inventory.lines.image.0')</th>
                                <th>@lang('inventory::inventory.lines.product_id.0')</th>
                                <th>@lang('inventory::inventory.lines.variant_id.0')</th>
                                <th>@lang('inventory::inventory.lines.locator_id.0')</th>
                                <th class="w-150px text-center">@lang('inventory::inventory.lines.current.0')</th>
                                <th class="w-150px text-center">@lang('inventory::inventory.lines.counted.0')</th>
                                <th class="w-150px text-center">@lang('inventory::inventory.lines.expire_at.0')</th>
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
                                                ($line->product->images->first()->url ?? 'assets/images/default.jpg')
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
                                    <td class="align-middle text-center">{{ $line->locator->name ?? '--' }}</td>
                                    <td class="align-middle text-center h4">{{ $line->current ?? 0 }}</td>
                                    <td class="align-middle text-center h4">{{ $line->counted ?? '--' }}</td>
                                    <td class="align-middle text-center">{{ substr($line->expire_at ?? '--', 0, 10) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        @include('backend::components.document-actions', [
            'route'     => 'backend.inventories.process',
            'resource'  => $resource,
        ])

    </div>
</div>

@endsection
