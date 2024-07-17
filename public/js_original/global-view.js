import "@picocss/pico/css/pico.min.css";
import "../styles/global.css";
import "../styles/theme.css";
import themeSwitcher from "./theme-switcher";
import Aos from "aos/src/js/aos";
import Splide from "@splidejs/splide";
import Cart from "./models/Cart";

themeSwitcher.init();

document.addEventListener("DOMContentLoaded", function () {
  const itemCount = Cart().getCartSize();

  // update cart item count in header
  document.querySelector("#mini-cart-count").textContent = `(${itemCount})`;
});
