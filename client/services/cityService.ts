import { createApi } from '@reduxjs/toolkit/query/react';
import { apiBaseQuery } from './api';

export const cityApi = createApi({
    reducerPath: 'cityApi',
    baseQuery: apiBaseQuery(),
    endpoints: (builder) => ({
      fetchCities: builder.query<string[], void>({
        query: () => ({url: '/city'}),
      })
    }),
  })

export const { useFetchCitiesQuery } = cityApi;
