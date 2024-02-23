function myCart() {
  function setItem(productID, size) {
    // get all cart contents from localStorage
    const currentCart = getItems();

    // check if product ID already exists in array
    // and make changes

    // add new product id to array
    currentCart.push(productID);

    // save final cart back to localStorage
    localStorage.setItem("cart", JSON.stringify(currentCart));
  }

  function isEmpty() {
    return getItems().length === 0;
  }

  function getItems() {
    return JSON.parse(localStorage.getItem("cart") || "[]");
  }

  function removeProduct(productID) {
    const currentCart = getItems();
    const newCart = currentCart.filter((id) => id !== productID);
    localStorage.setItem("cart", JSON.stringify(newCart));
  }

  function clear() {
    localStorage.setItem("cart", "[]");
  }
}
