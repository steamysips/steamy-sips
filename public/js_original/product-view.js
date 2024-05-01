/**
 * Script handling modals on product page (/shop/products).
 */
import Cart from "./models/Cart";
import CartItem from "./models/CartItem";
import ModalManager from "./modal";

const successAddToCartModal = ModalManager("my-modal");
const commentFormModal = ModalManager("comment-box");

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
  Cart().addItem(item);

  // open modal to display success
  successAddToCartModal.openModal();
}

window.addEventListener("DOMContentLoaded", function () {
  successAddToCartModal.init();
  commentFormModal.init();

  document
    .getElementById("product-customization-form")
    .addEventListener("submit", handleAddToCart);
});
