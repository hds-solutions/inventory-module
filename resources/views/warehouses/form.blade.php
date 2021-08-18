@include('backend::components.errors')

<x-backend-form-foreign :resource="$resource ?? null" name="branch_id" required
    :values="$branches" request="branch" :readonly="request()->has('branch')"

    foreign="branches" foreign-add-label="inventory::branches.add"

    label="inventory::warehouse.branch_id.0"
    placeholder="inventory::warehouse.branch_id._"
    {{-- helper="inventory::warehouse.branch_id.?" --}} />

<x-backend-form-text :resource="$resource ?? null" name="name" required
    label="inventory::warehouse.name.0"
    placeholder="inventory::warehouse.name._"
    {{-- helper="inventory::warehouse.name.?" --}} />

<x-backend-form-controls
    submit="inventory::warehouses.save"
    cancel="inventory::warehouses.cancel" cancel-route="backend.warehouses" />
