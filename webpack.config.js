const path = require("path");

const sveltePath = path.resolve("node_modules", "svelte");

module.exports = {
  mode: "production",
  devtool: "source-map",
  entry: "./js/index.js",
  output: {
    path: path.resolve(__dirname, "js/bundle"),
    filename: "index.js",
    uniqueName: "apps/transfer"
  },
  module: {
    rules: [
      {
        test: /\.(?:svelte|m?js)$/,
        include: [path.resolve(__dirname, "src"), path.dirname(sveltePath)],
        use: ["babel-loader"]
      },
      {
        test: /\.svelte$/,
        use: ["svelte-loader"]
      },
      {
        test: /\.css$/,
        use: ["style-loader", "css-loader"]
      },
      {
        test: /\.s[ac]ss$/,
        use: ["style-loader", "css-loader", "sass-loader"]
      }
    ]
  } 
}