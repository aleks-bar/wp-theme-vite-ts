import { mediaQuery } from 'js/helpers/mediaQuery';

export interface AppInterface {
  body: HTMLElement | null
  chunk: string | null
  bodyBlock: ( isBlock?: boolean ) => void
  initDependencies: () => void
  setWindowVariables: () => void
}
export interface App extends Omit<AppInterface, 'initDependencies'> {}

export const app: AppInterface = {
  body: null,
  chunk: null,
  bodyBlock( isBlock = true ) {
    if ( this.body ) {
      this.body.style.overflowY = isBlock ? 'hidden' : 'auto';
    }
  },
  initDependencies() {
    const { isDevice } = mediaQuery();
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    this.body = document.body;
    const appElem = this.body.querySelector( '#app' );

    if ( scrollbarWidth && isDevice.pc ) {
      this.body.style.paddingRight = `${ scrollbarWidth }px`;
    }

    if ( appElem ) {
      this.chunk = appElem.getAttribute( 'chunk' ) ?? null;
    }
  },
  setWindowVariables() {
    // тут можно добавить переменные в window при инициализации
  }
};
