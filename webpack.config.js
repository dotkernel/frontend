const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require("extract-text-webpack-plugin");

const extractSass = new ExtractTextPlugin({
    filename: "[name]",
});

module.exports = {
    context: path.resolve(__dirname, './src/App/assets'),
    entry: {
        'js/app.js': './js/app.js',
        'css/app.css': './scss/app.scss'
    },
    output: {
        filename: '[name]',
        path: path.resolve(__dirname, './public/assets/')
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: [/node_modules/],
                use: [{
                    loader: 'babel-loader',
                    options: { presets: ['es2017'] },
                }],
            },
            {
                test: /\.css$/,
                use: [{
                    loader: 'style-loader'
                },{
                    loader: 'css-loader', options: {
                        sourceMap: process.env.NODE_ENV === "development"
                    }
                }]
            },
            {
                test: /\.scss$/,
                use: extractSass.extract({
                    use: [{
                        loader: "css-loader", options: {
                            sourceMap: process.env.NODE_ENV === "development"
                        }
                    }, {
                        loader: "sass-loader",options: {
                            sourceMap: process.env.NODE_ENV === "development"
                        }
                    }],
                    // use style-loader in development
                    fallback: "style-loader"
                })
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf|svg)$/,
                exclude: [/images?|img/],
                use: [
                    // As SVG may count as both font or image
                    // we will not treat any file in a folder
                    // with the name image(s) or img as a font
                    'file-loader?name=./fonts/[hash].[ext]'
                ]
            },
            {
                test: /\.(png|svg|jpg|gif)$/,
                exclude: [/fonts?/],
                use: [
                    // As SVG may count as both font or image
                    // we will not treat any file in a folder
                    // with the name of font(s) as an image
                    'file-loader?name=./images/[hash].[ext]'
                ]
            },

        ]
    },
    plugins: [
        extractSass,
    ]
};
