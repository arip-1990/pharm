import { createApi } from '@reduxjs/toolkit/query/react';
// import { ICard } from '../models/ICard';
import {ArrayPhotoId, IPhotoKids, UserPhotoCount} from '../models/IPhotoKids'
import { apiBaseQuery } from './api';
import moment from 'moment';
import {any} from "prop-types";

export const kidsPhotoApi = createApi({
    reducerPath: 'kidsPhotoApi',
    baseQuery: apiBaseQuery(),
    tagTypes: ['kidsPhoto'],
    endpoints: (builder) => ({
        fetchCards: builder.query<IPhotoKids[], number>({
            query: (age) => ({url: `/kids/photo/${age}`}),
            providesTags: ['kidsPhoto'],
        }),
        addLike: builder.mutation<void, { photo_id: number }>({
            query: ({ photo_id }) => ({
                url: `/kids/photo/likes/${photo_id}`,
                method: 'POST',
            }),
            invalidatesTags: ['kidsPhoto'],
        }),
        fetchArrayIdPhoto: builder.query<ArrayPhotoId[], void>({
            query: () => ({url: `/kids/photo/likes/myLike`}),
            providesTags: ['kidsPhoto'],
        }),

        uploadPhoto: builder.mutation<any, FormData>({
            query: (data) => ({
                url: `/kids/photo/add`,
                method: 'POST',
                data,
            }),
            invalidatesTags: ['kidsPhoto'],
        }),

        fetchArrayChildrenCount: builder.query<any, void>({
            query: () => ({url: `/kids/user/count/children`}),
            providesTags: ['kidsPhoto'],
        }),

        addChildren: builder.mutation<any, any>({
            query: (count) => ({
                url: `/kids/user/add/children`,
                method: 'POST',
                data: count
            }),
            invalidatesTags: ['kidsPhoto'],
        }),

        fetchArrayUserChildrenPhotos: builder.query<UserPhotoCount, void>({
            query: () => ({url: `/kids/user/photo`}),
            providesTags: ['kidsPhoto'],
        }),

    }),
})

export const {
    useFetchCardsQuery,
    useAddLikeMutation,
    useFetchArrayIdPhotoQuery,
    useUploadPhotoMutation,
    useFetchArrayChildrenCountQuery,
    useAddChildrenMutation,
    useFetchArrayUserChildrenPhotosQuery
} = kidsPhotoApi;
