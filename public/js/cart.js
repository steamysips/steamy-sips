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

/**
 * A function for managing the cart in localStorage.
 * @returns {{getItems: (function(): CartItem[]), removeItem: (function(CartItem): void), isEmpty: (function(): boolean),
 * clear: (function(): void), addItem: (function(CartItem): void)}}
 */
function Cart() {
  /**
   * Adds a new item to shopping cart
   * @param {CartItem} item
   */
  function addItem(item) {
    // get all cart items from localStorage
    const currentCart = getItems();

    // check if there is already an identical item (ignoring quantity) in the cart
    let duplicateFound = false;
    for (let i = 0; i < currentCart.length; i++) {
      const currentItem = currentCart[i];

      if (
        currentItem.productID === item.productID &&
        currentItem.cupSize === item.cupSize &&
        currentItem.milkType === item.milkType
      ) {
        duplicateFound = true;

        // increment quantity
        currentItem.quantity += item.quantity;
      }
    }

    if (!duplicateFound) currentCart.push(item);

    // save final cart back to localStorage
    localStorage.setItem("cart", JSON.stringify(currentCart));
  }

  /**
   * Check if the shopping cart contains no items
   * @returns {boolean} True if cart is empty
   */
  function isEmpty() {
    return getItems().length === 0;
  }

  /**
   * An array of all items in the cart
   * @returns {Array<CartItem>}
   */
  function getItems() {
    return JSON.parse(localStorage.getItem("cart") || "[]");
  }

  /**
   * Checks if 2 cart items are identical. To be identical, they must have
   * the same product ID, quantity, size, and milk.
   * @param {CartItem} item1
   * @param {CartItem} item2
   * @returns {boolean}
   */
  function compareCartItems(item1, item2) {
    return JSON.stringify(item1) === JSON.stringify(item2);
  }

  /**
   * Remove a product and its associated information from the shopping cart
   * @param {CartItem} itemToBeRemoved product ID of product to be removed
   */
  function removeItem(itemToBeRemoved) {
    const currentCart = getItems();

    const newCart = currentCart.filter(
      (item) => !compareCartItems(item, itemToBeRemoved),
    );
    localStorage.setItem("cart", JSON.stringify(newCart));
  }

  /**
   * Empties the shopping cart
   */
  function clear() {
    localStorage.setItem("cart", "[]");
  }

  return { addItem, isEmpty, getItems, removeItem, clear };
}

// function testCart() {
//   const myCart = cart();
//   myCart.clear();
//   console.log("Initial cart = ", myCart.getItems());
//
//   const order1 = CartItem(1, 2, "small", "almond");
//   const order2 = CartItem(2, 2, "large", "almond");
//
//   console.log("Add 2 orders");
//   myCart.addItem(order1);
//   myCart.addItem(order2);
//   console.log("Final cart = ", myCart.getItems());
//
//   console.log("Add a duplicate order");
//   myCart.addItem(CartItem(1, 1, "small", "almond"));
//   console.log("Final cart = ", myCart.getItems());
//
//   console.log("Remove first order");
//   myCart.removeItem(order1);
//   console.log("Final cart = ", myCart.getItems());
//
//   console.log("Remove non-existent order");
//   myCart.removeItem(CartItem(999, 2, "small", "almond"));
//   console.log("Final cart = ", myCart.getItems());
//
//   console.log("Clear cart");
//   myCart.clear();
//   console.log("Cart == empty ?", myCart.isEmpty());
// }

export { Cart, CartItem };
