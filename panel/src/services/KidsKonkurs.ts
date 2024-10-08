import { createApi } from "@reduxjs/toolkit/query/react";
import { axiosBaseQuery } from "./api";
import { IKidsPhoto } from "../models/IKidsPhoto";

export const kidsApi = createApi({
  reducerPath: "kids",
  baseQuery: axiosBaseQuery("v3"),
  tagTypes: ["IKidsPhoto"],
  endpoints: (builder) => ({
    fetchBanners: builder.query<IKidsPhoto[], boolean>({
      query: (flag) => ({
        url: "/kids",
        params: {
          flag: flag,
        },
      }),
      providesTags: ["IKidsPhoto"],
    }),
    deletePhotos: builder.mutation({
      query: ({ id }) => ({
        url: `/kids/${id}`, // Путь к API для удаления фото по id
        method: 'DELETE', // Метод DELETE
      }),
      invalidatesTags: ['IKidsPhoto'], // Инвалидируем тег 'Photos' после удаления
    }),
    updatePhotos: builder.mutation({
      query: ({ids} ) => ({
        url: '/kids', // Путь к API для обновления нескольких фото
        method: 'POST', // Метод запроса
        data: { ids } , // Данные для обновления
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        }
      }),
      invalidatesTags: ['IKidsPhoto'], // Инвалидируем тег 'Photos' после обновления
    }),
  }),
});

export const {
  useFetchBannersQuery,
  useDeletePhotosMutation,
  useUpdatePhotosMutation
} = kidsApi;
