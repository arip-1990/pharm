import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';
import { HYDRATE } from "next-redux-wrapper";
import { ICatalog } from '../models/ICatalog';
import { IOffer } from '../models/IOffer';
import { IProduct } from '../models/IProduct';
import { apiBaseQuery, API_URL } from './api';

export const catalogApi = createApi({
    reducerPath: 'catalogApi',
    baseQuery: apiBaseQuery(),
    extractRehydrationInfo(action, { reducerPath }) {
      if (action.type === HYDRATE) {
        return action.payload[reducerPath]
      }
    },
    endpoints: (builder) => ({
      fetchPopularProducts: builder.query<IProduct[], void>({
        query: () => ({url: '/catalog/popular'}),
      }),
      fetchProducts: builder.query<ICatalog, {page: number, category?: string}>({
        query: (args) => ({
          url: '/catalog' + (args.category ? ('/' + args.category) : ''),
          params: {page: args.page}
        }),
      }),
      searchProducts: builder.query<Pagination<IProduct>, {q: string, page: number}>({
        query: (args) => ({
          url: '/catalog/search',
          params: {q: args.q, page: args.page}
        }),
      }),
      getProduct: builder.query<{product: IProduct, offers: IOffer[]}, string>({
        query: (slug) => ({url: '/catalog/product/' + slug}),
      })
    }),
  })

// Export hooks for usage in functional components
export const {useFetchPopularProductsQuery, useFetchProductsQuery, useSearchProductsQuery, useGetProductQuery, util: { getRunningOperationPromises }} = catalogApi;

// export endpoints for use in SSR
export const { fetchPopularProducts, fetchProducts, searchProducts, getProduct } = catalogApi.endpoints;
