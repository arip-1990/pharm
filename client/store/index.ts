import { configureStore } from "@reduxjs/toolkit";
import { createWrapper } from "next-redux-wrapper";

import { categoryApi } from "../lib/categoryService";
import { cityApi } from "../lib/cityService";
import { orderApi } from "../lib/orderService";
import { catalogApi } from "../lib/catalogService";
import { storeApi } from "../lib/storeService";
import { offerApi } from "../lib/offerService";
import { cardApi } from "../lib/cardService";
import { chequeApi } from "../lib/chequeService";
import { bonusApi } from "../lib/bonusService";
import { couponApi } from "../lib/couponService";
import { bannerApi } from "../lib/bannerService";

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
      [bannerApi.reducerPath]: bannerApi.reducer,
    },
    middleware: (getDefaultMiddleware) =>
      getDefaultMiddleware({ serializableCheck: false })
        .concat(cityApi.middleware)
        .concat(orderApi.middleware)
        .concat(categoryApi.middleware)
        .concat(catalogApi.middleware)
        .concat(storeApi.middleware)
        .concat(offerApi.middleware)
        .concat(cardApi.middleware)
        .concat(chequeApi.middleware)
        .concat(bonusApi.middleware)
        .concat(couponApi.middleware)
        .concat(bannerApi.middleware),
    devTools: true,
  });

export type AppStore = ReturnType<typeof makeStore>;
export type AppState = ReturnType<AppStore["getState"]>;
export type AppDispatch = AppStore["dispatch"];

export const wrapper = createWrapper<AppStore>(makeStore);
