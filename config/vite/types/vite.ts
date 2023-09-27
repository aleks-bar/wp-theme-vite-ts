import { ConfigEnv } from 'vite';

export interface VitePaths {
    build: string;
    src: string;
    root: string;
}

export interface ViteOptions {
    configEnv: ConfigEnv,
    paths: VitePaths,
    isDev: boolean,
    mode: string,
    port: number,
    host: string,
    domain: string,
    define: Record<string, any>
}
