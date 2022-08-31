import { createApi } from '@reduxjs/toolkit/query/react';
import { apiBaseQuery } from './api';
import moment from 'moment';
import { ICoupon } from '../models/ICoupon';

export const couponApi = createApi({
    reducerPath: 'couponApi',
    baseQuery: apiBaseQuery(),
    endpoints: (builder) => ({
      fetchCoupons: builder.query<ICoupon[], void>({
        query: () => ({url: '/coupon'}),
        transformResponse: (response: ICoupon[]) => response.map(
          item => ({ ...item, actualStart: moment(item.actualStart), actualEnd: moment(item.actualEnd) })
      )
      })
    }),
  })

// Export hooks for usage in functional components
export const {useFetchCouponsQuery} = couponApi;
