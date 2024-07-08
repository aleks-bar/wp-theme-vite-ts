import { resolve } from 'path';
import { defineConfig, loadEnv } from 'vite';
import { viteConfig } from './config/vite/viteConfig';
import { ViteOptions } from './config/vite/types/vite';

interface EnvFile {
    HOST: string,
    PORT: string,
    MODE: 'development' | 'production',
    BUILD_DIR: string,
    PUBLIC_DIR: string,
    MANIFEST: string,
    PATH_TO_THEME: string,
    JS_CHUNKS: string
}

export default defineConfig( ( configEnv ) => {
    const { mode } = configEnv;
    // Получаем переменные env
    const env = loadEnv( configEnv.mode, process.cwd(), '' ) as EnvFile;

    const jsChunks = env.JS_CHUNKS?.split( ',' ) ?? [];

    const options: ViteOptions = {
        configEnv,
        mode,
        isDev: mode === 'development',
        port: +env.PORT || 3000,
        host: env.HOST || 'localhost',
        domain: 'example.loc',
        jsChunks,
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
