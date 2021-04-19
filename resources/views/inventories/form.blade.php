@include('backend::components.errors')

<x-backend-form-foreign :resource="$resource ?? null" name="branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="{{ __('inventory::branches.add') }}"

    label="{{ __('inventory::inventory.branch_id.0') }}"
    placeholder="{{ __('inventory::inventory.branch_id._') }}"
    {{-- helper="{{ __('inventory::inventory.branch_id.?') }}" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required
    filtered-by="[name=branch_id]" filtered-using="branch"
    foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="{{ __('inventory::warehouses.add') }}"

    label="{{ __('inventory::inventory.warehouse_id.0') }}"
    placeholder="{{ __('inventory::inventory.warehouse_id._') }}"
    {{-- helper="{{ __('inventory::product.warehouse_id.?') }}" --}} />

<x-backend-form-text :resource="$resource ?? null" name="description" required
    default="{{ __('inventory::inventory.nav').' @ '.now() }}"
    label="{{ __('inventory::inventory.description.0') }}"
    placeholder="{{ __('inventory::inventory.description._') }}"
    {{-- helper="{{ __('inventory::inventory.description.?') }}" --}} />

<div class="form-row form-group mb-0">
    <label class="col-12 col-md-3 control-label mt-2 mb-3">@lang('inventory::inventory.lines.0')</label>
    <div class="col-9" data-multiple=".inventory-line-container" data-template="#new">
        @php $old = old('lines') ?? []; @endphp
        {{-- add product current lines --}}
        @if (isset($resource)) @foreach($resource->lines as $idx => $selected)
            @include('inventory::inventories.line', [
                'products'  => $products,
                'selected'  => $selected,
                'old'       => $old[$idx] ?? null,
            ])
            @php unset($old[$idx]); @endphp
        @endforeach @endif

        {{-- add new added --}}
        @foreach($old as $selected)
            @include('inventory::inventories.line', [
                'products'  => $products,
                'selected'  => 0,
                'old'       => $selected,
            ])
        @endforeach

        {{-- add empty for adding new lines --}}
        @include('inventory::inventories.line', [
            'products'  => $products,
            'selected'  => null,
            'old'       => null,
        ])
    </div>
</div>

@if (!isset($resource) || $resource->lines->count() == 0)
<div class="form-row form-group align-items-center">
    <div class="col-11 col-md-8 col-lg-6 offset-md-3">

        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="inventory-name">Excel</span>
            </div>
            <div class="custom-file">
                <input type="file" name="inventory" class="custom-file-input" id="inventory-file" aria-describedby="inventory-name">
                <label class="custom-file-label" for="inventory-file" data-show-file-name="true">@lang('inventory::inventory.file._')</label>
            </div>
            <div class="input-group-append">
                <button type="submit"
                    formaction="{{ !isset($resource) ?
                        route('backend.inventories.store', [ 'import' => true ]) :
                        route('backend.inventories.update', [ $resource, 'import' => true ])
                    }}"
                    class="btn btn-primary" id="inventory-label">@lang('inventory::inventories.save-create')</button>
            </div>
        </div>

    </div>
</div>
@endif

<x-backend-form-controls
    submit="inventory::inventories.save"
    cancel="inventory::inventories.cancel" cancel-route="backend.inventories" />
