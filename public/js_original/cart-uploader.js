import Cart from "./models/Cart";

/**
 * This script is executed when user accesses /cart. It sends the cart
 * data from localStorage to the server and then reloads the page.
 * @returns {Promise<void>}
 */
async function uploadCart() {
  const items = Cart().getItems();

  await fetch(window.location.href, {
    method: "POST",
    body: JSON.stringify(items),
  });

  // add loading delay of 1s
  await new Promise((r) => setTimeout(r, 1000));

  // reload page so that server can display the cart
  location.reload();
}

window.addEventListener("DOMContentLoaded", uploadCart);
