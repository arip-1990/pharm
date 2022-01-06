import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';
import { ICategory } from '../models/ICategory';


export const categoryApi = createApi({
    reducerPath: 'categoryApi',
    baseQuery: fetchBaseQuery({
        baseUrl: 'http://pharm.test/api',
        credentials: "include"
    }),
    endpoints: (builder) => ({
        fetchCategories: builder.query<ICategory[], void>({
            query: () => ({
                url: '/category'
            }),
        }),
    }),
});

export const { useFetchCategoriesQuery } = categoryApi;
