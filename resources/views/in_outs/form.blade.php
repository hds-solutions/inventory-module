@include('backend::components.errors')

<x-backend-form-boolean name="is_purchase"
    :resource="$resource ?? null"

    label="inventory::in_out.is_purchase.0"
    placeholder="inventory::in_out.is_purchase._"
    {{-- helper="inventory::in_out.is_purchase.?" --}} />

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
    foreign="branches" :values="$branches" foreign-add-label="{{ __('inventory::branches.add') }}"

    label="{{ __('inventory::in_out.branch_id.0') }}"
    placeholder="{{ __('inventory::in_out.branch_id._') }}"
    {{-- helper="{{ __('inventory::in_out.branch_id.?') }}" --}}>

    <x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required secondary
        filtered-by="[name=branch_id]" filtered-using="branch"
        foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="{{ __('inventory::warehouses.add') }}"

        label="{{ __('inventory::in_out.warehouse_id.0') }}"
        placeholder="{{ __('inventory::in_out.warehouse_id._') }}"
        {{-- helper="{{ __('inventory::in_out.warehouse_id.?') }}" --}} />

</x-backend-form-foreign>

<x-backend-form-foreign name="order_id" required
    :values="isset($resource) && $resource?->order ? collect([ $resource->order ]) : []" :resource="$resource ?? null"
    show="Order #document_number transacted_at_pretty"

    {{-- foreign="employees" foreign-add-label="sales::employees.add" --}}

    label="inventory::in_out.order_id.0"
    placeholder="inventory::in_out.order_id._"
    {{-- helper="inventory::in_out.order_id.?" --}} />

<x-backend-form-foreign name="invoice_id" required
    :values="isset($resource) && $resource?->invoice ? collect([ $resource->invoice ]) : []" :resource="$resource ?? null"

    {{-- foreign="employees" foreign-add-label="sales::employees.add" --}}

    label="inventory::in_out.invoice_id.0"
    placeholder="inventory::in_out.invoice_id._"
    {{-- helper="inventory::in_out.invoice_id.?" --}} />

<x-backend-form-foreign name="employee_id" required
    :values="$employees" :resource="$resource ?? null" show="full_name"

    foreign="employees" foreign-add-label="sales::employees.add"

    label="inventory::in_out.employee_id.0"
    placeholder="inventory::in_out.employee_id._"
    {{-- helper="inventory::in_out.employee_id.?" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="partnerable_id" required
    show="business_name"
    foreign="customers" :values="$customers" foreign-add-label="{{ __('inventory::customers.add') }}"

    label="{{ __('inventory::in_out.partnerable_id.0') }}"
    placeholder="{{ __('inventory::in_out.partnerable_id._') }}"
    {{-- helper="{{ __('inventory::in_out.branch_id.?') }}" --}} />

{{-- TODO: Customer.addresses --}} {{--
<x-backend-form-foreign name="address_id" required
    :values="$customers->pluck('addresses')->flatten()" :resource="$resource ?? null"

    foreign="addresses" foreign-add-label="sales::addresses.add"
    filtered-by="[name=partnerable_id]" filtered-using="customer"
    append="customer:customer_id"

    label="inventory::in_out.address_id.0"
    placeholder="inventory::in_out.address_id._"
    helper="inventory::in_out.address_id.?" /> --}}

@if ($resource ?? false)
<x-backend-form-multiple name="lines" contents-view="inventory::in_outs.form.line"
    data-type="in_out" editable="false"

    :values="$products" values-as="products"
    :extra="$branches" extra-as="branches"
    :selecteds="isset($resource) ? $resource->lines : []" grouped old-filter-fields="product_id,locator_id,quantity_movement"

    contents-size="xxl"
    container-class="my-1"
    card="bg-light"

    label="inventory::in_out.lines.0" />
@endif

<x-backend-form-controls
    submit="inventory::in_outs.save"
    cancel="inventory::in_outs.cancel"
        cancel-route="{{ isset($resource)
            ? 'backend.in_outs.show:'.$resource->id
            : 'backend.in_outs' }}" />
