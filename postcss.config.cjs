module.exports = () => {
  return ( {
    plugins: {
      'postcss-preset-env': {},
      autoprefixer: {},
      tailwindcss: {},
      'postcss-combine-duplicated-selectors': {},
      'postcss-sort-media-queries': {},
    },
  } );
};

