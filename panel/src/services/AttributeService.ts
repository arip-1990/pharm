import { createApi } from '@reduxjs/toolkit/query/react';
import { axiosBaseQuery } from './api';
import {IAttribute} from "../models/IAttribute";


export const attributeApi = createApi({
    reducerPath: 'attributeApi',
    baseQuery: axiosBaseQuery(),
    endpoints: (builder) => ({
        fetchAttributes: builder.query<IAttribute[], void>({
          query: () => ({
            url: '/attribute'
          }),
        }),
    }),
});

export const { useFetchAttributesQuery } = attributeApi;
