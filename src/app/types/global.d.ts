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

declare module 'react-dom/client';
declare const __IS_DEV__: boolean;
declare const __API__: string;
declare const __USER_LOGIN__: string;
declare const __USER_PASS__: string;
// declare const __PATH_TO_SPRITE__: string;
// declare const __PORT__: string;
// declare const __HOST__: string;
// declare const __PATH_TO_THEMES__: string;
// declare const __THEME_NAME__: string;

declare const site: {
    ajaxurl: string;
    themeUrl: string;
    nonce: string;
};
