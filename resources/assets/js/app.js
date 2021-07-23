import Application from '../../../../backend-module/resources/assets/js/resources/Application';
import Inventory from './resources/Inventory';
import PriceChange from './resources/PriceChange';
// import InventoryMovement from './resources/InventoryMovement';

Application.register('inventory', Inventory);
Application.register('price_change', PriceChange);
// Application.register('inventory_movement', InventoryMovement);
