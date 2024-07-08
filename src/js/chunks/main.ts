import 'vite/modulepreload-polyfill';
import 'virtual:svg-icons-register';
import '@src/styles/_index.scss';

document.addEventListener( 'DOMContentLoaded', () => {
    console.log( 'js enabled' );
} );
