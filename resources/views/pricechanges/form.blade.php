@include('backend::components.errors')

<x-backend-form-text :resource="$resource ?? null" name="description" required
    default="{{ __('inventory::pricechange.nav').' @ '.now() }}"
    label="{{ __('inventory::pricechange.description.0') }}"
    placeholder="{{ __('inventory::pricechange.description._') }}"
    {{-- helper="{{ __('inventory::pricechange.description.?') }}" --}} />

<div class="form-row form-group mb-0">
    <label class="col-12 col-md-3 col-lg-2 control-label mt-2 mb-3">@lang('inventory::pricechange.lines.0')</label>
    <div class="col-9 col-lg-10" data-multiple=".pricechange-line-container" data-template="#new">
        @php $old = old('lines') ?? []; @endphp
        {{-- add product current lines --}}
        @if (isset($resource)) @foreach($resource->lines as $idx => $selected)
            @include('inventory::pricechanges.line', [
                'products'  => $products,
                'selected'  => $selected,
                'old'       => $old[$idx] ?? null,
            ])
            @php unset($old[$idx]); @endphp
        @endforeach @endif

        {{-- add new added --}}
        @foreach($old as $selected)
            @include('inventory::pricechanges.line', [
                'products'  => $products,
                'selected'  => 0,
                'old'       => $selected,
            ])
        @endforeach

        {{-- add empty for adding new lines --}}
        @include('inventory::pricechanges.line', [
            'products'  => $products,
            'selected'  => null,
            'old'       => null,
        ])
    </div>
</div>

@if (!isset($resource) || $resource->lines->count() == 0)
<div class="form-row form-group align-items-center">
    <div class="col-11 col-md-8 col-lg-6 offset-md-3 offset-lg-2">

        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="pricechange-name">Excel</span>
            </div>
            <div class="custom-file">
                <input type="file" name="pricechange" class="custom-file-input" id="pricechange-file" aria-describedby="pricechange-name">
                <label class="custom-file-label" for="pricechange-file" data-show-file-name="true">@lang('inventory::pricechange.file._')</label>
            </div>
            <div class="input-group-append">
                <button type="submit"
                    formaction="{{ !isset($resource) ?
                        route('backend.pricechanges.store', [ 'import' => true ]) :
                        route('backend.pricechanges.update', [ $resource, 'import' => true ])
                    }}"
                    class="btn btn-primary" id="pricechange-label">@lang('inventory::pricechange.save-create')</button>
            </div>
        </div>

    </div>
</div>
@endif

<x-backend-form-controls
    submit="inventory::pricechanges.save"
    cancel="inventory::pricechanges.cancel" cancel-route="backend.pricechanges" />
