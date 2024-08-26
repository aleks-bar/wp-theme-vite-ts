import breakpointsMinData from 'src/styles/settings/breakpoints/breakpointsMin.module.scss';
import breakpointsMaxData from 'src/styles/settings/breakpoints/breakpointsMax.module.scss';

interface BreakpointsMin {
  xs: string,
  sm: string,
  md: string,
  lg: string,
  xl: string,
  xxl: string,
}
interface BreakpointsMax {
  xs: string,
  sm: string,
  md: string,
  lg: string,
  xl: string,
}

interface ChangeHandler {
  onEnter?: () => void
  onExit?: () => void
}

interface Changer {
  breakpoints: Partial<keyof BreakpointsMin>[]
  changeHandler: ChangeHandler
}

interface OnChangeProps {
  isBreakpointZone: boolean,
  breakpoint: string
}

interface AddChangerProps {
  name: string
  breakpoints: ( keyof BreakpointsMin )[]
  onChange: ( { isBreakpointZone, breakpoint }: OnChangeProps ) => void
}

interface ChangersData {
  changers: Record<string, Changer> | {}
  addChanger: ( props: AddChangerProps ) => void
  removeChanger: ( name: string ) => void
}

type Device = 'mobile' | 'tablet' | 'pc'
type IsDevice = Record<Device, boolean>

interface MediaQueryResponse {
  addOnChange: ChangersData['addChanger']
  onChangelist: ChangersData['changers']
  isDevice: IsDevice
}

export const mediaQuery = () => {
  const breakpointsMin: BreakpointsMin = { ...breakpointsMinData };
  const breakpointsMax: BreakpointsMax = { ...breakpointsMaxData };

  const minMatch = ( minWidth: string ) => {
    return window.matchMedia( `(min-width: ${ minWidth })` );
  };
  const betweenMatch = ( minWidth: string, maxWidth: string ) => {
    return window.matchMedia( `(min-width: ${ minWidth }) and (max-width: ${ maxWidth })` );
  };

  const breakpoints: Record<keyof BreakpointsMin, MediaQueryList> = {
    xs: betweenMatch( breakpointsMin.xs, breakpointsMax.sm ),
    sm: betweenMatch( breakpointsMin.sm, breakpointsMax.sm ),
    md: betweenMatch( breakpointsMin.md, breakpointsMax.md ),
    lg: betweenMatch( breakpointsMin.lg, breakpointsMax.lg ),
    xl: betweenMatch( breakpointsMin.xl, breakpointsMax.xl ),
    xxl: minMatch( breakpointsMin.xxl ),
  };

  /**
   * Добавляет метод для события onChange на брейкпоинте из объекта changersData.changers
   *
   * @param changersData
   */
  const updateChangers = ( changersData: ChangersData ) => {
    Object.keys( breakpoints ).forEach( ( breakpoint ) => {
      breakpoints[ breakpoint as keyof BreakpointsMin ].onchange = ( event ) => {
        Object.keys( changersData.changers ).forEach( ( changerName ) => {
          if ( changersData.changers[ changerName ].breakpoints.includes( breakpoint ) ) {
            changersData.changers[ changerName ].onChange( { isBreakpointZone: event.matches, breakpoint } );
          }
        } );
      };
    } );
  };

  const changersData: ChangersData = {
    changers: {},
    addChanger( { name, breakpoints, onChange } ) {
      if ( ! this.changers[ name ] ) {
        this.changers[ name ] = {
          breakpoints,
          onChange,
        };
      } else {
        throw new Error( `changers.${ name } is already exist` );
      }
      updateChangers( this );
    },
    removeChanger( name ) {
      if ( Object.keys( this.changers ).includes( name ) ) {
        delete this.changers[ name ];
        updateChangers( this );
      }
    },
  };

  /**
   * addOnChange принимает объект с 3 параметрами breakpoints, name, onChange
   * breakpoints - строковый массив который принимает брейкпоинты на которых будет срабатывать функция onChange
   *
   * name - строка(уникальная), грубо говоря идентификатор для текущего объекта.
   *   Создать более одного объекта с одинаковым именем нельзя
   *
   * onChange - функция в которую передается объект OnChangeProps у которого есть 2 поля isBreakpointZone и breakpoint.
   *   Срабатывает при попадании или уходе из зоны брейкпоинта указанного в массиве breakpoints текущего объекта.
   *   isBreakpointZone - булевое значение указываеющее вход или выход в зону брейкпоинта
   *   breakpoint - строка, название брейкпоинта
   *
   *   Пример:
   *
   *   addOnChange({
   *     breakpoints: ['xxl'],
   *     name: 'myChanger',
   *     onChange: ({isBreakpointZone, breakpoint}) => {
   *       if(isBreakpointZone){
   *         // что-то делаем при входе в зону
   *       } else {
   *         // что-то делаем при выходе из зоны
   *       }
   *     }
   *   })
   */
  const response: MediaQueryResponse = {
    addOnChange: changersData.addChanger.bind( changersData ),
    onChangelist: changersData.changers,
    isDevice: {
      mobile: breakpoints.xs.matches || breakpoints.sm.matches,
      tablet: breakpoints.md.matches,
      pc: breakpoints.lg.matches || breakpoints.xl.matches || breakpoints.xxl.matches,
    },
  };

  return response;
};
