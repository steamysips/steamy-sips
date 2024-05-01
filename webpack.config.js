const path = require("path");
const entryPath = "./public/js_original/";

module.exports = {
  mode: "production",
  entry: {
    product_view: entryPath + "product-view.js",
    cart_view: entryPath + "cart-view.js",
    cart_uploader: entryPath + "cart-uploader.js",
    theme_switcher: entryPath + "theme-switcher.js",
  },
  output: {
    filename: "[name].bundle.js",
    path: path.resolve(__dirname, "public/js"),
  },
};
