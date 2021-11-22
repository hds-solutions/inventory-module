@include('backend::components.errors')

<x-backend-form-foreign :resource="$resource ?? null" name="branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="backend::branches.add"

    label="inventory::inventory_movement.warehouse_id.0"
    placeholder="inventory::inventory_movement.branch_id._"
    {{-- helper="inventory::inventory_movement.branch_id.?" --}}>

    <x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required secondary
        filtered-by="[name=branch_id]" filtered-using="branch"
        foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="inventory::warehouses.add"

        label="inventory::inventory_movement.warehouse_id.0"
        placeholder="inventory::inventory_movement.warehouse_id._"
        {{-- helper="inventory::product.warehouse_id.?" --}} />

</x-backend-form-foreign>


<x-backend-form-foreign :resource="$resource ?? null" name="to_branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="backend::branches.add"

    label="inventory::inventory_movement.to_branch_id.0"
    placeholder="inventory::inventory_movement.to_branch_id._"
    {{-- helper="inventory::inventory_movement.to_branch_id.?" --}}>

    <x-backend-form-foreign :resource="$resource ?? null" name="to_warehouse_id" required secondary
        filtered-by="[name=to_branch_id]" filtered-using="branch"
        foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="inventory::warehouses.add"

        label="inventory::inventory_movement.to_warehouse_id.0"
        placeholder="inventory::inventory_movement.to_warehouse_id._"
        {{-- helper="inventory::product.to_warehouse_id.?" --}} />

</x-backend-form-foreign>

<x-backend-form-text :resource="$resource ?? null" name="description" required
    default="{{ __('inventory::inventory_movement.nav').' @ '.now() }}"
    label="inventory::inventory_movement.description.0"
    placeholder="inventory::inventory_movement.description._"
    {{-- helper="inventory::inventory_movement.description.?" --}} />

<x-backend-form-multiple name="lines" contents-view="inventory::inventory_movements.form.line"
    data-type="inventory_movement"

    :values="$products" values-as="products"
    :extra="$branches" extra-as="branches"
    :selecteds="isset($resource) ? $resource->lines : []" grouped old-filter-fields="product_id,locator_id,quantity"

    contents-size="xxl"
    container-class="my-3"
    card="bg-light"

    label="inventory::inventory_movement.lines.0" />

<x-backend-form-controls
    submit="inventory::inventory_movements.save"
    cancel="inventory::inventory_movements.cancel" cancel-route="backend.inventory_movements" />
