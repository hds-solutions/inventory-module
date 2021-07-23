@include('backend::components.errors')

<x-backend-form-text name="document_number" required
    :resource="$resource ?? null" :default="$highs['document_number'] ?? null"

    label="inventory::inventory.document_number.0"
    placeholder="inventory::inventory.document_number._"
    {{-- helper="inventory::inventory.document_number.?" --}} />

<x-backend-form-text :resource="$resource ?? null" name="description" required
    default="{{ __('inventory::inventory.nav').' @ '.now() }}"
    label="inventory::inventory.description.0"
    placeholder="inventory::inventory.description._"
    {{-- helper="inventory::inventory.description.?" --}} />

<x-backend-form-foreign :resource="$resource ?? null" name="branch_id" required
    foreign="branches" :values="$branches" foreign-add-label="inventory::branches.add"

    label="inventory::inventory.branch_id.0"
    placeholder="inventory::inventory.branch_id._"
    {{-- helper="inventory::inventory.branch_id.?" --}}>

    <x-backend-form-foreign :resource="$resource ?? null" name="warehouse_id" required secondary
        filtered-by="[name=branch_id]" filtered-using="branch"
        foreign="warehouses" :values="$branches->pluck('warehouses')->flatten()" foreign-add-label="inventory::warehouses.add"

        label="inventory::inventory.warehouse_id.0"
        placeholder="inventory::inventory.warehouse_id._"
        {{-- helper="inventory::product.warehouse_id.?" --}} />

</x-backend-form-foreign>

<x-backend-form-multiple name="lines" contents-view="inventory::inventories.form.line"
    data-type="inventory"

    :values="$products" values-as="products"
    :extra="$branches" extra-as="branches"
    :selecteds="isset($resource) ? $resource->lines : []" grouped old-filter-fields="product_id,locator_id"

    contents-size="xxl"
    container-class="my-3"
    card="bg-light"

    label="inventory::inventory.lines.0">

    @if (!isset($resource) || $resource->lines->count() == 0)
    <x-slot name="card-footer">
        <div class="form-row form-group align-items-center">
            <div class="col-11 col-md-8 col-lg-6 offset-md-5">

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inventory-name">Excel</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" name="inventory" class="custom-file-input" id="inventory-file" aria-describedby="inventory-name">
                        <label class="custom-file-label" for="inventory-file" data-show-file-name="true">@lang('inventory::inventory.file._')</label>
                    </div>
                    <div class="input-group-append">
                        <button type="submit"
                            formaction="{{ !isset($resource) ?
                                route('backend.inventories.store', [ 'import' => true ]) :
                                route('backend.inventories.update', [ $resource, 'import' => true ])
                            }}"
                            class="btn btn-outline-gray btn-hover-success" id="inventory-label">@lang('inventory::inventories.save-create')</button>
                    </div>
                </div>

            </div>
        </div>
    </x-slot>
    @endif

</x-backend-form-multiple>

<x-backend-form-controls
    submit="inventory::inventories.save"
    cancel="inventory::inventories.cancel" cancel-route="backend.inventories" />
