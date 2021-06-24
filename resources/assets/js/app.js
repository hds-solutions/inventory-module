import Application from '../../../../backend-module/resources/assets/js/resources/Application';
import Inventory from './resources/Inventory';
import PriceChange from './resources/PriceChange';

Application.register('inventory', Inventory);
Application.register('price_change', PriceChange);
