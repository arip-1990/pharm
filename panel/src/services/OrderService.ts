import {createApi} from '@reduxjs/toolkit/query/react';
import moment from 'moment';
import {axiosBaseQuery} from './api';
import {IItem, IOrder} from '../models/IOrder';
import {IPagination} from '../models/IPagination';


export const orderApi = createApi({
  reducerPath: 'orderApi',
  baseQuery: axiosBaseQuery(),
  endpoints: (builder) => ({
    fetchOrders: builder.query<IPagination<IOrder>, {
      pagination: { current: number, pageSize: number },
      order: { field: string | null, direction: string },
      filters?: { field: string, value: string }[],
      platform?: 'mobile' | 'ios' | 'android' | 'web'
    }>({
      query: (args) => {
        let params: any = {
          page: args.pagination.current,
          pageSize: args.pagination.pageSize,
        };

        args.filters?.forEach(filter => {
          params[filter.field] = filter.value;
        });

        if (args.platform) {
          params.platform = args.platform;
        }

        if (args.order.field) {
          params.orderField = args.order.field;
          params.orderDirection = args.order.direction;
        }

        return {
          url: '/order',
          params
        }
      },
      transformResponse: (response: IPagination<IOrder>) => ({
        ...response,
        data: response.data.map(item => ({
          ...item,
          createdAt: moment(item.createdAt),
          updatedAt: moment(item.updatedAt),
          statuses: item.statuses.map(status => ({...status, createdAt: moment(status.createdAt)}))
        }))
      }),
    }),
    fetchOrder: builder.query<IOrder, number>({
      query: (id) => ({
        url: '/order/' + id
      }),
      transformResponse: (response: IOrder) => ({
        ...response,
        createdAt: moment(response.createdAt),
        updatedAt: moment(response.updatedAt),
        statuses: response.statuses.map(status => ({...status, createdAt: moment(status.createdAt)}))
      }),
    }),
    fetchOrderItems: builder.query<IItem[], number>({
      query: (id) => ({
        url: `/order/${id}/items`
      }),
    }),
  }),
});

export const {useFetchOrdersQuery, useFetchOrderQuery, useFetchOrderItemsQuery} = orderApi;
