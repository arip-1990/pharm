import { createApi } from '@reduxjs/toolkit/query/react';
import { axiosBaseQuery } from '../services/api';
import moment from 'moment';
import { IPagination } from '../models/IPagination';
import { IProduct } from '../models/IProduct';


export const productApi = createApi({
    reducerPath: 'productApi',
    baseQuery: axiosBaseQuery(),
    tagTypes: ['IProduct'],
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
            providesTags: ['IProduct'],
        }),
        updateProduct: builder.mutation<void, {slug: string, data: any}>({
            query: (args) => ({
              url: `/product/${args.slug}`,
              method: 'put',
              data: args.data
            }),
            invalidatesTags: ['IProduct'],
        }),
        updateDescriptionProduct: builder.mutation<void, {slug: string, data: any}>({
            query: (args) => ({
              url: `/product/description/${args.slug}`,
              method: 'put',
              data: args.data
            }),
            invalidatesTags: ['IProduct'],
        }),
        updateAttributesProduct: builder.mutation<void, {slug: string, data: any}>({
            query: (args) => ({
              url: `/product/attributes/${args.slug}`,
              method: 'put',
              data: args.data
            }),
            invalidatesTags: ['IProduct'],
        }),
    }),
});

export const { useFetchProductsQuery, useFetchProductQuery, useUpdateProductMutation, useUpdateDescriptionProductMutation, useUpdateAttributesProductMutation } = productApi;
