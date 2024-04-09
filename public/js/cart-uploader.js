import { cart } from "./cart";

/**
 * This script is executed when user accesses /cart. It sends the cart
 * data from localStorage to the server and then reloads the page.
 * @returns {Promise<void>}
 */
async function uploadCart() {
  // send cart data to server
  const items = cart().getItems();
  console.log(items);
  const request = await fetch(window.location.href, {
    method: "POST",
    redirect: "follow",
    body: JSON.stringify(items),
  });
  console.log(request);

  // wait 1s
  await new Promise((r) => setTimeout(r, 1000));

  // reload page so that server can display the order details
  location.reload();
}

window.addEventListener("DOMContentLoaded", uploadCart);
