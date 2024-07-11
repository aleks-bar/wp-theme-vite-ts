import { ConfigEnv } from 'vite';

export interface VitePaths {
    build: string;
    src: string;
    root: string;
    templates?: string;
}

export interface ViteOptions {
    configEnv: ConfigEnv,
    paths: VitePaths,
    isDev: boolean,
    mode: string,
    port: number,
    host: string,
    domain: string,
    jsChunks: string | object,
    define: Record<string, any>
}
