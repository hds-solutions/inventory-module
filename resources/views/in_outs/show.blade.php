@extends('backend::layouts.master')

@section('page-name', __('inventory::in_outs.title'))
@section('description', __('inventory::in_outs.description'))

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col-6 d-flex align-items-center">
                <i class="fas fa-user-plus mr-2"></i>
                @lang('inventory::in_outs.show')
            </div>
            <div class="col-6 d-flex justify-content-end">
                @if (!$resource->isCompleted())
                <a href="{{ route('backend.in_outs.edit', $resource) }}"
                    class="btn btn-sm ml-2 btn-outline-info">@lang('inventory::in_outs.edit')</a>
                @endif
                {{-- <a href="{{ route('backend.in_outs.create') }}"
                    class="btn btn-sm ml-2 btn-primary">@lang('inventory::in_outs.create')</a> --}}
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
            <div class="col-12">

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.branch_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->branch->name }}</div>
                </div>

                <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.partnerable_id.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->partnerable->fullname }}</div>
                </div>

                {{-- <div class="row">
                    <div class="col-4 col-lg-4">@lang('inventory::in_out.description.0'):</div>
                    <div class="col-8 col-lg-6 h4">{{ $resource->description }}</div>
                </div> --}}

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
                                    <td class="align-middle text-center h4 font-weight-bold">{{ $line->quantity_movement }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        @include('backend::components.document-actions', [
            'route'     => 'backend.in_outs.process',
            'resource'  => $resource,
        ])

    </div>
</div>

@endsection
