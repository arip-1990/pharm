import {combineReducers} from "redux";
import {configureStore} from "@reduxjs/toolkit";
import {cartApi} from "./cartService";
import { cityApi } from "./cityService";
import { orderApi } from "./orderService";

const rootReducer = combineReducers({
  [cityApi.reducerPath]: cityApi.reducer,
  [cartApi.reducerPath]: cartApi.reducer,
  [orderApi.reducerPath]: orderApi.reducer,
});

export const store = configureStore({
  reducer: rootReducer,
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({serializableCheck: false})
    .concat(cityApi.middleware)
    .concat(cartApi.middleware)
    .concat(orderApi.middleware)
});

export type RootState = ReturnType<typeof rootReducer>;
export type AppDispatch = typeof store.dispatch;
