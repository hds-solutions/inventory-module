<div class="col-1 d-flex justify-content-center">
    <div class="position-relative d-flex align-items-center">
        <img src="" class="img-fluid mh-75px" id="line_preview">
    </div>
</div>
<div class="col-9 col-xl-10">

    <div class="form-row">
        <div class="col-4 d-flex align-items-center">
            <x-form-foreign name="lines[product_id][]" :required="$selected !== null"
                :values="$products" data-live-search="true"
                default="{{ $old['product_id'] ?? $selected?->product_id }}"

                {{-- show="code name" title="code" --}}
                append="url:images.0.url??backend-module/assets/images/default.jpg"
                data-preview="#line_preview" data-preview-init="false"
                data-preview-url-prepend="{{ asset('') }}"

                foreign="products" foreign-add-label="products-catalog::products.add"

                label="inventory::price_change.lines.product_id.0"
                placeholder="inventory::price_change.lines.product_id._"
                {{-- helper="inventory::price_change.lines.product_id.?" --}} />
        </div>
        <div class="col-4 d-flex align-items-center">
            <x-form-foreign name="lines[variant_id][]" {{-- :required="$selected !== null" --}}
                :values="$products->pluck('variants')->flatten()" data-live-search="true"
                default="{{ $old['variant_id'] ?? $selected?->variant_id }}"

                filtered-by='[name="lines[product_id][]"]' filtered-using="product"
                data-filtered-init="false"

                show="sku" {{-- title="code" --}}

                foreign="variants" foreign-add-label="products-catalog::variants.add"

                {{-- label="inventory::price_change.lines.variant_id.0" --}}
                placeholder="inventory::price_change.lines.variant_id._"
                {{-- helper="inventory::price_change.lines.variant_id.?" --}} />
        </div>
        <div class="col-4 d-flex align-items-center">
            <x-form-foreign name="lines[currency_id][]" :required="$selected !== null" id="f{{ $id = Str::random(16) }}"
                :values="backend()->currencies()"
                default="{{ $old['currency_id'] ?? $selected?->currency_id }}"

                show="[code] name" title="name"
                append="decimals"

                {{-- label="inventory::price_change.lines.currency_id.0" --}}
                placeholder="inventory::price_change.lines.currency_id._"
                {{-- helper="inventory::price_change.lines.currency_id.?" --}} />
        </div>
    </div>

    <div class="form-row mt-2">
        <div class="col-4 d-flex align-items-center">
            <div class="form-row">
                <div class="col-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text">@lang('inventory::price_change.lines.cost.0')</label>
                        </div>
                        <input name="lines[current_cost][]" type="number" thousand readonly
                            data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                            value="{{ isset($selected) ? number($selected->current_cost, $selected->currency->decimals) : '' }}" @if ($selected !== null) required @endif
                            class="form-control text-right" placeholder="@lang('inventory::price_change.lines.current_cost.0')">
                        <input name="lines[cost][]" type="number" min="0"
                            data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                            value="{{ isset($selected) ? number($selected->cost, $selected->currency->decimals) : '' }}" thousand
                            class="form-control text-right" placeholder="@lang('inventory::price_change.lines.cost.0')">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4 d-flex align-items-center">
            <div class="form-row">
                <div class="col-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text">@lang('inventory::price_change.lines.price.0')</label>
                        </div>
                        <input name="lines[current_price][]" type="number" thousand readonly
                            data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                            value="{{ isset($selected) ? number($selected->current_price, $selected->currency->decimals) : '' }}" @if ($selected !== null) required @endif
                            class="form-control text-right" placeholder="@lang('inventory::price_change.lines.current_price.0')">
                        <input name="lines[price][]" type="number" min="0"
                            data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                            value="{{ isset($selected) ? number($selected->price, $selected->currency->decimals) : '' }}" thousand
                            class="form-control text-right" placeholder="@lang('inventory::price_change.lines.price.0')">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4 d-flex align-items-center">
            <div class="form-row">
                <div class="col-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text">@lang('inventory::price_change.lines.limit.0')</label>
                        </div>
                        <input name="lines[current_limit][]" type="number" thousand readonly
                            data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                            value="{{ isset($selected) ? number($selected->current_limit, $selected->currency->decimals) : '' }}" @if ($selected !== null) required @endif
                            class="form-control text-right" placeholder="@lang('inventory::price_change.lines.current_limit.0')">
                        <input name="lines[limit][]" type="number" min="0"
                            data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                            value="{{ isset($selected) ? number($selected->limit, $selected->currency->decimals) : '' }}" thousand
                            class="form-control text-right" placeholder="@lang('inventory::price_change.lines.limit.0')">
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
