import { createApi } from '@reduxjs/toolkit/query/react';
import { HYDRATE } from "next-redux-wrapper";
import { IStore } from '../models/IStore';
import { apiBaseQuery } from './api';

export const storeApi = createApi({
    reducerPath: 'storeApi',
    baseQuery: apiBaseQuery(),
    extractRehydrationInfo(action, { reducerPath }) {
      if (action.type === HYDRATE) {
        return action.payload[reducerPath]
      }
    },
    endpoints: (builder) => ({
      fetchStores: builder.query<Pagination<IStore>, number>({
        query: (page) => ({
          url: 'store',
          params: {page}
        }),
      }),
      getStore: builder.query<IStore, string>({
        query: (slug) => ({url: 'store/' + slug}),
      })
    }),
  })

// Export hooks for usage in functional components
export const {useFetchStoresQuery, useGetStoreQuery, util: { getRunningOperationPromises }} = storeApi;

// export endpoints for use in SSR
export const { fetchStores, getStore } = storeApi.endpoints;
