import DocumentLine from '../../../../../backend-module/resources/assets/js/resources/DocumentLine';

export default class InventoryMovementLine extends DocumentLine {

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

            // redirect event to listener
            this.updated(e);
        }));
    }

}
