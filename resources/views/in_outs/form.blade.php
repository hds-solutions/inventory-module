@include('backend::components.errors')

<x-backend-form-text name="document_number" required
    :resource="$resource ?? null" :default="$highs['document_number'] ?? null"

    label="inventory::in_out.document_number.0"
    placeholder="inventory::in_out.document_number._"
    {{-- helper="inventory::in_out.document_number.?" --}} />

<x-backend-form-datetime name="transacted_at" required
    :resource="$resource ?? null" default="{{ now() }}"

    label="inventory::in_out.transacted_at.0"
    placeholder="inventory::in_out.transacted_at._"
    {{-- helper="inventory::in_out.transacted_at.?" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="inventory::branches.add"

    label="inventory::in_out.branch_id.0"
    placeholder="inventory::in_out.branch_id._"
    {{-- helper="inventory::in_out.branch_id.?" --}}>

    <x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required secondary
        filtered-by="[name=branch_id]" filtered-using="branch"
        foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="inventory::warehouses.add"

        label="inventory::in_out.warehouse_id.0"
        placeholder="inventory::in_out.warehouse_id._"
        {{-- helper="inventory::in_out.warehouse_id.?" --}} />

</x-backend-form-foreign>

@yield('referable')

<x-backend-form-foreign name="employee_id" required
    :values="$employees" :resource="$resource ?? null" show="full_name"

    foreign="employees" foreign-add-label="sales::employees.add"

    label="inventory::in_out.employee_id.0"
    placeholder="inventory::in_out.employee_id._"
    {{-- helper="inventory::in_out.employee_id.?" --}} />

@yield('partnerable')

<x-backend-form-multiple name="lines" contents-view="inventory::in_outs.form.line"
    data-type="in_out" editable="false"

    :values="$products" values-as="products"
    :extra="$branches" extra-as="branches"
    :selecteds="isset($resource) ? $resource->lines : []" grouped old-filter-fields="product_id,locator_id,quantity_movement"

    contents-size="xxl"
    container-class="my-1"
    card="bg-light"

    label="inventory::in_out.lines.0" />

@yield('buttons')
