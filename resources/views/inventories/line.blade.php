<div class="form-row mb-3 inventory-line-container" @if ($selected === null && $old === null) id="new" @else data-used="true" @endif>
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body py-2">

                <div class="form-row">
                    <div class="col-1 d-flex justify-content-center">
                        <div class="position-relative d-flex align-items-center">
                            <img src="" class="img-fluid mh-75px" id="line_preview">
                        </div>
                    </div>
                    <div class="col-9 col-xl-10 d-flex align-items-center">
                        <div class="form-row">

                            <div class="col-8 d-flex align-items-center mb-2">
                                <select name="lines[product_id][]" data-live-search="true"
                                    @if ($selected !== null) required @endif
                                    data-preview="#line_preview" data-preview-init="false"
                                    value="{{ $old['product_id'] ?? $selected?->product_id ?? null }}"
                                    class="form-control selectpicker"
                                    placeholder="@lang('inventory::inventory.lines.product_id._')">

                                    <option value="" selected disabled hidden>@lang('inventory::inventory.lines.product_id.0')</option>

                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" url="{{ asset($product->images->first()->url ?? 'backend-module/assets/images/default.jpg') }}"
                                        @if ($product->id == ($old['product_id'] ?? $selected?->product_id ?? null)) selected @endif>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 d-flex align-items-center mb-2">
                                <select name="lines[variant_id][]"
                                    data-filtered-by='[name="lines[product_id][]"]' data-filtered-using="product" data-filtered-init="false"
                                    value="{{ $old['variant_id'] ?? $selected?->variant_id ?? null }}"
                                    class="form-control selectpicker"
                                    placeholder="@lang('inventory::inventory.lines.variant_id._')">

                                    <option value="" selected disabled hidden>@lang('inventory::inventory.lines.variant_id.0')</option>

                                    @foreach($products->pluck('variants')->flatten() as $variant)
                                    <option value="{{ $variant->id }}" data-product="{{ $variant->product_id }}"
                                        @if ($variant->id == ($old['variant_id'] ?? $selected?->variant_id ?? null)) selected @endif>{{ $variant->sku }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 d-flex align-items-center">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">@lang('inventory::inventory.lines.locator_id.0')</div>
                                    </div>
                                    <select name="lines[locator_id][]" @if ($selected !== null) required @endif
                                        data-filtered-by='[name="warehouse_id"]' data-filtered-using="warehouse" data-filtered-keep-id="true"
                                        value="{{ $old['locator_id'] ?? $selected?->locator_id ?? null }}"
                                        class="form-control selectpicker"
                                        placeholder="@lang('inventory::inventory.lines.locator_id._')">
                                        <option value="" selected disabled hidden>@lang('inventory::inventory.lines.locator_id.0')</option>
                                        @foreach($branches->pluck('warehouses')->flatten()->pluck('locators')->flatten() as $locator)
                                        <option value="{{ $locator->id }}" data-warehouse="{{ $locator->warehouse_id }}"
                                            @if ($locator->id == ($old['locator_id'] ?? $selected?->locator_id ?? null)) selected @endif>{{ $locator->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-8 d-flex align-items-center">
                                <div class="form-row">
                                    <div class="col-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">@lang('inventory::inventory.lines.current.0') / @lang('inventory::inventory.lines.counted.0')</label>
                                            </div>
                                            <input name="lines[current][]" type="number" readonly
                                                value="{{ $old['current'] ?? $selected?->current ?? null }}"
                                                class="form-control" placeholder="@lang('inventory::inventory.lines.current.0')">
                                            <input name="lines[counted][]" type="number" min="0"
                                                value="{{ $old['counted'] ?? $selected?->counted ?? null }}" @if ($selected !== null) required @endif
                                                class="form-control" placeholder="@lang('inventory::inventory.lines.counted.0')">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">@lang('inventory::inventory.lines.expire_at.0')</div>
                                            </div>
                                            <input name="lines[expire_at][]" type="date"
                                                value="{{ substr($old['expire_at'] ?? $selected?->expire_at ?? '', 0, 10) }}"
                                                class="form-control" placeholder="@lang('inventory::inventory.lines.expire_at.0')">
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
