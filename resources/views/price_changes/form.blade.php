@include('backend::components.errors')

<x-backend-form-text name="document_number" required
    :resource="$resource ?? null" :default="$highs['document_number'] ?? null"

    label="inventory::price_change.document_number.0"
    placeholder="inventory::price_change.document_number._"
    {{-- helper="inventory::price_change.document_number.?" --}} />

<x-backend-form-text :resource="$resource ?? null" name="description" required
    default="{{ __('inventory::price_change.nav').' @ '.now() }}"
    label="{{ __('inventory::price_change.description.0') }}"
    placeholder="{{ __('inventory::price_change.description._') }}"
    {{-- helper="{{ __('inventory::price_change.description.?') }}" --}} />

<x-backend-form-multiple name="lines" contents-view="inventory::price_changes.form.line"
    data-type="price_change"

    :values="$products" values-as="products"
    :extra="$currencies" extra-as="currencies"
    :selecteds="isset($resource) ? $resource->lines : []" grouped old-filter-fields="product_id,currency_id,price"

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
                        <span class="input-group-text" id="price_change-name">Excel</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" name="price_change" class="custom-file-input" id="price_change-file" aria-describedby="price_change-name">
                        <label class="custom-file-label" for="price_change-file" data-show-file-name="true">@lang('inventory::price_change.file._')</label>
                    </div>
                    <div class="input-group-append">
                        <button type="submit"
                            formaction="{{ !isset($resource) ?
                                route('backend.price_changes.store', [ 'import' => true ]) :
                                route('backend.price_changes.update', [ $resource, 'import' => true ])
                            }}"
                            class="btn btn-primary" id="price_change-label">@lang('inventory::price_changes.save-create')</button>
                    </div>
                </div>

            </div>
        </div>
    </x-slot>
    @endif

</x-backend-form-multiple>

<x-backend-form-controls
    submit="inventory::price_changes.save"
    cancel="inventory::price_changes.cancel" cancel-route="backend.price_changes" />
