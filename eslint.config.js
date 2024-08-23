import typescriptParser from '@typescript-eslint/parser'
import js from "@eslint/js";
import airbnb from 'eslint-config-airbnb'
import wordpress from '@wordpress/eslint-plugin/configs/esnext.js'
export default [
  js.configs.recommended,
  {
    languageOptions: {
      ecmaVersion: 2015,
      parser: typescriptParser,
      sourceType: 'module',
      globals: {
        isDev: 'readonly',
        VARIABLES: 'readonly',
        __WebpackModuleApi: 'readonly',
        wp: 'readonly',
        ymaps3: 'readonly',
      }
    },
    // env: {
    //   browser: true
    // },
    plugins: {
      airbnb,
      wordpress
    },
    rules: {
      // 'linebreak-style': 'off',
      // 'no-plusplus': 'off',
      // indent: [ 'error', 2, { VariableDeclarator: 2 } ],
      // 'import/no-unresolved': 'off',
      // 'import/prefer-default-export': 'off',
      // 'no-unused-vars': 'off',
      // 'no-shadow': 'off',
      // 'import/extensions': 'off',
      // 'import/no-extraneous-dependencies': 'off',
      // 'no-underscore-dangle': 'off',
      // 'max-len': [ 'error', { ignoreComments: true, code: 140 } ],
      // 'no-param-reassign': 'off',
      // 'no-prototype-builtins': 'off',
      // 'no-unused-expressions': 'off',
      // 'arrow-body-style': 'off',
      // camelcase: 'off',
      'no-undef': 'off',
    }
  },
];