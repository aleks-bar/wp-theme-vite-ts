import { app, App } from "js/common";

interface ChunkCustom {
  default: (app?: App) => void
}

interface ChunkData {
  module: ChunkCustom
  error: Error | null
  loading: boolean
}

interface ChunkLazy {
  chunkLoad: () => Promise<void>,
  chunkInit: (data: ChunkLazy['data'], app?: App) => void,
  data: ChunkData
}

export type ChunksList = Record<string, () => void>

function chunkLazy(importFunc, moduleName: string): ChunkLazy {
  const moduleData: ChunkData = {
    module: { default: () => console.error(`${moduleName} - модуль не загружен`)},
    error: null,
    loading: false
  }

  const chunkLoad = async () => {
    try {
      moduleData.loading = true
      moduleData.module = await importFunc()
    } catch (error) {
      moduleData.error = error
    } finally {
      moduleData.loading = false;
    }
  };

  const chunkInit = (data: ChunkLazy['data'], app?: App) => {
    const { module, loading, error } = data;

    if ( loading ) {
      console.log( 'Загрузка...' );
    } else if ( error ) {
      console.error( error.message )
    } else {
      if(IS_DEV && app?.chunk){
        console.log('Загружен чанк: ' + `>  ${app.chunk}  <`)
      }
      module.default( app )
    }
  }

  return {
    chunkLoad,
    chunkInit,
    get data() { return moduleData },
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