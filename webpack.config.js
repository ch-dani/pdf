const path = require("path");

module.exports = {
    resolve: {
        alias: {
            "js": path.resolve(__dirname, "resources/js/"),
            "scss": path.resolve(__dirname, 'resources/sass/')
        }
    },
    module: {
  rules: [
    {
      test: /\.js?$/,
      exclude: /(bower_components)/,
      use: [
        {
          loader: 'babel-loader',
          options: Config.babel()
        }
      ]
    }
  ]
} 
};



