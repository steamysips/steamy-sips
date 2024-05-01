import "aos/dist/aos.css";
import "@splidejs/splide/dist/css/splide.min.css";
import Splide from "@splidejs/splide";
import Aos from "aos/src/js/aos";

document.addEventListener("DOMContentLoaded", function () {
  Aos.init();

  new Splide("#testimonials", {
    perPage: 2,
    breakpoints: {
      1000: {
        perPage: 1,
      },
    },
    lazyLoad: "nearby",
    preloadPages: 3,
    focus: 0,
  }).mount();
});
