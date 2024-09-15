const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (env, argv) => {
    return config(argv.mode);
};

function config(mode) {
    return {
        devtool: 'source-map',
        entry: {
            app: [
                'jquery',
                'slick-carousel',
                './webpack/js/app.js',
                './webpack/scss/app.scss',
            ],
        },
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: path.resolve(__dirname, 'node_modules'),
                    include: [
                        path.resolve(__dirname, 'webpack', 'js'),
                    ],
                    use: {
                        loader: 'babel-loader'
                    },
                },
                {
                    test: /\.css$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                url: false,
                                sourceMap: true
                            },
                        },
                    ]
                },
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: "css-loader",
                            options: {
                                url: false,
                                sourceMap: true
                            }
                        },
                        {
                            loader: "sass-loader"
                        }
                    ]
                },
                {
                    test: require.resolve('jquery'),
                    use: [
                        {
                            loader: 'expose-loader',
                            options: {
                                exposes: '$'
                            },
                        },
                        {
                            loader: 'expose-loader',
                            options: {
                                exposes: 'jQuery'
                            },
                        }
                    ]
                },
            ],
        },
        optimization: {
            minimize: true,
            concatenateModules: true,
        },
        output: {
            filename: '[name].js',
            path: path.resolve(__dirname, 'public', 'assets', 'js'),
        },
        performance: {
            hints: 'error',
            maxEntrypointSize: 900000,
            maxAssetSize: 900000,
        },
        plugins: [
            new MiniCssExtractPlugin({
                filename: "../css/[name].css",
                chunkFilename: "../css/[id].css",
            })
        ],
        stats: {
            warnings: false,
        }
    }
};

