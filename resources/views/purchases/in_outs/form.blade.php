@extends('inventory::in_outs.form')

@section('referable')
    <x-backend-form-foreign name="invoice_id" required readonly
        :values="isset($resource) && $resource?->invoice ? collect([ $resource->invoice ]) : []" :resource="$resource ?? null"
        show="Invoice #document_number" subtext="transacted_at_pretty" data-show-subtext="true"

        {{-- foreign="invoices" foreign-add-label="sales::invoices.add" --}}

        label="inventory::in_out.invoice_id.0"
        placeholder="inventory::in_out.invoice_id._"
        {{-- helper="inventory::in_out.invoice_id.?" --}} />
@endsection

@section('partnerable')
    <x-backend-form-foreign :resource="$resource ?? null" name="partnerable_id" required
        show="business_name"
        foreign="providers" :values="$providers" foreign-add-label="customers::providers.add"

        label="inventory::in_out.provider_id.0"
        placeholder="inventory::in_out.provider_id._"
        {{-- helper="inventory::in_out.provider_id.?" --}} />

    {{-- TODO: Customer.addresses --}} {{--
    <x-backend-form-foreign name="address_id" required
        :values="$providers->pluck('addresses')->flatten()" :resource="$resource ?? null"

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
                ? 'backend.purchases.in_outs.show:'.$resource->id
                : 'backend.purchases.in_outs' }}" />
@endsection
