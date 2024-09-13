const AutoPrefixerPlugin = require('autoprefixer');
const CombineDuplicatedSelectorsPlugin = require('postcss-combine-duplicated-selectors');
const MediaQueriesPackerPlugin = require('css-mqpacker');
const CssNanoPlugin = require('cssnano');

module.exports = {
    plugins: [
        new AutoPrefixerPlugin(),
        new CombineDuplicatedSelectorsPlugin(),
        new MediaQueriesPackerPlugin(),
        new CssNanoPlugin(),
    ]
};
