import Splide from "@splidejs/splide";
import Aos from "aos/src/js/aos";
import "aos/dist/aos.css";

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
