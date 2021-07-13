<div class="col-1 d-flex justify-content-center">
    <div class="position-relative d-flex align-items-center h-50px">
        <img src="" class="img-fluid mh-50px" id="line_preview">
    </div>
</div>

<div class="col-10 col-xl-11 d-flex align-items-center">
    <div class="w-100">
        <div class="form-row">

            <div class="col-3">
                <x-form-foreign name="lines[product_id][]" :required="$selected !== null"
                    :values="$products" data-live-search="true"
                    default="{{ $old['product_id'] ?? $selected?->product_id }}"

                    {{-- show="code name" title="code" --}}
                    append="url:images.0.url??backend-module/assets/images/default.jpg"
                    data-preview="#line_preview" data-preview-init="false"
                    data-preview-url-prepend="{{ asset(null) }}"

                    foreign="products" foreign-add-label="products-catalog::products.add"

                    label="inventory::in_out.lines.product_id.0"
                    placeholder="inventory::in_out.lines.product_id._"
                    {{-- helper="inventory::in_out.lines.product_id.?" --}} />
            </div>

            <div class="col-3">
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

                    {{-- label="inventory::in_out.lines.variant_id.0" --}}
                    placeholder="inventory::in_out.lines.variant_id._"
                    {{-- helper="inventory::in_out.lines.variant_id.?" --}} />
            </div>

            <div class="col-3">
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

                    {{-- label="inventory::in_out.lines.locator_id.0" --}}
                    placeholder="inventory::in_out.lines.locator_id._"
                    {{-- helper="inventory::in_out.lines.locator_id.?" --}} />
            </div>

            <div class="col-3">
                <div class="input-group">
                    <x-form-input type="number" name="lines[quantity_ordered][]" readonly
                        value="{{ $old['quantity_ordered'] ?? $selected?->quantity_ordered ?? null }}"
                        class="text-center"
                        placeholder="inventory::in_out.lines.quantity_ordered.0" />

                    <x-form-input type="number" name="lines[quantity_movement][]" min="0" :required="$selected !== null"
                        value="{{ $old['quantity_movement'] ?? $selected?->quantity_movement ?? null }}"
                        class="text-center"
                        placeholder="inventory::in_out.lines.quantity_movement.0" />
                </div>
            </div>

        </div>
    </div>
</div>
{{--
<div class="col-2 col-xl-1 d-flex justify-content-end align-items-center">
    <button type="button" class="btn btn-danger" tabindex="-1"
        data-action="delete"
        @if ($selected !== null)
        data-confirm="Eliminar Linea?"
        data-text="Esta seguro de eliminar la linea con el producto {{ $selected->product->name }}?"
        data-accept="Si, eliminar"
        @endif>X
    </button>
</div>
 --}}
