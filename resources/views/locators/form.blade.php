@include('backend::components.errors')

<x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required
    foreign="warehouses" :values="$warehouses" foreign-add-label="inventory::warehouses.add"
    data-live-search="true"

    label="inventory::locator.warehouse_id.0"
    placeholder="inventory::locator.warehouse_id._"
    {{-- helper="inventory::locator.warehouse_id.?" --}} />


<x-form-row>
    <x-form-label text="inventory::locator.x.0" form-label/>

    <x-form-row-group>

        <div class="col">
            <input name="x" type="text" required
                value="{{ isset($resource) && !old('x') ? $resource->x : old('x') }}"
                class="form-control {{ $errors->has('x') ? 'is-danger' : '' }}"
                placeholder="@lang('inventory::locator.x._')">
        </div>

        <div class="col">
            <input name="y" type="text" required
                value="{{ isset($resource) && !old('y') ? $resource->y : old('y') }}"
                class="form-control {{ $errors->has('y') ? 'is-danger' : '' }}"
                placeholder="@lang('inventory::locator.y._')">
        </div>

        <div class="col">
            <input name="z" type="text" required
                value="{{ isset($resource) && !old('z') ? $resource->z : old('z') }}"
                class="form-control {{ $errors->has('z') ? 'is-danger' : '' }}"
                placeholder="@lang('inventory::locator.z._')">
        </div>

    </x-form-row-group>

</x-form-row>

<x-backend-form-boolean :resource="$resource ?? null"
    name="default"
    label="inventory::locator.default.0"
    placeholder="inventory::locator.default._" />

<x-backend-form-controls
    submit="inventory::locators.save"
    cancel="inventory::locators.cancel" cancel-route="backend.locators" />
