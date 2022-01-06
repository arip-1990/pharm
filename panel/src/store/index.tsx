import { combineReducers } from "redux";
import { configureStore } from "@reduxjs/toolkit";
import { categoryApi } from "../services/CategoryService";
import { userApi } from "../services/UserService";
import { statisticApi } from "../services/StatisticService";
import { orderApi } from "../services/OrderService";
import { productApi } from "../services/ProductService";

const rootReducer = combineReducers({
  [categoryApi.reducerPath]: categoryApi.reducer,
  [userApi.reducerPath]: userApi.reducer,
  [orderApi.reducerPath]: orderApi.reducer,
  [productApi.reducerPath]: productApi.reducer,
  [statisticApi.reducerPath]: statisticApi.reducer,
});

export const store = configureStore({
  reducer: rootReducer,
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({ serializableCheck: false })
      .concat(categoryApi.middleware)
      .concat(userApi.middleware)
      .concat(orderApi.middleware)
      .concat(productApi.middleware)
      .concat(statisticApi.middleware),
});

export type RootState = ReturnType<typeof rootReducer>;
export type AppDispatch = typeof store.dispatch;
