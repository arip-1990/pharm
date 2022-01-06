import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';
import moment from 'moment';
import { IOrder } from '../models/IOrder';
import { IPagination } from '../models/IPagination';


export const orderApi = createApi({
    reducerPath: 'orderApi',
    baseQuery: fetchBaseQuery({
        baseUrl: 'http://pharm.test/api',
        credentials: "include"
    }),
    endpoints: (builder) => ({
        fetchOrders: builder.query<IPagination<IOrder>, {
            pagination: {current: number, pageSize: number},
            order: {field: string | null, direction: string}
        }>({
            query: (args) => {
                let params: any = {
                    page: args.pagination.current,
                    pageSize: args.pagination.pageSize,
                };

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
    }),
});

export const { useFetchOrdersQuery } = orderApi;
