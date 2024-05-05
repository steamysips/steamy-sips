/**
 * Script handling modals on product page (/shop/products).
 */
import Cart from "./models/Cart";
import CartItem from "./models/CartItem";
import ModalManager from "./modal";
import Chart from "chart.js/auto";

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

function createRatingChart() {
  const labels = ["5 star", "4 star", "3 star", "2 star", "1 star"];

  // fetch chart data from html
  const chartData = JSON.parse(
    document
      .getElementById("customer_rating_chart")
      .getAttribute("data-chart-data"),
  );

  const data = {
    labels: labels,
    datasets: [
      {
        axis: "y",
        label: "Percentage",
        data: chartData,
        fill: true,
        backgroundColor: "rgb(255, 159, 64)",
        borderWidth: 1,
      },
    ],
  };

  const config = {
    type: "bar",
    data,
    options: {
      indexAxis: "y",
    },
  };

  new Chart(document.getElementById("customer_rating_chart"), config);
}

window.addEventListener("DOMContentLoaded", function () {
  successAddToCartModal.init();
  commentFormModal.init();

  createRatingChart();

  document
    .getElementById("product-customization-form")
    .addEventListener("submit", handleAddToCart);
});
