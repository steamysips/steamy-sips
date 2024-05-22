function openTab(evt, tabName) {
  console.log("New tab = " + tabName);

  // hide all tab contents
  const tabcontents = [...document.getElementsByClassName("tabcontent")];
  for (let i = 0; i < tabcontents.length; i++) {
    tabcontents[i].style.display = "none";
  }

  // remove active class from the currently active tab link
  const tablinks = document.getElementsByClassName("tablink");
  for (let i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // display content for clicked tab
  document.getElementById(tabName).style.display = "block";

  // set active class only to the clicked tab link
  evt.currentTarget.className += " active";
}

const tabs = ["Account", "Orders", "Settings"];

window.addEventListener("DOMContentLoaded", () => {
  [...document.getElementsByClassName("tablink")].forEach((tablink, i) => {
    console.log(i, tablink);
    tablink.addEventListener("click", (e) => openTab(e, tabs[i]));
  });
});