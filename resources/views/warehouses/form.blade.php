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

<div class="form-row">
    <div class="offset-0 offset-md-3 col-12 col-md-9">
        <button type="submit" class="btn btn-success">@lang('inventory::warehouses.save')</button>
        <a href="{{ route('backend.warehouses') }}" class="btn btn-danger">@lang('inventory::warehouses.cancel')</a>
    </div>
</div>
