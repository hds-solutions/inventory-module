<div class="form-row mb-3 in_out-line-container" @if ($selected === null) id="new" @else data-used="true" @endif>
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body py-2">
                <div class="form-row">
                    <div class="col-1 d-flex justify-content-center">
                        <div class="position-relative d-flex align-items-center">
                            <img src="" class="img-fluid mh-50px" id="line_preview">
                        </div>
                    </div>
                    <div class="col-9 col-xl-10 d-flex align-items-center">
                        <div class="form-row">
                            <div class="col-4">
                                <select name="lines[product_id][]" data-live-search="true"
                                    @if ($selected !== null) required @endif
                                    data-preview="#line_preview" data-preview-init="false"
                                    value="{{ isset($selected) && !old('product_id') ? $selected->product_id : old('product_id') }}"
                                    class="form-control selectpicker {{ $errors->has('product_id') ? 'is-danger' : '' }}"
                                    placeholder="@lang('inventory::in_out.lines.product_id._')">

                                    <option value="" selected disabled
                                            hidden>@lang('inventory::in_out.lines.product_id.0')</option>

                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            url="{{ asset($product->images->first()->url ?? 'backend-module/assets/images/default.jpg') }}"
                                            @if (isset($selected) && !old('product_id') && $selected->product_id == $product->id ||
                                                old('product_id') == $product->id) selected @endif>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="lines[variant_id][]"
                                    data-filtered-by='[name="lines[product_id][]"]' data-filtered-using="product"
                                    data-filtered-init="false"
                                    value="{{ isset($selected) && !old('variant_id') ? $selected->variant_id : old('variant_id') }}"
                                    class="form-control selectpicker {{ $errors->has('variant_id') ? 'is-danger' : '' }}"
                                    placeholder="@lang('inventory::in_out.lines.variant_id._')">

                                    <option value="" selected disabled
                                            hidden>@lang('inventory::in_out.lines.variant_id.0')</option>

                                    @foreach($products->pluck('variants')->flatten() as $variant)
                                        <option value="{{ $variant->id }}" data-product="{{ $variant->product_id }}"
                                                @if (isset($selected) && !old('variant_id') && $selected->variant_id == $variant->id ||
                                                    old('variant_id') == $variant->id) selected @endif>{{ $variant->sku }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input name="lines[quantity_movement][]" type="number" min="1"
                                       value="{{ $selected->quantity_movement ?? '' }}"
                                       class="form-control text-center"
                                       placeholder="@lang('inventory::in_out.lines.quantity_movement.0')">
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
                            @endif>X
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
