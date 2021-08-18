import DocumentLine from '../../../../../backend-module/resources/assets/js/resources/DocumentLine';

export default class InventoryLine extends DocumentLine {

    #fields = [];

    constructor(document, container) {
        super(document, container);
        this.#fields.push(...this.container.querySelectorAll('select'));
        this._init();
    }

    _init() {
        // capture change on fields
        this.#fields.forEach(field => field.addEventListener('change', e => {
            // ignore if field doesn't have form (deleted line)
            if (field.form === null) return;

            // if field is <select> fire product/variant change
            if (field.localName.match(/^select/)) this.#loadProduct(field);

            // redirect event to listener
            this.updated(e);
        }));
    }

    #loadProduct(field) {
        // build request data
        let data = { _token: this.document.token }, option;
        data.warehouse = this.document.warehouse.selectedOptions[0].value;
        // check if no warehouse was selected
        if (!data.warehouse) return;

        // load product,variant,currency selected options
        if ((option = this.container.querySelector('[name="lines[product_id][]"]').selectedOptions[0]).value) data.product = option.value;
        if ((option = this.container.querySelector('[name="lines[variant_id][]"]').selectedOptions[0]).value) data.variant = option.value;
        if ((option = this.container.querySelector('[name="lines[locator_id][]"]').selectedOptions[0]).value) data.locator = option.value;
        // ignore if no product
        if (!data.product) return;

        // request current price quantity
        $.ajax({
            method: 'POST',
            url: '/inventories/stock',
            data: data,
            // update current stock for product+variant on locator
            success: data => this.container.querySelector('[name="lines[current][]"]').value = data.stock
        });
    }

}
