import { createApi } from '@reduxjs/toolkit/query/react';
import { apiBaseQuery } from './api';
import moment from 'moment';
import { ICheque } from '../models/ICheque';

export const chequeApi = createApi({
    reducerPath: 'chequeApi',
    baseQuery: apiBaseQuery(),
    endpoints: (builder) => ({
      fetchCheques: builder.query<ICheque[], void>({
        query: () => ({url: '/cheque'}),
        transformResponse: (response: ICheque[]) => response.map(
          item => ({ ...item, date: moment(item.date) })
      )
      })
    }),
  })

// Export hooks for usage in functional components
export const {useFetchChequesQuery} = chequeApi;
