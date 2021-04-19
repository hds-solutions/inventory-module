@include('backend::components.errors')

<x-backend-form-foreign :resource="$resource ?? null" name="branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="{{ __('inventory::branches.add') }}"

    label="{{ __('inventory::warehouse.branch_id.0') }}"
    placeholder="{{ __('inventory::warehouse.branch_id._') }}"
    {{-- helper="{{ __('inventory::warehouse.branch_id.?') }}" --}} />

<x-backend-form-text :resource="$resource ?? null" name="name" required
    label="{{ __('inventory::warehouse.name.0') }}"
    placeholder="{{ __('inventory::warehouse.name._') }}"
    {{-- helper="{{ __('inventory::warehouse.name.?') }}" --}} />

<x-backend-form-controls
    submit="inventory::warehouses.save"
    cancel="inventory::warehouses.cancel" cancel-route="backend.warehouses" />
