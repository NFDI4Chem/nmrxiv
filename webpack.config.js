const path = require('path');
const Dotenv = require('dotenv-webpack');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
        },
    },
    plugins: [
        new Dotenv()
    ]
};
