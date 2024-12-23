import { createApi } from '@reduxjs/toolkit/query/react';
import {ArrayPhotoId, IPhotoKids} from '../models/IPhotoKids'
import { apiBaseQuery } from './api';

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
        addChildren: builder.mutation<void, { count: number }>({
            query: ({count}) => ({
                url: `/kids/photo/user`,
                method: 'POST',
                data: {userChildren: count}
            }),
            invalidatesTags: ['kidsPhoto'],
        }),

        fetchArrayUserChildrenPhotos: builder.query<IPhotoKids[], void>({
            query: () => ({url: `/kids/photo/user`}),
            providesTags: ['kidsPhoto'],
        }),

    }),
})

export const {
    useFetchCardsQuery,
    useAddLikeMutation,
    useFetchArrayIdPhotoQuery,
    useUploadPhotoMutation,
    useAddChildrenMutation,
    useFetchArrayUserChildrenPhotosQuery
} = kidsPhotoApi;
