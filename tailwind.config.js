// /** @type {import('tailwindcss').Config} */

// theme.screens должно быть равно grid в  shared/utils/grid

module.exports = {
    content: [
        'templates/**/*.php',
        'config/**/*.php',
        './*.php',
        './src/**/*.{html,js,jsx,vue,ts,tsx}',
    ],
    theme: {
        extend: {
            colors: {
                whatsapp: '#38CD61',
                accent: '#E42038',
                'accent-hover': '#FF7485',
                'accent-active': '#C7152A',
                error: '#fc2940',
                success: '#a0f158',
                'light-red': '#FCE9EB',
                'light-red-hover': '#FFCED3',
                'light-red-active': '#FFBFC6',
                'bg-disabled': '#E6E9EF',
                'text-disabled': '#CBD1DE',
                blue: '#2086E4',
                'blue-1': '#EAF2FB',
                'blue-2': '#D5E5F6',
                'gray-1': '#737D8C',
                'gray-2': '#E3E5E8',
                'gray-3': '#9DA4AF',
                'gray-4': '#252528',
                'gray-5': '#EEEEEE',
                'gray-6': '#F4F6FA',
                'gray-7': '#EDEDED',
                'gray-8': '#454B54',
                'gray-9': '#E0E5EB',
                'gray-10': '#E2E8F3',
                'gray-11': '#D1D9EB',
                'dark-a-20': 'rgba(0,0,0, .2)',
                'modal-bg': 'rgba(36, 36, 36, 0.40)',
                'preloader-bg': 'rgba(255, 255, 255, 0.50)',
                'wt-a-60': 'rgba(255, 255, 255, 0.60)',
            },
        },
        borderRadius: {
            biggest: '60px',
            big: '40px',
            none: '0px',
            sm: '0.125rem',
            DEFAULT: '0.25rem', /* 4px */
            md: '0.375rem', /* 6px */
            lg: '0.5rem', /* 8px */
            xl: '0.75rem', /* 12px */
            '2xl': '1rem', /* 16px */
            '3xl': '1.5rem', /* 24px */
            20: '20px',
            full: '9999px',
        },
        boxShadow: {
            card: '0px 4px 4px 0px rgba(7, 27, 46, 0.03)',
            'menu-window': '-20px 0px 15px 5px rgba(7, 27, 46, 0.07)',
            header: '0px 3px 15px 0px rgba(7, 27, 46, 0.04)',
        },
        animation: {
            modal: 'scale .150s ease-in-out',
            'menu-window': 'translate-x-reverse .150s ease-in-out',
        },
        keyframes: {
            scale: {
                '0%': { transform: 'scale(0.5)', 'transform-origin': '50% 100%' },
                '20%': { transform: 'scale(0.6)', 'transform-origin': '50% 100%' },
                '40%': { transform: 'scale(0.7)', 'transform-origin': '50% 100%' },
                '60%': { transform: 'scale(0.8)', 'transform-origin': '50% 100%' },
                '80%': { transform: 'scale(0.9)', 'transform-origin': '50% 100%' },
                '100%': { transform: 'scale(1)', 'transform-origin': '50% 100%' },
            },
            'translate-x-reverse': {
                '0%': { transform: 'translateX(100%)' },
                '20%': { transform: 'translateX(80%)' },
                '40%': { transform: 'translateX(60%)' },
                '60%': { transform: 'translateX(40%)' },
                '80%': { transform: 'translateX(20%)' },
                '100%': { transform: 'translateX(0%)' },
            },
        },
        fontSize: {
            small: [
                '12px',
                {
                    lineHeight: '1',
                    fontWeight: '400',
                },
            ],
            'base-sm': [
                '16px',
                {
                    lineHeight: '1.4',
                    fontWeight: '400',
                },
            ],
            'base-lg': [
                '20px', {
                    lineHeight: '1.5',
                    letterSpacing: '-0.2px',
                    fontWeight: '400',
                },
            ],
            'h1-lg': [
                '80px', {
                    lineHeight: '1.2',
                    letterSpacing: '-1.6px',
                    fontWeight: '500',
                },
            ],
            'h1-sm': [
                '36px', {
                    lineHeight: '1.05',
                    letterSpacing: '-0.72px',
                    fontWeight: '500',
                },
            ],
            'h2-lg': [
                '60px', {
                    lineHeight: '1.1',
                    fontWeight: '500',
                    letterSpacing: '-1.2px',
                },
            ],
            'h2-sm': [
                '32px', {
                    lineHeight: '1.05',
                    fontWeight: '500',
                    letterSpacing: '-0.64px',
                },
            ],
            'h3-lg': [
                '40px', {
                    lineHeight: '1.2',
                    fontWeight: '500',
                },
            ],
            'h3-sm': [
                '24px', {
                    lineHeight: '1.3',
                    fontWeight: '500',
                },
            ],
            'h4-lg': [
                '36px', {
                    lineHeight: '1.3',
                    fontWeight: '500',
                },
            ],
            'h4-sm': [
                '24px', {
                    lineHeight: '1.3',
                    fontWeight: '500',
                },
            ],
            'h5-lg': [
                '30px', {
                    lineHeight: '1.3',
                    fontWeight: '600',
                },
            ],
            'h5-sm': [
                '36px', {
                    lineHeight: '1.3',
                    fontWeight: '500',
                },
            ],
            'card-lg': [
                '20px', {
                    lineHeight: '1.5',
                    fontWeight: '400',
                },
            ],
            'card-sm': [
                '14px', {
                    lineHeight: '1.4',
                    fontWeight: '400',
                },
            ],
            'card-title-lg': [
                '30px', {
                    lineHeight: '1.3',
                    fontWeight: '600',
                },
            ],
            'card-title-sm': [
                '18px', {
                    lineHeight: '1.1',
                    fontWeight: '500',
                },
            ],
        },
        container: {
            center: true,
            padding: {
                DEFAULT: '20px',
                md: '0px',
                lg: '16px',
            },
            screens: {
                sm: '576px',
                md: '768px',
                mdlg: '1056px',
                lg: '1312px',
                xl: '1542px',
                xxl: '1792px',
            },
        },
        screens: {
            xs: '0px',
            sm: '616px',
            md: '768px',
            mdlg: '1056px',
            lg: '1312px',
            xl: '1542px',
            xxl: '1792px',
            smr: { max: '615px' },
            mdr: { max: '767px' },
            mdlgr: { max: '1055px' },
            lgr: { max: '1311px' },
            xlr: { max: '1541px' },
            xxlr: { max: '1791px' },
        },
    },
    plugins: [],
};
