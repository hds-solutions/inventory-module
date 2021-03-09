@include('backend::components.errors')

<x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required
    foreign="warehouses" :values="$warehouses" foreign-add-label="{{ __('inventory::warehouses.add') }}"

    label="{{ __('inventory::locator.warehouse_id.0') }}"
    placeholder="{{ __('inventory::locator.warehouse_id._') }}"
    {{-- help="{{ __('inventory::locator.warehouse_id.?') }}" --}} />

<div class="form-row form-group align-items-center">
    <label class="col-12 col-md-3 control-label mb-0">@lang('inventory::locator.x.0')</label>
    <div class="col-11 col-md-8 col-lg-6 col-xl-4">
        <div class="form-row">
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
        </div>
    </div>
    {{-- <div class="col-1">
        <i class="fas fa-info-circle ml-2 cursor-help" data-toggle="tooltip" data-placement="right"
            title="@lang('inventory::locator.x.?')"></i>
    </div> --}}
    {{-- <label class="col-12 control-label small">@lang('inventory::locator.x.?')</label> --}}
</div>

<x-backend-form-boolean :resource="$resource ?? null"
    name="default"
    label="{{ __('inventory::locator.default.0') }}"
    placeholder="{{ __('inventory::locator.default._') }}" />

<div class="form-row">
    <div class="offset-0 offset-md-3 col-12 col-md-9">
        <button type="submit" class="btn btn-success">@lang('inventory::locators.save')</button>
        <a href="{{ route('backend.locators') }}" class="btn btn-danger">@lang('inventory::locators.cancel')</a>
    </div>
</div>
