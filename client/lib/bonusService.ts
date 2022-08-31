import { createApi } from '@reduxjs/toolkit/query/react';
import { apiBaseQuery } from './api';
import moment from 'moment';
import { IBonus } from '../models/IBonus';

export const bonusApi = createApi({
    reducerPath: 'bonusApi',
    baseQuery: apiBaseQuery(),
    endpoints: (builder) => ({
      fetchBonuses: builder.query<IBonus[], void>({
        query: () => ({url: '/bonus'}),
        transformResponse: (response: IBonus[]) => response.map(
          item => ({ ...item, createdDate: moment(item.createdDate), actualStart: moment(item.actualStart), actualEnd: moment(item.actualEnd) })
      )
      })
    }),
  })

// Export hooks for usage in functional components
export const {useFetchBonusesQuery} = bonusApi;
