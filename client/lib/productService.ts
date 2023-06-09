import { createApi } from "@reduxjs/toolkit/query/react";
import { HYDRATE } from "next-redux-wrapper";
import { IProduct } from "../models/IProduct";
import { apiBaseQuery } from "./api";

export const productApi = createApi({
  reducerPath: "productApi",
  baseQuery: apiBaseQuery(),
  extractRehydrationInfo(action, { reducerPath }) {
    if (action.type === HYDRATE) {
      return action.payload[reducerPath];
    }
  },
  endpoints: (builder) => ({
    fetchPopularProducts: builder.query<any, void>({
      query: () => ({ url: "/catalog/popular" }),
    }),
    fetchProducts: builder.query<
      Pagination<IProduct>,
      { page: number; category?: string }
    >({
      query: (args) => ({
        url: "catalog" + (args.category ? "/" + args.category : ""),
        params: { page: args.page },
      }),
    }),
    fetchDiscounts: builder.query<Pagination<IProduct>, number>({
      query: (page) => ({
        url: "products/discounts",
        params: { page },
      }),
    }),
    getProduct: builder.query<IProduct, string>({
      query: (slug) => ({ url: "catalog/product/" + slug }),
    }),
  }),
});

// Export hooks for usage in functional components
export const {
  useFetchPopularProductsQuery,
  useFetchProductsQuery,
  useFetchDiscountsQuery,
  useGetProductQuery,
  util: { getRunningQueriesThunk },
} = productApi;

// export endpoints for use in SSR
export const {
  fetchPopularProducts,
  fetchProducts,
  fetchDiscounts,
  getProduct,
} = productApi.endpoints;
