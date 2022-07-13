import {configureStore} from "@reduxjs/toolkit";
import {createWrapper} from 'next-redux-wrapper';

import {cartApi} from "./cartService";
import { categoryApi } from "./categoryService";
import { cityApi } from "./cityService";
import { orderApi } from "./orderService";
import { productApi } from "./productService";

const makeStore = () =>
  configureStore({
    reducer: {
      [cityApi.reducerPath]: cityApi.reducer,
      [cartApi.reducerPath]: cartApi.reducer,
      [orderApi.reducerPath]: orderApi.reducer,
      [categoryApi.reducerPath]: categoryApi.reducer,
      [productApi.reducerPath]: productApi.reducer,
    },
    middleware: (getDefaultMiddleware) =>
      getDefaultMiddleware({serializableCheck: false})
        .concat(cityApi.middleware)
        .concat(cartApi.middleware)
        .concat(orderApi.middleware)
        .concat(categoryApi.middleware)
        .concat(productApi.middleware),
    devTools: true,
});


export type AppStore = ReturnType<typeof makeStore>;
export type AppState = ReturnType<AppStore["getState"]>;
export type AppDispatch = AppStore["dispatch"];

export const wrapper = createWrapper<AppStore>(makeStore, { debug: true });
