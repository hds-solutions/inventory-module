import Document from '../../../../../backend-module/resources/assets/js/resources/Document';
import PriceChangeLine from './PriceChangeLine';

export default class PriceChange extends Document {

    _getContainerInstance(container) {
        return new PriceChangeLine(this, container);
    }

}
