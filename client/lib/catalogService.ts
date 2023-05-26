import { createApi } from "@reduxjs/toolkit/query/react";
import { HYDRATE } from "next-redux-wrapper";
import { ICatalog } from "../models/ICatalog";
import { IOffer } from "../models/IOffer";
import { IProduct } from "../models/IProduct";
import { apiBaseQuery } from "./api";

export const catalogApi = createApi({
  reducerPath: "catalogApi",
  baseQuery: apiBaseQuery(),
  extractRehydrationInfo(action, { reducerPath }) {
    if (action.type === HYDRATE) {
      return action.payload[reducerPath];
    }
  },
  endpoints: (builder) => ({
    fetchPopularProducts: builder.query<IProduct[], void>({
      query: () => ({ url: "/catalog/popular" }),
    }),
    fetchProducts: builder.query<ICatalog, { page: number; category?: string }>(
      {
        query: (args) => ({
          url: "/catalog" + (args.category ? "/" + args.category : ""),
          params: { page: args.page },
        }),
      }
    ),
    fetchStockProducts: builder.query<ICatalog, { page: number }>({
      query: (args) => ({
        url: "/catalog/stock",
        params: { page: args.page },
      }),
    }),
    searchProducts: builder.query<
      Pagination<IProduct>,
      { q: string; page: number; pageSize: number }
    >({
      query: (params) => ({
        url: "/catalog/search",
        params: { ...params, full: 1 },
      }),
    }),
    searchNameProducts: builder.query<
      { id: string; name: string; slug: string; highlight: string }[],
      string
    >({
      query: (text) => ({
        url: "/catalog/search",
        params: { q: text },
      }),
    }),
    getProduct: builder.query<{ product: IProduct; offers: IOffer[] }, string>({
      query: (slug) => ({ url: "/catalog/product/" + slug }),
    }),
  }),
});

// Export hooks for usage in functional components
export const {
  useFetchPopularProductsQuery,
  useFetchProductsQuery,
  useFetchStockProductsQuery,
  useSearchProductsQuery,
  useSearchNameProductsQuery,
  useGetProductQuery,
  util: { getRunningQueriesThunk },
} = catalogApi;

// export endpoints for use in SSR
export const {
  fetchPopularProducts,
  fetchProducts,
  fetchStockProducts,
  searchProducts,
  searchNameProducts,
  getProduct,
} = catalogApi.endpoints;
