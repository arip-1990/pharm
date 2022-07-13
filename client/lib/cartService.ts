import { createApi } from '@reduxjs/toolkit/query/react';
import { ICart } from '../models/ICart';
import { apiBaseQuery } from './api';

export const cartApi = createApi({
    reducerPath: 'cartApi',
    baseQuery: apiBaseQuery(),
    tagTypes: ['ICart'],
    endpoints: (builder) => ({
      fetchCart: builder.query<ICart[], void>({
        query: () => ({url: '/cart'}),
        providesTags: ['ICart'],
      }),
      addCart: builder.mutation<void, string>({
        query: (id) => ({
            url: `/cart/${id}`,
            method: 'POST'
        }),
        invalidatesTags: ['ICart'],
      }),
      changeCart: builder.mutation<void, {id: string, quantity: number}>({
        query: (args) => ({
            url: `/cart/${args.id}`,
            method: 'PUT',
            data: {quantity: args.quantity}
        }),
        invalidatesTags: ['ICart'],
      }),
      deleteCart: builder.mutation<void, string>({
        query: (id) => ({
            url: `/cart/${id}`,
            method: 'DELETE'
        }),
        invalidatesTags: ['ICart'],
      })
    }),
  })

export const { useFetchCartQuery, useAddCartMutation, useChangeCartMutation, useDeleteCartMutation } = cartApi;
