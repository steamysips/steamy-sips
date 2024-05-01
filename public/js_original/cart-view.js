/**
 * This script is executed on /cart page. It allows users to modify their cart in real-time and view the updated totals.
 */

import Cart from "./models/Cart";
import CartItem from "./models/CartItem";

function updateCart(e) {
  const sectionNode = e.target.parentNode.parentNode;

  // get cart item original attributes (before update)
  const currentCartItem = CartItem(
    parseInt(sectionNode.getAttribute("data-productid"), 10),
    parseInt(sectionNode.getAttribute("data-quantity"), 10),
    sectionNode.getAttribute("data-cupsize"),
    sectionNode.getAttribute("data-milktype"),
  );

  // console.log("Old item", currentCartItem);

  // calculate new subtotal
  const newQuantity = parseInt(e.target.value, 10);
  const unitPrice = parseFloat(sectionNode.getAttribute("data-unitprice"));
  const newSubTotal = Math.round(newQuantity * unitPrice * 100) / 100;

  // display new subtotal
  const priceNode = sectionNode.querySelector(".container > strong");
  priceNode.textContent = "Rs " + newSubTotal;

  // update quantity on actual node
  sectionNode.setAttribute("data-quantity", newQuantity);

  // update localstorage
  const currentCart = Cart();
  currentCart.removeItem(currentCartItem);

  // a quantity of 0 means to remove the item from cart
  if (newQuantity > 0) {
    currentCartItem.quantity = newQuantity;
    currentCart.addItem(currentCartItem);
  }
}

window.addEventListener("DOMContentLoaded", function () {
  const quantityInputs = [
    ...document.querySelectorAll("section input[type='number']"),
  ];

  quantityInputs.forEach((input) => {
    input.addEventListener("change", updateCart);
  });
});
