/**
 * A function for managing a modal
 *
 * @param {string} modalID ID of modal in document
 * @returns {{ init: (function(): void), openModal: (function(): void), closeModal: (function(): void)}}
 * @constructor
 *
 *  Adapted from: https://github.com/picocss/examples/blob/master/v1-preview/js/modal.js
 *  Pico.css - https://picocss.com
 *  Copyright 2019-2023 - Licensed under MIT
 */
function ModalManager(modalID) {
  const isOpenClass = "modal-is-open";
  const openingClass = "modal-is-opening";
  const closingClass = "modal-is-closing";
  const animationDuration = 600; // ms
  let visibleModal = null;
  const modal = document.getElementById(modalID);

  // Toggle modal
  const toggleModal = (event) => {
    event.preventDefault();

    typeof modal != "undefined" && modal != null && isModalOpen(modal)
      ? closeModal(modal)
      : openModal(modal);
  };

  // Is modal open
  const isModalOpen = () => {
    return modal.hasAttribute("open") && modal.getAttribute("open") !== "false";
  };

  // Open modal
  const openModal = () => {
    if (isScrollbarVisible()) {
      document.documentElement.style.setProperty(
        "--scrollbar-width",
        `${getScrollbarWidth()}px`,
      );
    }
    document.documentElement.classList.add(isOpenClass, openingClass);
    setTimeout(() => {
      visibleModal = modal;
      document.documentElement.classList.remove(openingClass);
    }, animationDuration);
    modal.setAttribute("open", true);
  };

  // Close modal
  const closeModal = () => {
    visibleModal = null;
    document.documentElement.classList.add(closingClass);
    setTimeout(() => {
      document.documentElement.classList.remove(closingClass, isOpenClass);
      document.documentElement.style.removeProperty("--scrollbar-width");
      modal.removeAttribute("open");
    }, animationDuration);
  };

  // Get scrollbar width
  const getScrollbarWidth = () => {
    // Creating invisible container
    const outer = document.createElement("div");
    outer.style.visibility = "hidden";
    outer.style.overflow = "scroll"; // forcing scrollbar to appear
    outer.style.msOverflowStyle = "scrollbar"; // needed for WinJS apps
    document.body.appendChild(outer);

    // Creating inner element and placing it in the container
    const inner = document.createElement("div");
    outer.appendChild(inner);

    // Calculating difference between container's full width and the child width
    const scrollbarWidth = outer.offsetWidth - inner.offsetWidth;

    // Removing temporary elements from the DOM
    outer.parentNode.removeChild(outer);

    return scrollbarWidth;
  };

  // Is scrollbar visible
  const isScrollbarVisible = () => {
    return document.body.scrollHeight > screen.height;
  };

  const init = () => {
    // Close modal with a click outside
    document.addEventListener("click", (event) => {
      if (visibleModal != null) {
        const modalContent = visibleModal.querySelector("article");
        const isClickInside = modalContent.contains(event.target);
        !isClickInside && closeModal(visibleModal);
      }
    });

    // Close modal with Esc key
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && visibleModal != null) {
        closeModal(visibleModal);
      }
    });

    // Add event listeners to modal components

    const topCloseButton = document.querySelector(
      `#${modalID} article > a[data-target="${modalID}"]`,
    ); // close button in top right corner

    if (topCloseButton !== null)
      topCloseButton.addEventListener("click", (e) => toggleModal(e));

    const bottomCloseButtom = document.querySelector(
      `#${modalID} article > footer a[data-target="${modalID}"]`,
    ); // close button in footer

    if (bottomCloseButtom !== null)
      bottomCloseButtom.addEventListener("click", (e) => toggleModal(e));
  };

  return { openModal, closeModal, init };
}

export default ModalManager;
