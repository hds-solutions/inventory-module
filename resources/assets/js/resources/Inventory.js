import Document from '../../../../../backend-module/resources/assets/js/resources/Document';
import InventoryLine from './InventoryLine';

export default class Inventory extends Document {

    constructor() {
        super();
        this.warehouse = document.querySelector('[name="warehouse_id"]');
        this._init();
    }

    _getContainerInstance(container) {
        return new InventoryLine(this, container);
    }

    _init() {
        // capture warehouse change and redirect change to every line
        this.warehouse.addEventListener('change', e =>
            // foreach lines and fire change
            this.lines.forEach(line =>
                // fire change on first <select> (product selector)
                Inventory.fire('change', line.container.querySelector('select:first-child'))
            )
        );
    }

}
