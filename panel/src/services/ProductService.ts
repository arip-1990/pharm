import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';
import moment from 'moment';
import { IPagination } from '../models/IPagination';
import { IProduct } from '../models/IProduct';


export const productApi = createApi({
    reducerPath: 'productApi',
    baseQuery: fetchBaseQuery({
        baseUrl: 'http://pharm.test/api',
        credentials: "include"
    }),
    endpoints: (builder) => ({
        fetchProducts: builder.query<IPagination<IProduct>, {
            pagination: {current: number, pageSize: number},
            search: {column: string, text: string} | undefined,
            order: {field: string | null, direction: string},
            filters: {field: string, value: string}[]
        }>({
            query: (args) => {
                let params: any = {
                    page: args.pagination.current,
                    pageSize: args.pagination.pageSize,
                };

                args.filters.forEach(filter => {
                    params[filter.field] = filter.value;
                });

                if (args.search?.column && args.search?.text) {
                    params.searchColumn = args.search.column;
                    params.searchText = args.search.text;
                }
                if (args.order.field) {
                    params.orderField = args.order.field;
                    params.orderDirection = args.order.direction;
                }

                return {
                    url: '/product',
                    params
                }
            },
            transformResponse: (response: IPagination<IProduct>) => ({
                ...response,
                data: response.data.map(item => ({
                    ...item,
                    createdAt: moment(item.createdAt),
                    updatedAt: moment(item.updatedAt)
                }))
            }),
        }),
        fetchProduct: builder.query<IProduct, string>({
            query: (slug) => ({
                url: '/product/' + slug
            }),
        }),
    }),
});

export const { useFetchProductsQuery, useFetchProductQuery } = productApi;
