@include('backend::components.errors')

<x-backend-form-text name="document_number" required
    :resource="$resource ?? null" :default="$highs['document_number'] ?? null"

    label="inventory::material_return.document_number.0"
    placeholder="inventory::material_return.document_number._"
    {{-- helper="inventory::material_return.document_number.?" --}} />

<x-backend-form-datetime name="transacted_at" required
    :resource="$resource ?? null" default="{{ now() }}"

    label="inventory::material_return.transacted_at.0"
    placeholder="inventory::material_return.transacted_at._"
    {{-- helper="inventory::material_return.transacted_at.?" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="{{ __('inventory::branches.add') }}"

    label="{{ __('inventory::material_return.branch_id.0') }}"
    placeholder="{{ __('inventory::material_return.branch_id._') }}"
    {{-- helper="{{ __('inventory::material_return.branch_id.?') }}" --}}>

    <x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required secondary
        filtered-by="[name=branch_id]" filtered-using="branch"
        foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="{{ __('inventory::warehouses.add') }}"

        label="{{ __('inventory::material_return.warehouse_id.0') }}"
        placeholder="{{ __('inventory::material_return.warehouse_id._') }}"
        {{-- helper="{{ __('inventory::material_return.warehouse_id.?') }}" --}} />

</x-backend-form-foreign>

<x-backend-form-foreign name="employee_id" required
    :values="$employees" :resource="$resource ?? null" show="full_name"

    foreign="employees" foreign-add-label="sales::employees.add"

    label="inventory::material_return.employee_id.0"
    placeholder="inventory::material_return.employee_id._"
    {{-- helper="inventory::material_return.employee_id.?" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="partnerable_id" required :disabled="isset($resource)"
    show="business_name"
    foreign="customers" :values="$customers" foreign-add-label="{{ __('inventory::customers.add') }}"


    label="{{ __('inventory::material_return.partnerable_id.0') }}"
    placeholder="{{ __('inventory::material_return.partnerable_id._') }}"
    {{-- helper="{{ __('inventory::material_return.branch_id.?') }}" --}} />

{{-- TODO: Customer.addresses --}} {{--
<x-backend-form-foreign name="address_id" required :disabled="isset($resource)"
    :values="$customers->pluck('addresses')->flatten()" :resource="$resource ?? null"

    foreign="addresses" foreign-add-label="sales::addresses.add"
    filtered-by="[name=partnerable_id]" filtered-using="customer"
    append="customer:customer_id"

    label="inventory::material_return.address_id.0"
    placeholder="inventory::material_return.address_id._"
    helper="inventory::material_return.address_id.?" /> --}}

<x-backend-form-foreign name="invoice_id" required :disabled="isset($resource)"
    :values="$customers->pluck('invoices')->flatten()" :resource="$resource ?? null"
    show="{{ __('inventory::material_return.invoice_id.0') }} #document_number transacted_at_pretty"

    filtered-by="[name=partnerable_id]" filtered-using="partner"
    append="partner:partnerable_id"

    {{-- foreign="employees" foreign-add-label="sales::employees.add" --}}

    label="inventory::material_return.invoice_id.0"
    placeholder="inventory::material_return.invoice_id._"
    {{-- helper="inventory::material_return.invoice_id.?" --}} />

@if ($resource ?? false)
<x-backend-form-multiple name="lines" contents-view="inventory::material_returns.form.line"
    data-type="material_return" editable="false"

    :values="$products" values-as="products"
    :extra="$branches" extra-as="branches"
    :selecteds="isset($resource) ? $resource->lines : []" grouped old-filter-fields="product_id,locator_id,quantity_movement"

    contents-size="xxl"
    container-class="my-1"
    card="bg-light"

    label="inventory::material_return.lines.0" />
@endif

<x-backend-form-controls
    submit="inventory::material_returns.save"
    cancel="inventory::material_returns.cancel"
        cancel-route="{{ isset($resource)
            ? 'backend.material_returns.show:'.$resource->id
            : 'backend.material_returns' }}" />
