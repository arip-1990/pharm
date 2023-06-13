import { createApi } from "@reduxjs/toolkit/query/react";
import { HYDRATE } from "next-redux-wrapper";
import { IProduct } from "../models/IProduct";
import { apiBaseQuery } from "./api";

export const productApi = createApi({
  reducerPath: "productApi",
  baseQuery: apiBaseQuery('v2'),
  extractRehydrationInfo(action, { reducerPath }) {
    if (action.type === HYDRATE) {
      return action.payload[reducerPath];
    }
  },
  endpoints: (builder) => ({
    fetchProducts: builder.query<
      Pagination<IProduct>,
      { page: number; category?: string }
    >({
      query: (args) => ({
        url: "products" + (args.category ? `/${args.category}` : ""),
        params: { page: args.page },
      }),
    }),
    searchProducts: builder.query<
      Pagination<IProduct>,
      { q: string; page: number; pageSize: number }
    >({
      query: (params) => ({
        url: "/products/search",
        params: { ...params, full: 1 },
      }),
    }),
    searchNameProducts: builder.query<
      { id: string; name: string; slug: string; highlight: string }[],
      string
    >({
      query: (text) => ({
        url: "/products/search",
        params: { q: text },
      }),
    }),
    fetchPopulars: builder.query<any, void>({
      query: () => ({ url: "/products/populars" }),
    }),
    fetchDiscounts: builder.query<Pagination<IProduct>, number>({
      query: (page) => ({
        url: "products/discounts",
        params: { page },
      }),
    }),
    getProduct: builder.query<IProduct, string>({
      query: (slug) => ({ url: `products/${slug}` }),
    }),
  }),
});

// Export hooks for usage in functional components
export const {
  useFetchProductsQuery,
  useSearchProductsQuery,
  useSearchNameProductsQuery,
  useFetchPopularsQuery,
  useFetchDiscountsQuery,
  useGetProductQuery,
  util: { getRunningQueriesThunk },
} = productApi;

// export endpoints for use in SSR
export const {
  fetchProducts,
  searchProducts,
  searchNameProducts,
  fetchPopulars,
  fetchDiscounts,
  getProduct,
} = productApi.endpoints;
