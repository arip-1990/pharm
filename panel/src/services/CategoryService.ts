import { createApi } from '@reduxjs/toolkit/query/react';
import { axiosBaseQuery } from './api';
import { ICategory } from '../models/ICategory';


export const categoryApi = createApi({
    reducerPath: 'categoryApi',
    baseQuery: axiosBaseQuery(),
    endpoints: (builder) => ({
        fetchCategories: builder.query<ICategory[], void>({
            query: () => ({
                url: '/category'
            }),
        }),
    }),
});

export const { useFetchCategoriesQuery } = categoryApi;
