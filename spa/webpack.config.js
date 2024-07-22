const webpack = require('webpack');
const Encore = require('@symfony/webpack-encore');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const dotenv = require('dotenv')

const env = dotenv.config({ path: '../.env' }).parsed;

// Log the environment variables
console.log('Loaded Environment Variables:', env);

if (!env) {
  throw new Error('Could not load .env file');
}

const envKeys = Object.keys(env).reduce((prev, next) => {
  prev[`process.env.${next}`] = JSON.stringify(env[next]);
  return prev;
}, {});

Encore
  .setOutputPath('public/')
  .setPublicPath('/')
  .cleanupOutputBeforeBuild()
  .addEntry('app', './src/app.js')
  .enablePreactPreset()
  .enableSassLoader()
  .enableSingleRuntimeChunk()
  .addPlugin(new HtmlWebpackPlugin({ template: 'src/index.ejs', alwaysWriteToDisk: true }))
  // .addPlugin(new webpack.DefinePlugin({
  //   'ENV_API_ENDPOINT': JSON.stringify(process.env.API_ENDPOINT)
  // }))
  .addPlugin(new webpack.DefinePlugin(envKeys))
;

module.exports = Encore.getWebpackConfig();