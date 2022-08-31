import { createApi } from '@reduxjs/toolkit/query/react';
import { ICard } from '../models/ICard';
import { apiBaseQuery } from './api';
import moment from 'moment';

export const cardApi = createApi({
    reducerPath: 'cardApi',
    baseQuery: apiBaseQuery(),
    tagTypes: ['Card'],
    endpoints: (builder) => ({
      fetchCards: builder.query<ICard[], void>({
        query: () => ({url: '/card'}),
        transformResponse: (response: ICard[]) => response.map(
          item => ({ ...item, statusDate: moment(item.statusDate), expiryDate: moment(item.expiryDate) })
      ),
        providesTags: ['Card'],
      }),
      getCard: builder.query<ICard, string>({
        query: (id) => ({url: '/card/' + id}),
      }),
      blockCard: builder.mutation<void, string>({
        query: (id) => ({
          url: '/card/block/' + id,
          method: 'put'
        }),
        invalidatesTags: ['Card']
      })
    }),
  })

// Export hooks for usage in functional components
export const {useFetchCardsQuery, useGetCardQuery, useBlockCardMutation} = cardApi;
