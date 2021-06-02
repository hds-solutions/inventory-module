@include('backend::components.errors')

<x-backend-form-text :resource="$resource ?? null" name="description" required
    default="{{ __('inventory::price_change.nav').' @ '.now() }}"
    label="{{ __('inventory::price_change.description.0') }}"
    placeholder="{{ __('inventory::price_change.description._') }}"
    {{-- helper="{{ __('inventory::price_change.description.?') }}" --}} />

<div class="form-row form-group mb-0">
    <label class="col-12 col-md-3 col-lg-2 control-label mt-2 mb-3">@lang('inventory::price_change.lines.0')</label>
    <div class="col-9 col-lg-10" data-multiple=".price_change-line-container" data-template="#new">
        @php $old = old('lines') ?? []; @endphp
        {{-- add product current lines --}}
        @if (isset($resource)) @foreach($resource->lines as $idx => $selected)
            @include('inventory::price_changes.line', [
                'products'  => $products,
                'selected'  => $selected,
                'old'       => $old[$idx] ?? null,
            ])
            @php unset($old[$idx]); @endphp
        @endforeach @endif

        {{-- add new added --}}
        @foreach($old as $selected)
            @include('inventory::price_changes.line', [
                'products'  => $products,
                'selected'  => 0,
                'old'       => $selected,
            ])
        @endforeach

        {{-- add empty for adding new lines --}}
        @include('inventory::price_changes.line', [
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
                <span class="input-group-text" id="price_change-name">Excel</span>
            </div>
            <div class="custom-file">
                <input type="file" name="price_change" class="custom-file-input" id="price_change-file" aria-describedby="price_change-name">
                <label class="custom-file-label" for="price_change-file" data-show-file-name="true">@lang('inventory::price_change.file._')</label>
            </div>
            <div class="input-group-append">
                <button type="submit"
                    formaction="{{ !isset($resource) ?
                        route('backend.price_changes.store', [ 'import' => true ]) :
                        route('backend.price_changes.update', [ $resource, 'import' => true ])
                    }}"
                    class="btn btn-primary" id="price_change-label">@lang('inventory::price_changes.save-create')</button>
            </div>
        </div>

    </div>
</div>
@endif

<x-backend-form-controls
    submit="inventory::price_changes.save"
    cancel="inventory::price_changes.cancel" cancel-route="backend.price_changes" />
