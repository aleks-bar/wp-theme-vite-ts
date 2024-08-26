import { App } from "js/common";

interface ChunkCustom {
  default: (app?: App) => void
}

interface ChunkData {
  chunk: ChunkCustom
  error: Error | null
  loading: boolean
}

interface ChunkLazy {
  chunkLoad: () => Promise<void>,
  chunkInit: (data: ChunkLazy['data'], app?: App) => void,
  data: ChunkData
}

export type ChunksList = Record<string, () => void>

/**
 * importFunc функция которая возвращает import('./путь/до/чанка')
 *
 * @param importFunc
 * @param chunkName
 */
export function chunkLazy(importFunc, chunkName: string): ChunkLazy {
  const chunkData: ChunkData = {
    chunk: { default: () => console.error(`${chunkName} - чанк не загружен`)},
    error: null,
    loading: false
  }

  const chunkLoad = async () => {
    try {
      chunkData.loading = true
      chunkData.chunk = await importFunc()
    } catch (error) {
      chunkData.error = error
    } finally {
      chunkData.loading = false;
    }
  };

  const chunkInit = (data: ChunkLazy['data'], app?: App) => {
    const { chunk, loading, error } = data;

    if ( loading ) {
      console.log( 'Загрузка...' );
    } else if ( error ) {
      console.error( error.message )
    } else {
      if(IS_DEV && app?.chunk){
        console.log('Загружен чанк: ' + `>  ${app.chunk}  <`)
      }
      chunk.default( app )
    }
  }

  return {
    chunkLoad,
    chunkInit,
    get data() { return chunkData },
  };
}

export const addLazyChunkForApp = (app: App, chunks: ChunksList) => {
  (async () => {
    if ( app?.chunk && chunks[ app.chunk ] ) {
      const { chunkLoad, data, chunkInit } = chunkLazy( chunks[ app.chunk ], app.chunk );
      chunkLoad().then( () => chunkInit(data, app) )
    }
  })()
}