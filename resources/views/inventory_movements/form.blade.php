@include('backend::components.errors')

<x-backend-form-foreign :resource="$resource ?? null" name="branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="{{ __('inventory::branches.add') }}"

    label="{{ __('inventory::inventory_movement.branch_id.0') }}"
    placeholder="{{ __('inventory::inventory_movement.branch_id._') }}"
    {{-- helper="{{ __('inventory::inventory_movement.branch_id.?') }}" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required
    filtered-by="[name=branch_id]" filtered-using="branch"
    foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="{{ __('inventory::warehouses.add') }}"

    label="{{ __('inventory::inventory_movement.warehouse_id.0') }}"
    placeholder="{{ __('inventory::inventory_movement.warehouse_id._') }}"
    {{-- helper="{{ __('inventory::product.warehouse_id.?') }}" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="to_branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="{{ __('inventory::branches.add') }}"

    label="{{ __('inventory::inventory_movement.to_branch_id.0') }}"
    placeholder="{{ __('inventory::inventory_movement.to_branch_id._') }}"
    {{-- helper="{{ __('inventory::inventory_movement.to_branch_id.?') }}" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="to_warehouse_id" required
    filtered-by="[name=to_branch_id]" filtered-using="branch"
    foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="{{ __('inventory::warehouses.add') }}"

    label="{{ __('inventory::inventory_movement.to_warehouse_id.0') }}"
    placeholder="{{ __('inventory::inventory_movement.to_warehouse_id._') }}"
    {{-- helper="{{ __('inventory::product.to_warehouse_id.?') }}" --}} />

<x-backend-form-text :resource="$resource ?? null" name="description" required
    default="{{ __('inventory::inventory_movement.nav').' @ '.now() }}"
    label="{{ __('inventory::inventory_movement.description.0') }}"
    placeholder="{{ __('inventory::inventory_movement.description._') }}"
    {{-- helper="{{ __('inventory::inventory_movement.description.?') }}" --}} />

<div class="form-row form-group mb-0">
    <label class="col-12 col-md-3 col-lg-2 control-label mt-2 mb-3">@lang('inventory::inventory_movement.lines.0')</label>
    <div class="col-9 col-lg-10" data-multiple=".inventory-movement-line-container" data-template="#new">
        @php $old = old('lines') ?? []; @endphp
        {{-- add product current lines --}}
        @if (isset($resource)) @foreach($resource->lines as $idx => $selected)
            @include('inventory::inventory_movements.line', [
                'products'  => $products,
                'selected'  => $selected,
                'old'       => $old[$idx] ?? null,
            ])
            @php unset($old[$idx]); @endphp
        @endforeach @endif

        {{-- add new added --}}
        @foreach($old as $selected)
            @include('inventory::inventory_movements.line', [
                'products'  => $products,
                'selected'  => 0,
                'old'       => $selected,
            ])
        @endforeach

        {{-- add empty for adding new lines --}}
        @include('inventory::inventory_movements.line', [
            'products'  => $products,
            'selected'  => null,
            'old'       => null,
        ])
    </div>
</div>

<x-backend-form-controls
    submit="inventory::inventory_movements.save"
    cancel="inventory::inventory_movements.cancel" cancel-route="backend.inventory_movements" />
