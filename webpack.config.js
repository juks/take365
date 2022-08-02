const path = require('path');

module.exports = {
  // Chosen mode tells webpack to use its built-in optimizations accordingly.
  entry: "./web/blocks/appRender.jsx",
  output: {
    // options related to how webpack emits results
    path: path.resolve(__dirname, "./web/js"),
    filename: "react.js", // string
    // the filename template for entry chunks
    // publicPath: "/assets/", // string
  },
  module: {
    // configuration regarding modules
    rules: [{
      test: /\.jsx?$/,
      loader: "babel-loader",
    }, {
      test: /\.css$/i,
      use: ['style-loader', 'css-loader'],
    }],
  }
}