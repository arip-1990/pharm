import {configureStore} from "@reduxjs/toolkit";
import {createWrapper} from 'next-redux-wrapper';

import { categoryApi } from "./categoryService";
import { cityApi } from "./cityService";
import { orderApi } from "./orderService";
import { catalogApi } from "./catalogService";
import { storeApi } from "./storeService";
import { offerApi } from "./offerService";
import { cardApi } from "./cardService";
import { chequeApi } from "./chequeService";
import { bonusApi } from "./bonusService";
import { couponApi } from "./couponService";

const makeStore = () =>
configureStore({
  reducer: {
    [cityApi.reducerPath]: cityApi.reducer,
    [orderApi.reducerPath]: orderApi.reducer,
    [categoryApi.reducerPath]: categoryApi.reducer,
    [catalogApi.reducerPath]: catalogApi.reducer,
    [storeApi.reducerPath]: storeApi.reducer,
    [offerApi.reducerPath]: offerApi.reducer,
    [cardApi.reducerPath]: cardApi.reducer,
    [chequeApi.reducerPath]: chequeApi.reducer,
    [bonusApi.reducerPath]: bonusApi.reducer,
    [couponApi.reducerPath]: couponApi.reducer,
  },
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({serializableCheck: false})
      .concat(cityApi.middleware)
      .concat(orderApi.middleware)
      .concat(categoryApi.middleware)
      .concat(catalogApi.middleware)
      .concat(storeApi.middleware)
      .concat(offerApi.middleware)
      .concat(cardApi.middleware)
      .concat(chequeApi.middleware)
      .concat(bonusApi.middleware)
      .concat(couponApi.middleware),
  devTools: true,
});


export type AppStore = ReturnType<typeof makeStore>;
export type AppState = ReturnType<AppStore["getState"]>;
export type AppDispatch = AppStore["dispatch"];

export const wrapper = createWrapper<AppStore>(makeStore, { debug: true });
