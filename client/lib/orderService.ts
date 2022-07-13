import { createApi } from '@reduxjs/toolkit/query/react';
import { IOrder } from '../models/IOrder';
import { apiBaseQuery } from './api';

export const orderApi = createApi({
    reducerPath: 'orderApi',
    baseQuery: apiBaseQuery(),
    endpoints: (builder) => ({
      fetchOrders: builder.query<Pagination<IOrder>, {page: number}>({
        query: (args) => ({url: '/order', params: {page: args.page}}),
      }),
      getOrder: builder.query<IOrder, number>({
        query: (id) => ({url: `/order/${id}`}),
      })
    }),
  })

export const { useFetchOrdersQuery, useGetOrderQuery } = orderApi;
