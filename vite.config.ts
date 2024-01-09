import { resolve } from 'path';
import { defineConfig, loadEnv } from 'vite';
import { viteConfig } from './config/vite/viteConfig';
import { ViteOptions } from './config/vite/types/vite';

export default defineConfig( ( configEnv ) => {
    const { mode } = configEnv;
    // Получаем переменные env
    const env = loadEnv( configEnv.mode, process.cwd(), '' );

    const options: ViteOptions = {
        configEnv,
        mode,
        isDev: mode === 'development',
        port: +env.DEV_PORT || 3000,
        host: env.DEV_HOST || 'localhost',
        domain: 'example.loc',
        jsFile: env.MAIN_JS_FILE,
        paths: {
            root: resolve( __dirname ),
            build: resolve( __dirname, env.BUILD_DIR || 'dist' ),
            src: resolve( __dirname, 'src' ),
        },
        define: {
            __IS_DEV__: mode === 'development',
            __API__: '/wp-json/',
        },
    };

    return viteConfig( options );
} );
