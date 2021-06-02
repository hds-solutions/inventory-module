<div class="form-row mb-3 price_change-line-container" @if ($selected === null) id="new" @else data-used="true" @endif>
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body py-2">

                <div class="form-row">
                    <div class="col-1 d-flex justify-content-center">
                        <div class="position-relative d-flex align-items-center">
                            <img src="" class="img-fluid mh-75px" id="line_preview">
                        </div>
                    </div>
                    <div class="col-9 col-xl-10">

                        <div class="form-row">
                            <div class="col-4 d-flex align-items-center">
                                <select name="lines[product_id][]" data-live-search="true" @if ($selected !== null) required @endif
                                    data-preview="#line_preview" data-preview-init="false"
                                    value="{{ isset($selected) && !old('product_id') ? $selected->product_id : old('product_id') }}"
                                    class="form-control selectpicker {{ $errors->has('product_id') ? 'is-danger' : '' }}"
                                    placeholder="@lang('inventory::price_change.lines.product_id._')">
                                    <option value="" selected disabled hidden>@lang('inventory::price_change.lines.product_id.0')</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" url="{{ asset($product->images->first()->url ?? 'backend-module/assets/images/default.jpg') }}"
                                        @if (isset($selected) && !old('product_id') && $selected->product_id == $product->id ||
                                            old('product_id') == $product->id) selected @endif>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <select name="lines[variant_id][]" data-live-search="true"
                                    data-filtered-by='[name="lines[product_id][]"]' data-filtered-using="product" data-filtered-init="false"
                                    value="{{ isset($selected) && !old('variant_id') ? $selected->variant_id : old('variant_id') }}"
                                    class="form-control selectpicker {{ $errors->has('variant_id') ? 'is-danger' : '' }}"
                                    placeholder="@lang('inventory::price_change.lines.variant_id._')">
                                    <option value="" selected disabled hidden>@lang('inventory::price_change.lines.variant_id.0')</option>
                                    @foreach($products->pluck('variants')->flatten() as $variant)
                                    <option value="{{ $variant->id }}" data-product="{{ $variant->product_id }}"
                                        @if (isset($selected) && !old('variant_id') && $selected->variant_id == $variant->id ||
                                            old('variant_id') == $variant->id) selected @endif>{{ $variant->sku }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <select name="lines[currency_id][]" data-live-search="true" @if (isset($selected)) id="f{{ $id = Str::random(16) }}" @endif
                                    value="{{ isset($selected) && !old('currency_id') ? $selected->currency_id : old('currency_id') }}"
                                    class="form-control selectpicker {{ $errors->has('currency_id') ? 'is-danger' : '' }}"
                                    placeholder="@lang('inventory::price_change.lines.currency_id._')">
                                    <option value="" selected disabled hidden>@lang('inventory::price_change.lines.currency_id.0')</option>
                                    @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" title="{{ $currency->name }}"
                                        data-decimals="{{ $currency->decimals }}"
                                        @if (isset($selected) && !old('currency_id') && $selected->currency_id == $currency->id ||
                                            old('currency_id') == $currency->id) selected @endif>[{{ $currency->code }}] {{ $currency->name }}</option>
                                    @endforeach
                                </select>
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
                                                class="form-control" placeholder="@lang('inventory::price_change.lines.current_cost.0')">
                                            <input name="lines[cost][]" type="number" min="0"
                                                data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                                                value="{{ isset($selected) ? number($selected->cost, $selected->currency->decimals) : '' }}" thousand
                                                class="form-control" placeholder="@lang('inventory::price_change.lines.cost.0')">
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
                                                class="form-control" placeholder="@lang('inventory::price_change.lines.current_price.0')">
                                            <input name="lines[price][]" type="number" min="0"
                                                data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                                                value="{{ isset($selected) ? number($selected->price, $selected->currency->decimals) : '' }}" thousand
                                                class="form-control" placeholder="@lang('inventory::price_change.lines.price.0')">
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
                                                class="form-control" placeholder="@lang('inventory::price_change.lines.current_limit.0')">
                                            <input name="lines[limit][]" type="number" min="0"
                                                data-currency-by="{{ isset($selected) ? "#f$id" : '[name="lines[currency_id][]"]' }}"
                                                value="{{ isset($selected) ? number($selected->limit, $selected->currency->decimals) : '' }}" thousand
                                                class="form-control" placeholder="@lang('inventory::price_change.lines.limit.0')">
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
                </div>

            </div>
        </div>
    </div>
</div>
