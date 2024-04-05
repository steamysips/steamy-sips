/**
 * Factory function for cart item objects
 * @param {number} productID Product ID
 * @param {number} quantity Number of times the product with the specific customizations is ordered
 * @param {number} size Size of drink (small, medium, large, ...)
 * @param {string} milk Type of milk (almond, coconut, ...)
 * @returns {{size, milk, id}}
 */
const cartItem = (productID, quantity, size, milk) => {
  return { productID, quantity, size, milk };
};

/**
 * A function for managing the cart in localStorage.
 * @returns {{getItems: (function(): any), removeItem: removeItem, isEmpty: (function(): boolean), clear: clear, setItem: setItem}}
 */
function cart() {
  /**
   * Adds a new item to shopping cart
   * @param {cartItem} item
   */
  function setItem(item) {
    // get all cart items from localStorage
    const currentCart = getItems();

    // check if product ID already exists in array
    // and make changes

    // add new product id to array
    currentCart.push(item);

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
   * @returns {Array<cartItem>}
   */
  function getItems() {
    // TODO: Convert each array item to a cartItem object
    return JSON.parse(localStorage.getItem("cart") || "[]");
  }

  /**
   * Remove a product and its associated information from the shopping cart
   * @param {int} itemToBeRemoved product ID of product to be removed
   */
  function removeItem(itemToBeRemoved) {
    const currentCart = getItems();

    // TODO: Allow several cart items of the same product but different customizations
    const newCart = currentCart.filter(
      (item) => itemToBeRemoved.productID() !== productID,
    );
    localStorage.setItem("cart", JSON.stringify(newCart));
  }

  /**
   * Empties the shopping cart
   */
  function clear() {
    localStorage.setItem("cart", "[]");
  }

  return { setItem, isEmpty, getItems, removeItem, clear };
}
