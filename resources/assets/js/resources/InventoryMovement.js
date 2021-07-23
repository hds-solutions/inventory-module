import Document from '../../../../../backend-module/resources/assets/js/resources/Document';
import InventoryMovementLine from './InventoryMovementLine';

export default class InventoryMovement extends Document {

    constructor() {
        super();
        this.warehouse = document.querySelector('[name="warehouse_id"]');
        this.to_warehouse = document.querySelector('[name="to_warehouse_id"]');
        this._init();
    }

    _getContainerInstance(container) {
        return new InventoryMovementLine(this, container);
    }

    _init() {
        // capture warehouse change and redirect change to every line
        this.warehouse.addEventListener('change', e =>
            // foreach lines and fire change
            this.lines.forEach(line =>
                // fire change on first <select> (product selector)
                this.fire('change', line.container.querySelector('select:first-child'))
            )
        );
        // capture warehouse change and redirect change to every line
        this.to_warehouse.addEventListener('change', e =>
            // foreach lines and fire change
            this.lines.forEach(line =>
                // fire change on first <select> (product selector)
                this.fire('change', line.container.querySelector('select:first-child'))
            )
        );
    }

}
