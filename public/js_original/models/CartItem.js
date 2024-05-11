/**
 * A cart item object.
 * @typedef {{productID, quantity, cupSize, milkType}} CartItem
 */

/**
 * Factory function for cart item objects
 * @param {number} productID Product ID
 * @param {number} quantity Number of times the product with the specific customizations is ordered
 * @param {string} cupSize Size of drink (small, medium, large, ...)
 * @param {string} milkType Type of milk (almond, coconut, ...)
 * @returns {{productID, quantity, cupSize, milkType}}
 */
const CartItem = (productID, quantity, cupSize, milkType) => {
  return { productID, quantity, cupSize, milkType };
};

export default CartItem;
