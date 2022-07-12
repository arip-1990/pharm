import {combineReducers, Action} from "redux";
import { TypedUseSelectorHook, useSelector } from 'react-redux'
import {configureStore, ThunkAction} from "@reduxjs/toolkit";
import {createWrapper} from 'next-redux-wrapper';

import {cartApi} from "./cartService";
import { cityApi } from "./cityService";
import { orderApi } from "./orderService";
import { productSlice } from "./productService";

const rootReducer = combineReducers({
  [cityApi.reducerPath]: cityApi.reducer,
  [cartApi.reducerPath]: cartApi.reducer,
  [orderApi.reducerPath]: orderApi.reducer,
  [productSlice.name]: productSlice.reducer,
});

const makeStore = () =>
    configureStore({
        reducer: rootReducer,
        middleware: (getDefaultMiddleware) =>
          getDefaultMiddleware({serializableCheck: false})
            .concat(cityApi.middleware)
            .concat(cartApi.middleware)
            .concat(orderApi.middleware),
        devTools: true,
    });

// export const store = configureStore({
//   reducer: rootReducer,
//   middleware: (getDefaultMiddleware) =>
//     getDefaultMiddleware({serializableCheck: false})
//     .concat(cityApi.middleware)
//     .concat(cartApi.middleware)
//     .concat(orderApi.middleware)
// });

export const store = makeStore();

export type AppStore = ReturnType<typeof makeStore>;
export type AppState = ReturnType<AppStore['getState']>;
export type AppThunk<ReturnType = void> = ThunkAction<ReturnType, AppState, unknown, Action>;

// export type RootState = ReturnType<typeof rootReducer>;
export type AppDispatch = typeof store.dispatch;
export const useAppSelector: TypedUseSelectorHook<AppState> = useSelector

export const wrapper = createWrapper<AppStore>(makeStore);
