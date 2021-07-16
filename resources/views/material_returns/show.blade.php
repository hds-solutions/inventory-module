@extends('backend::layouts.master')

@section('page-name', __('inventory::material_returns.title'))
@section('description', __('inventory::material_returns.description'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6">
                <i class="fas fa-user-plus"></i>
                @lang('inventory::material_returns.show')
            </div>
            <div class="col-6 d-flex justify-content-end">
                @if (!$resource->isCompleted())
                <a href="{{ route('backend.material_returns.edit', $resource) }}"
                    class="btn btn-sm ml-2 btn-info">@lang('inventory::material_returns.edit')</a>
                @endif
                <a href="{{ route('backend.material_returns.create') }}"
                    class="btn btn-sm ml-2 btn-primary">@lang('inventory::material_returns.create')</a>
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('backend::components.errors')

        <div class="row">
            <div class="col">
                <h2>@lang('inventory::material_return.details.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::material_return.branch_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->branch->name }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::material_return.partnerable_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->partnerable->fullname }}</div>
                </div>

                {{-- <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::material_return.description.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->description }}</div>
                </div> --}}

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::material_return.transacted_at.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ pretty_date($resource->transacted_at, true) }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::material_return.document_status.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ Document::__($resource->document_status) }}</div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col">
                <h2>@lang('inventory::material_return.lines.0')</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-borderless table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="w-150px">@lang('inventory::material_return.lines.image.0')</th>
                                <th>@lang('inventory::material_return.lines.product_id.0')</th>
                                <th>@lang('inventory::material_return.lines.variant_id.0')</th>
                                <th class="w-200px text-center">@lang('inventory::material_return.lines.locator_id.0')</th>
                                <th class="w-250px text-center">@lang('inventory::material_return.lines.quantity_movement.0')</th>
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
                                            class="text-primary text-decoration-none">{{ $line->product->name }}</a>
                                    </td>
                                    <td class="align-middle pl-3">
                                        <div>
                                            @if ($line->variant)
                                            <a href="{{ route('backend.variants.edit', $line->variant) }}"
                                                class="text-primary text-decoration-none">{{ $line->variant->sku }}</a>
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
                                    <td class="align-middle text-center">{{ $line->locator->name }}</td>
                                    <td class="align-middle text-center h4 font-weight-bold">{{ $line->quantity_movement }}</td>
                                </tr>
                                <tr class="d-none"></tr>
                                <tr class="collapse line-{{ $line->id }}-details">
                                    <td class="py-0"></td>
                                    <td class="py-0 pl-3" colspan="3">
                                        <a href="{{ route('backend.invoices.show', $line->invoiceLine->invoice) }}"
                                            class="text-secondary text-decoration-none">{{ $line->invoiceLine->invoice->document_number }}</a> <small class="ml-1">{{ $line->invoiceLine->invoice->transacted_at_pretty }}</small>
                                    </td>
                                    <td class="py-0 text-center">{{ $line->quantity_ordered }}</td>
                                    {{-- <td class="py-0"></td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        @include('backend::components.document-actions', [
            'route'     => 'backend.material_returns.process',
            'resource'  => $resource,
        ])

    </div>
</div>

@endsection
