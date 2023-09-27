module.exports = () => {
    return ( {
        plugins: {
            'postcss-preset-env': {},
            'css-mqpacker': { sort: true },
            autoprefixer: {},
            tailwindcss: {},
        },
        resolve: {

        },
    } );
};

