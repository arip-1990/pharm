import { createApi } from '@reduxjs/toolkit/query/react';
import { IOffer } from '../models/IOffer';
import { apiBaseQuery } from './api';

export const offerApi = createApi({
  reducerPath: 'offerApi',
  baseQuery: apiBaseQuery(),
  endpoints: (builder) => ({
    fetchOffers: builder.query<IOffer[], string>({
      query: (slug) => ({ url: `/offer/${slug}` }),
    }),
  }),
});

// Export hooks for usage in functional components
export const { useFetchOffersQuery, util: { getRunningQueriesThunk } } = offerApi;

// export endpoints for use in SSR
export const { fetchOffers } = offerApi.endpoints;
