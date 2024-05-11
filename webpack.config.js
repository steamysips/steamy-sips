const path = require("path");
const entryPath = "./public/js_original/";

module.exports = {
  mode: "production",
  entry: {
    global_view: entryPath + "global-view.js",
    home_view: entryPath + "home-view.js",
    product_view: entryPath + "product-view.js",
    cart_view: entryPath + "cart-view.js",
    theme_switcher: entryPath + "theme-switcher.js",
  },
  output: {
    filename: "[name].bundle.js",
    path: path.resolve(__dirname, "public/js"),
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: [["@babel/preset-env", { targets: "defaults" }]],
          },
        },
      },
      {
        test: /\.css$/i,
        use: ["style-loader", "css-loader"],
      },
      {
        test: /\.s[ac]ss$/i,
        use: [
          // Creates `style` nodes from JS strings
          "style-loader",
          // Translates CSS into CommonJS
          "css-loader",
          // Compiles Sass to CSS
          "sass-loader",
        ],
      },
    ],
  },
};
