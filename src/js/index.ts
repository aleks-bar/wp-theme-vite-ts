import 'src/styles/layout/_index.scss'

import { app } from "js/common/app";

document.addEventListener('DOMContentLoaded', () => {
  app.setWindowVariables();
  app.initDependencies();

  console.log('js is enabled')

  // раскомментировать код ниже для использования системы чанков

  // addLazyChunkForApp( app,
  //   {
  //     'main': () => import('./chunks/main'),
  //   }
  // );
})
