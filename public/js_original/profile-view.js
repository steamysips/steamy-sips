/**
 * This script is responsible for implementing the tab switching logic on the user profile page.
 */
import $ from "jquery";

function openTab(tabLinkElement, tabName) {
  // hide all tab contents
  $(".tabcontent").hide();

  // display content for clicked tab
  $("#" + tabName).show();

  // remove active class from the currently active tab link (same as removing active class from all tab links)
  $(".tablink").removeClass("active");

  // set active class only to the clicked tab link
  $(tabLinkElement).addClass("active");
}

$(document).ready(() => {
  const tabIDs = ["Account", "Orders", "Settings"]; // IDs of container for tabs

  // when user clicks on a tab, switch to respective tab
  $(".tablink").each((i, tablink) => {
    $(tablink).on("click", () => openTab(tablink, tabIDs[i]));
  });
});
