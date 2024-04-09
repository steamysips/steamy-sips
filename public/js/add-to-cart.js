import { cart, CartItem } from "./cart";

function handleAddToCart(e) {
  // capture form submission
  e.preventDefault();

  // extract form data
  const formData = new FormData(e.target);
  const formProps = Object.fromEntries(formData);

  const item = CartItem(
    parseInt(formProps.product_id, 10),
    parseInt(formProps.quantity, 10),
    formProps.cupSize,
    formProps.milkType,
  );
  cart().addItem(item);
  console.log("Added new item to cart!");
}

window.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("product-customization-form")
    .addEventListener("submit", handleAddToCart);
});