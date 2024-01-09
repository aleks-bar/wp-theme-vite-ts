// /** @type {import('tailwindcss').Config} */

module.exports = {
    content: [
        'templates/**/*.php',
        'config/**/*.php',
        './*.php',
        './src/**/*.{html,js,jsx,vue,ts,tsx}',
    ],
    theme: {
        container: {
            center: true,
            padding: {
                DEFAULT: '16px',
            },
            screens: {
                xs: '100%',
                sm: '544px',
                md: '736px',
                lg: '992px',
                xl: '1300px',
                xxl: '1600px',
            },
        },
        screens: {
            xs: '0px',
            sm: '576px',
            md: '768px',
            lg: '1024px',
            xl: '1332px',
            xxl: '1632px',
            smr: { max: '575px' },
            mdr: { max: '767px' },
            lgr: { max: '1023px' },
            xlr: { max: '1331px' },
            xxlr: { max: '1631px' },
        },
    },
    plugins: [],
};
