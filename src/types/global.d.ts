declare module '*.scss' {
    interface IClassNames {
        [className: string]: string
    }

    const classNames: IClassNames;
    // @ts-ignore
    export = classNames;
}

// declare module '*.svg' {
//     import React from 'react';
//     // @ts-ignore
//     const SVG: React.VFC<React.SVGProps<SVGSVGElement>>;
//     // @ts-ignore
//     export default SVG;
// }

interface ThemeVariables {
    ajaxurl: string;
    themeUrl: string;
    nonce: string;
}

declare const API_PATH: string;
declare const IS_DEV: boolean;
declare const VARIABLES: ThemeVariables;
// declare const __PATH_TO_SPRITE__: string;
// declare const __PORT__: string;
// declare const __HOST__: string;
// declare const __PATH_TO_THEMES__: string;
// declare const __THEME_NAME__: string;
