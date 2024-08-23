import { resolve, basename } from 'path';
import { defineConfig, loadEnv } from 'vite';
import { viteConfig } from './config/vite/viteConfig';
import { ViteOptions } from './config/vite/types/vite';

interface ThemeEnvFile {
  HOST: string,
  PORT: string,
  BUILD_DIR: string,
  MANIFEST: string,
  PATH_TO_THEME: string,
  VARIABLES_LIST_NAME: string
}
interface ProjectEnvFile {
  DB_NAME: string,
  DB_USER: string,
  DB_PASSWORD: string,
  DB_HOST?: string,
  DB_PREFIX?: string,
  WP_ENV: 'development' | 'production'
  WP_HOME: string
  WP_SITEURL: string

  AUTH_KEY?: string,
  SECURE_AUTH_KEY?: string,
  LOGGED_IN_KEY?: string,
  NONCE_KEY?: string,
  AUTH_SALT?: string,
  SECURE_AUTH_SALT?: string,
  LOGGED_IN_SALT?: string,
  NONCE_SALT?: string,

}

export default defineConfig( ( configEnv ) => {
  const { mode } = configEnv;

  const pathToProjectEnv = resolve(__dirname, '..', '..', '..', '..');

  // Получаем переменные env
  const projectEnv = loadEnv( configEnv.mode, pathToProjectEnv, '' ) as ProjectEnvFile;
  const themeEnv = loadEnv( configEnv.mode, process.cwd(), '' ) as ThemeEnvFile;

  const isDev = mode === 'development';

  const options: ViteOptions = {
    configEnv,
    mode,
    isDev,
    manifestName: themeEnv.MANIFEST.split('.')[0] || 'manifest',
    domain: new URL(projectEnv.WP_HOME ?? 'example.loc').hostname,
    port: +themeEnv.PORT || 3000,
    host: themeEnv.HOST || 'localhost',
    paths: {
      root: resolve( __dirname ),
      build: resolve( __dirname, themeEnv.BUILD_DIR || 'dist' ),
      src: resolve( __dirname, 'src' ),
      templates: resolve( __dirname, 'templates' ),
    },
    define: {
      API_PATH: '/wp-json/',
      IS_DEV: isDev,
      VARIABLES: themeEnv.VARIABLES_LIST_NAME,
    },
  };

  if(!isDev){
    options.paths.base = `${themeEnv.PATH_TO_THEME + basename(__dirname) + '/' + themeEnv.BUILD_DIR + '/'}`
  }

  return viteConfig( options );
} );
