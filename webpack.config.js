/*
 * Webpack is used to compile and minify/uglify JS and Sass.
 * Since this will nuke the public/assets folder every time it is run,
 * you should no longer manually add images etc. to the public folder.
 * We have setup a configuration that will automatically copy any image
 * from the images folder here to public/assets/images.
 *
 * so please, DO NOT MANUALLY ADD FILES TO PUBLIC/ASSETS!
 */

// Include npm modules
const path = require('path')
const webpack = require('webpack')
const autoprefixer = require('autoprefixer');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin')
const ExtractTextPlugin = require("extract-text-webpack-plugin")

let pathsToNuke = [
    './public/js',
    './public/css',
    './public/font',
    './public/images'
]

// Prepare plugin to extract styles into a css file
// instead of a javascript file
const extractStyles = new ExtractTextPlugin({
    filename: "[name]",
});

module.exports = {
	// This is the basepath for Webpack to look for source files
	// if you need to include modules outside of the App module,
	// move the "/App/assets" portion of the context onto the two
	// strings below, so it becomes "./App/assets/js/app.js" etc.
    context: path.resolve(__dirname, './src/App/assets'),

    // These are our entry files, this is the files Webpack will use
    // when looking for Sass and Javascript to compile.
    // The format is "DESTINATION": "SOURCE", and each path is
    // relative to the output path and the context respectively.
    entry: {
        'js/app.js': './js/app.js',
        'css/app.css': './scss/app.scss',
    },

    // The Output is where Webpack will export our files to
    // the filename will be resolved to the key in the entry object above.
    // The publicPath is what it'll rewrite css relative urls to use.
    // The path is where it'll save files too
    output: {
        filename: '[name]',
        publicPath: '/', // URL root
        path: path.resolve(__dirname, './public/') // Save-file root
    },

    // This is all the available file-loaders, feel free to append your own.
    // IMPORTANT NOTE: loaders are evaluated in REVERSE-ARRAY ORDER,
    // that means that they move from the end and towards the start.
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: [/node_modules/],
                use: [{
                    loader: 'babel-loader',
                    options: {
                        presets: ['es2017'],
                        sourceMap: process.env.NODE_ENV === "development"
                    },
                }],
            },
            {
                test: /\.scss$/,
                use: extractStyles.extract({
                    use: [{
                        loader: "css-loader",
                        options: {
                            url: false,
                            sourceMap: process.env.NODE_ENV === "development"
                        }
                    }, {
                        loader: 'postcss-loader',
                        options: {
                            sourceMap: process.env.NODE_ENV === "development",
                            plugins () {
                                return [autoprefixer]
                            }
                        }
                    }, {
                        loader: "sass-loader",
                        options: {
                            sourceMap: process.env.NODE_ENV === "development"
                        }
                    }],
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
                    'file-loader?name=fonts/[name].[ext]'
                ]
            },
            {
                test: /\.(png|svg|jpg|gif)$/,
                exclude: [/fonts?/],
                use: [
                    // As SVG may count as both font or image
                    // we will not treat any file in a folder
                    // with the name of font(s) as an image
                    'file-loader?name=images/[name].[ext]'
                ]
            },
        ]
    },
    plugins: [
        extractStyles,

        // Nuke the assets folder
        new CleanWebpackPlugin(pathsToNuke, {
            verbose: process.env.NODE_ENV !== "development",
            dry: process.env.NODE_ENV === "development"
        }),

        // Copy images from the source folder to the
        // destination folder
        new CopyWebpackPlugin([ {
        	from:'./images',to:'./images'
        }]),
    ]
};
