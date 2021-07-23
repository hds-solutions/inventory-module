<div class="col-1 d-flex justify-content-center">
    <div class="position-relative d-flex align-items-center h-75px">
        <img src="" class="img-fluid mh-75px" id="line_preview">
    </div>
</div>

<div class="col-9 col-xl-10 d-flex align-items-center">
    <div class="form-row flex-fill">

        <div class="col-8 d-flex align-items-center mb-1">
            <x-form-foreign name="lines[product_id][]" :required="$selected !== null"
                :values="$products" data-live-search="true"
                default="{{ $old['product_id'] ?? $selected?->product_id }}"

                {{-- show="code name" title="code" --}}
                append="url:images.0.url??backend-module/assets/images/default.jpg"
                data-preview="#line_preview" data-preview-init="false"
                data-preview-url-prepend="{{ asset('') }}"

                foreign="products" foreign-add-label="products-catalog::products.add"

                label="inventory::inventory_movement.lines.product_id.0"
                placeholder="inventory::inventory_movement.lines.product_id._"
                {{-- helper="inventory::inventory_movement.lines.product_id.?" --}} />
        </div>

        <div class="col-4 d-flex align-items-center mb-1">
            <x-form-foreign name="lines[variant_id][]" {{-- :required="$selected !== null" --}}
                :values="$products->pluck('variants')->flatten()" data-live-search="true"
                default="{{ $old['variant_id'] ?? $selected?->variant_id }}"

                filtered-by='[name="lines[product_id][]"]' filtered-using="product"
                data-filtered-init="false"

                show="sku" {{-- title="code" --}}
                {{-- append="url:images.0.url??backend-module/assets/images/default.jpg" --}}
                {{-- data-preview="#line_preview" data-preview-init="false" --}}
                {{-- data-preview-url-prepend="{{ asset('') }}" --}}

                foreign="variants" foreign-add-label="products-catalog::variants.add"

                {{-- label="inventory::inventory_movement.lines.variant_id.0" --}}
                placeholder="inventory::inventory_movement.lines.variant_id._"
                {{-- helper="inventory::inventory_movement.lines.variant_id.?" --}} />
        </div>

        <div class="col-4 d-flex align-items-center">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">@lang('inventory::inventory_movement.lines.locator_id.0')</div>
                </div>
                <x-form-foreign name="lines[locator_id][]" {{-- :required="$selected !== null" --}}
                    :values="$branches->pluck('warehouses')->flatten()->pluck('locators')->flatten()" data-live-search="true"
                    default="{{ $old['locator_id'] ?? $selected?->locator_id }}"

                    filtered-by='[name="warehouse_id"]' filtered-using="warehouse"
                    data-filtered-keep-id="true" data-filtered-init="false"

                    show="x : y : z" {{-- title="code" --}}
                    append="warehouse:warehouse_id"
                    {{-- data-preview="#line_preview" data-preview-init="false" --}}
                    {{-- data-preview-url-prepend="{{ asset('') }}" --}}

                    foreign="locators" foreign-add-label="inventory::locators.add"

                    {{-- label="inventory::inventory_movement.lines.locator_id.0" --}}
                    placeholder="inventory::inventory_movement.lines.locator_id._"
                    {{-- helper="inventory::inventory_movement.lines.locator_id.?" --}} />
            </div>
        </div>

        <div class="col-8 d-flex align-items-center">
            <div class="form-row flex-fill">

                <div class="col-4">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text">@lang('inventory::inventory_movement.lines.quantity.0')</label>
                        </div>

                        <x-form-input type="number" name="lines[quantity][]" min="0" :required="$selected !== null"
                            value="{{ $old['quantity'] ?? $selected?->quantity ?? null }}"
                            class="text-center"
                            placeholder="inventory::inventory_movement.lines.quantity.0" />
                    </div>
                </div>

                <div class="col-8">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">@lang('inventory::inventory_movement.lines.to_locator_id.0')</div>
                        </div>

                        <x-form-foreign name="lines[to_locator_id][]" {{-- :required="$selected !== null" --}}
                            :values="$branches->pluck('warehouses')->flatten()->pluck('locators')->flatten()" data-live-search="true"
                            default="{{ $old['locator_id'] ?? $selected?->locator_id }}"

                            filtered-by='[name="to_warehouse_id"]' filtered-using="warehouse"
                            data-filtered-keep-id="true" data-filtered-init="false"

                            show="x : y : z" {{-- title="code" --}}
                            append="warehouse:warehouse_id"
                            {{-- data-preview="#line_preview" data-preview-init="false" --}}
                            {{-- data-preview-url-prepend="{{ asset('') }}" --}}

                            foreign="locators" foreign-add-label="inventory::locators.add"

                            {{-- label="inventory::inventory_movement.lines.locator_id.0" --}}
                            placeholder="inventory::inventory_movement.lines.locator_id._"
                            {{-- helper="inventory::inventory_movement.lines.locator_id.?" --}} />

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<div class="col-2 col-xl-1 d-flex justify-content-end align-items-center">
    <button type="button" class="btn btn-danger"
        data-action="delete"
        @if ($selected !== null)
        data-confirm="Eliminar Linea?"
        data-text="Esta seguro de eliminar la linea con el producto {{ $selected->product->name }}?"
        data-accept="Si, eliminar"
        @endif>X</button>
</div>
