@extends('inventory::in_outs.form')

@section('referable')
    <x-backend-form-foreign name="order_id" required readonly
        :values="isset($resource) && $resource?->order ? collect([ $resource->order ]) : []" :resource="$resource ?? null"
        show="Order #document_number" subtext="transacted_at_pretty" data-show-subtext="true"

        {{-- foreign="invoices" foreign-add-label="sales::invoices.add" --}}

        label="inventory::in_out.order_id.0"
        placeholder="inventory::in_out.order_id._"
        {{-- helper="inventory::in_out.order_id.?" --}} />
@endsection

@section('partnerable')
    <x-backend-form-foreign :resource="$resource ?? null" name="partnerable_id" required
        show="business_name"
        foreign="customers" :values="$customers" foreign-add-label="customers::customers.add"

        label="inventory::in_out.customer_id.0"
        placeholder="inventory::in_out.customer_id._"
        {{-- helper="inventory::in_out.customer_id.?" --}} />

    {{-- TODO: Customer.addresses --}} {{--
    <x-backend-form-foreign name="address_id" required
        :values="$customers->pluck('addresses')->flatten()" :resource="$resource ?? null"

        foreign="addresses" foreign-add-label="sales::addresses.add"
        filtered-by="[name=partnerable_id]" filtered-using="customer"
        append="customer:customer_id"

        label="inventory::in_out.address_id.0"
        placeholder="inventory::in_out.address_id._"
        helper="inventory::in_out.address_id.?" /> --}}
@endsection

@section('buttons')

    <x-backend-form-controls
        submit="inventory::in_outs.save"
        cancel="inventory::in_outs.cancel"
            cancel-route="{{ isset($resource)
                ? 'backend.sales.in_outs.show:'.$resource->id
                : 'backend.sales.in_outs' }}" />
@endsection
