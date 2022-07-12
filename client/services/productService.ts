import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { HYDRATE } from "next-redux-wrapper";
import { IProduct } from "../models/IProduct";
import { AppState } from "./store";

export const productSlice = createSlice({
  name: 'product',
  initialState: {data: []},
  reducers: {
    setProductData: (state, action: PayloadAction<IProduct[]>) => {
      state.data = action.payload;
    }
  },
  extraReducers: {
    [HYDRATE]: (state, action) => {
      return {
        ...state,
        ...action.payload.data
      }
    }
  }
});

export const {setProductData} = productSlice.actions;

export const selectProductData = (state: AppState) => state.product.data;
