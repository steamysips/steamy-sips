/**
 * Factory function for cart item objects
 * @param {number} productID Product ID
 * @param {number} quantity Number of times the product with the specific customizations is ordered
 * @param {string} size Size of drink (small, medium, large, ...)
 * @param {string} milk Type of milk (almond, coconut, ...)
 * @returns {{productID, quantity, size, milk}}
 */
const CartItem = (productID, quantity, size, milk) => {
  return { productID, quantity, size, milk };
};

/**
 * A function for managing the cart in localStorage.
 * @returns {{getItems: (function(): CartItem[]), removeItem: (function(CartItem): void), isEmpty: (function(): boolean),
 * clear: (function(): void), setItem: (function(CartItem): void)}}
 */
function cart() {
  /**
   * Adds a new item to shopping cart
   * @param {CartItem} item
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
   * @returns {Array<CartItem>}
   */
  function getItems() {
    return JSON.parse(localStorage.getItem("cart") || "[]");
  }

  /**
   * Checks if 2 cart items are identical. To be identical, they must have
   * the same product ID, quantity, size, and milk.
   * @param item1
   * @param item2
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

  return { setItem, isEmpty, getItems, removeItem, clear };
}

function testCart() {
  const myCart = cart();
  myCart.clear();
  console.log("Initial cart = ", myCart.getItems());

  const order1 = CartItem(1, 2, "small", "almond");
  const order2 = CartItem(2, 2, "small", "almond");

  console.log("Add 2 orders");
  myCart.setItem(order1);
  myCart.setItem(order2);

  console.log("Final cart = ", myCart.getItems());

  console.log("Remove first order");
  myCart.removeItem(order1);
  console.log("Final cart = ", myCart.getItems());

  console.log("Remove non-existent order");
  myCart.removeItem(CartItem(999, 2, "small", "almond"));
  console.log("Final cart = ", myCart.getItems());

  console.log("Clear cart");
  myCart.clear();
  console.log("Cart == empty ?", myCart.isEmpty());
}
