import { createApi } from '@reduxjs/toolkit/query/react';
import { HYDRATE } from "next-redux-wrapper";
import { ICatalog } from '../models/ICatalog';
import { IOffer } from '../models/IOffer';
import { IProduct } from '../models/IProduct';
import { apiBaseQuery } from './api';

export const catalogApi = createApi({
    reducerPath: 'catalogApi',
    baseQuery: apiBaseQuery(),
    extractRehydrationInfo(action, { reducerPath }) {
      if (action.type === HYDRATE) {
        return action.payload[reducerPath]
      }
    },
    endpoints: (builder) => ({
      fetchPopularProducts: builder.query<any, void>({
        query: () => ({url: '/catalog/popular'}),
      }),
      fetchProducts: builder.query<ICatalog, {page: number, category?: string}>({
        query: (args) => ({
          url: 'catalog' + (args.category ? ('/' + args.category) : ''),
          params: {page: args.page}
        }),
      }),
      getProduct: builder.query<{product: IProduct, offers: IOffer[]}, string>({
        query: (slug) => ({url: 'catalog/product/' + slug}),
      })
    }),
  })

// Export hooks for usage in functional components
export const {useFetchPopularProductsQuery, useFetchProductsQuery, useGetProductQuery, util: { getRunningOperationPromises }} = catalogApi;

// export endpoints for use in SSR
export const { fetchPopularProducts, fetchProducts, getProduct } = catalogApi.endpoints;
