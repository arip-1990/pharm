import { createApi } from "@reduxjs/toolkit/query/react";
import { axiosBaseQuery } from "./api";
import { IBanner } from "../models/IBanner";

export const bannerApi = createApi({
  reducerPath: "banner",
  baseQuery: axiosBaseQuery("v2"),
  tagTypes: ["IBanner"],
  endpoints: (builder) => ({
    fetchBanners: builder.query<IBanner[], void>({
      query: () => ({
        url: "/banners",
        params: { type: "all" },
      }),
      providesTags: ["IBanner"],
    }),
    addBanner: builder.mutation<void, FormData>({
      query: (data) => ({
        url: "/banners",
        method: "post",
        data,
      }),
      invalidatesTags: ["IBanner"],
    }),
    updateSortBanners: builder.mutation<void, { id: number; sort: number }[]>({
      query: (items) => ({
        url: "/banners",
        method: "patch",
        data: { items },
      }),
      invalidatesTags: ["IBanner"],
    }),
    deleteBanner: builder.mutation<void, number>({
      query: (id) => ({
        url: `/banners/${id}`,
        method: "delete",
      }),
      invalidatesTags: ["IBanner"],
    }),
  }),
});

export const {
  useFetchBannersQuery,
  useAddBannerMutation,
  useUpdateSortBannersMutation,
  useDeleteBannerMutation,
} = bannerApi;
