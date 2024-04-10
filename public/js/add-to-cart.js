import { cart, CartItem } from "./cart";
import ModalManager from "./modal";

const modal = ModalManager("my-modal");

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

  // open modal to display success
  modal.openModal();
}

window.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("product-customization-form")
    .addEventListener("submit", handleAddToCart);
});

modal.init();
