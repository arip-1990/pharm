import { createApi } from '@reduxjs/toolkit/query/react';
import { ICategory } from '../models/ICategory';
import { apiBaseQuery } from './api';

export const categoryApi = createApi({
    reducerPath: 'categoryApi',
    baseQuery: apiBaseQuery(),
    endpoints: (builder) => ({
      fetchCategories: builder.query<ICategory[], void>({
        query: () => ({url: '/category'}),
      })
    }),
  })

// Export hooks for usage in functional components
export const {useFetchCategoriesQuery, util: { getRunningOperationPromises }} = categoryApi;

// export endpoints for use in SSR
export const { fetchCategories } = categoryApi.endpoints;
