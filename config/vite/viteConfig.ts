import { resolve } from 'path';
import { UserConfig } from 'vite';
import { createSvgIconsPlugin } from 'vite-plugin-svg-icons';
import tsconfigPaths from 'vite-tsconfig-paths';
import devManifest from 'vite-plugin-dev-manifest';
import { visualizer } from 'rollup-plugin-visualizer';
import liveReload from 'vite-plugin-live-reload';
import { ViteOptions } from './types/vite';

export function viteConfig( options: ViteOptions ): UserConfig {
  // @ts-ignore
  return {
    css: {
      devSourcemap: true,
    },
    define: options.define,
    publicDir: resolve( options.paths.src, 'assets' ),
    plugins: [
      visualizer( {
        template: 'treemap', // or sunburst
        open: false,
        gzipSize: true,
        brotliSize: true,
        filename: 'dist/analyse.html', // will be saved in project's root
      } ),
      devManifest( {
        manifestName: options.manifestName,
      } ),
      tsconfigPaths(),
      createSvgIconsPlugin( {
        iconDirs: [ resolve( options.paths.src, 'assets', 'icons' ) ],
        symbolId: '[name]',
        svgoOptions: {
          plugins:
            [
              {
                name: 'removeAttrs',
                params: {
                  attrs: [ 'class', 'data-name', 'fill', 'stroke' ],
                },
              },
            ],
        },
      } ),
      !! options.paths.templates && liveReload( options.paths.templates ),
    ],
    root: options.paths.root,
    base: options.paths.base,
    esbuild: {
      target: [ 'es2015' ],
    },
    build: {
      cssCodeSplit: true,
      manifest: true,
      outDir: options.paths.build,
      sourcemap: options.mode === 'development',
      rollupOptions: {
        input: {
          index: resolve( options.paths.src, 'js', 'index.ts' ),
        },
        output: {
          entryFileNames: 'js/[name].[hash].js',
          chunkFileNames: 'js/[name].[hash].js',
          assetFileNames: ( assetInfo ) => {
            // @ts-ignore
            let extType = assetInfo.name.split( '.' )[ 1 ];

            if ( /png|jpe?g|svg|gif|tiff|bmp|ico/i.test( extType ) ) {
              extType = 'img';
            }

            if ( /css|scss/i.test( extType ) ) {
              extType = 'css';
            }

            if ( extType === 'css' ) {
              return `${ extType }/[name].[hash][extname]`;
            }

            return `assets/${ extType }/[name].[hash][extname]`;
          },
          manualChunks( id ) {
            if ( id.includes( 'node_modules' ) ) {
              let vendorName = id
                .split(new RegExp('\\S+node_modules([\/|\\\\])', 'g'))
                .slice(-1)[0]
                .split(new RegExp('([\/|\\\\])', 'g'))[0];

              return `vendors-${vendorName}`;
            }
          },

        },
        plugins: [],
      },
    },
    server: {
      cors: true,
      strictPort: true,
      port: options.port,
      https: false,
      origin: options.domain,
      hmr: {
        host: options.host,
      },
    },
    resolve: {
      alias: {
        src: resolve( options.paths.src ),
        js: resolve( options.paths.src, 'js' ),
      },
      extensions: [
        '.js',
        '.ts',
        '.tsx',
        '.jsx',
        '.css',
        '.scss',
        '.jpg',
        '.jpeg',
        '.png',
        '.svg',
        '.vue',
      ],
    },
  };
}
