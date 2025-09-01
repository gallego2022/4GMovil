export default {
  plugins: {
    'postcss-import': {},
    'tailwindcss/nesting': 'postcss-nesting',
    tailwindcss: {},
    autoprefixer: {},
    'postcss-preset-env': {
      stage: 3,
      features: {
        'nesting-rules': false,
        'custom-properties': true,
        'custom-media-queries': true,
        'media-query-ranges': true,
        'custom-selectors': true,
        'color-mod-function': true,
        'logical-properties-and-values': true,
        'overflow-property': true,
        'focus-visible-pseudo-class': true,
        'focus-within-pseudo-class': true,
        'color-functional-notation': true,
      },
    },
    ...(process.env.NODE_ENV === 'production' ? { cssnano: {
      preset: ['default', {
        discardComments: { removeAll: true },
        normalizeWhitespace: true,
        colormin: true,
        minifyFontValues: true,
        minifySelectors: true,
      }],
    }} : {}),
  },
}; 