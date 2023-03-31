import { createApi } from "@reduxjs/toolkit/query/react";
import { axiosBaseQuery } from "./api";
import moment from "moment";
import { IPagination } from "../models/IPagination";
import { IProduct } from "../models/IProduct";
import { IUser } from "../models/IUser";

interface IStatisticUser {
  user: { id: string; name: string };
  addCountPhotos: number;
  editCountProducts: number;
  addTotalCountPhotos: number;
  editTotalCountProducts: number;
}

export const productApi = createApi({
  reducerPath: "productApi",
  baseQuery: axiosBaseQuery(),
  tagTypes: ["IProduct"],
  endpoints: (builder) => ({
    fetchProducts: builder.query<
      IPagination<IProduct>,
      {
        pagination: { current: number; pageSize: number };
        search: { column: string; text: string } | undefined;
        order: { field: string | null; direction: string };
        filters: { field: string; value: string }[];
      }
    >({
      query: (args) => {
        let params: any = {
          page: args.pagination.current,
          pageSize: args.pagination.pageSize,
        };

        args.filters.forEach((filter) => {
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
          url: "/product",
          params,
        };
      },
      transformResponse: (response: IPagination<IProduct>) => ({
        ...response,
        data: response.data.map((item) => ({
          ...item,
          createdAt: moment(item.createdAt),
          updatedAt: moment(item.updatedAt),
          photos: item.photos.map((photo) => ({
            ...photo,
            createdAt: moment(photo.createdAt),
            updatedAt: moment(photo.updatedAt),
          })),
        })),
      }),
    }),
    fetchProduct: builder.query<IProduct, string>({
      query: (slug) => ({
        url: "/product/" + slug,
      }),
      transformResponse: (response: IProduct) => ({
        ...response,
        createdAt: moment(response.createdAt),
        updatedAt: moment(response.updatedAt),
        photos: response.photos.map((photo) => ({
          ...photo,
          createdAt: moment(photo.createdAt),
          updatedAt: moment(photo.updatedAt),
        })),
      }),
      providesTags: ["IProduct"],
    }),
    fetchModerationProducts: builder.query<IProduct[], void>({
      query: () => ({
        url: "/product/moderation",
      }),
      transformResponse: (response: IProduct[]) =>
        response.map((item) => ({
          ...item,
          createdAt: moment(item.createdAt),
          updatedAt: moment(item.updatedAt),
          photos: item.photos.map((photo) => ({
            ...photo,
            createdAt: moment(photo.createdAt),
            updatedAt: moment(photo.updatedAt),
          })),
        })),
      providesTags: ["IProduct"],
    }),
    updateProduct: builder.mutation<void, { slug: string; data: any }>({
      query: (args) => ({
        url: `/product/${args.slug}`,
        method: "put",
        data: args.data,
      }),
      invalidatesTags: ["IProduct"],
    }),
    updateModerationProduct: builder.mutation<
      void,
      { slug: string; check: boolean }
    >({
      query: (args) => ({
        url: `/product/moderation/${args.slug}`,
        method: "put",
        data: { check: args.check },
      }),
      invalidatesTags: ["IProduct"],
    }),
    updateDescriptionProduct: builder.mutation<
      void,
      { slug: string; data: any }
    >({
      query: (args) => ({
        url: `/product/description/${args.slug}`,
        method: "put",
        data: args.data,
      }),
      invalidatesTags: ["IProduct"],
    }),
    updateAttributesProduct: builder.mutation<
      void,
      { slug: string; data: any }
    >({
      query: (args) => ({
        url: `/product/attributes/${args.slug}`,
        method: "put",
        data: args.data,
      }),
      invalidatesTags: ["IProduct"],
    }),
    addPhotoProduct: builder.mutation<
      void,
      { slug: string; data: FormData; onProgress: (event: any) => void }
    >({
      query: (args) => ({
        url: `/product/upload/${args.slug}`,
        headers: { "content-type": "multipart/form-data" },
        method: "post",
        data: args.data,
        onProgress: args.onProgress,
      }),
      invalidatesTags: ["IProduct"],
    }),
    updateStatusPhotosProduct: builder.mutation<void, number[]>({
      query: (items) => ({
        url: "/product/upload/status",
        method: "patch",
        data: { items },
      }),
      invalidatesTags: ["IProduct"],
    }),
    updatePhotosProduct: builder.mutation<void, any[]>({
      query: (items) => ({
        url: "/product/upload",
        method: "patch",
        data: { items },
      }),
      invalidatesTags: ["IProduct"],
    }),
    deletePhotosProduct: builder.mutation<void, number[]>({
      query: (items) => ({
        url: "/product/upload",
        method: "delete",
        data: { items },
      }),
      invalidatesTags: ["IProduct"],
    }),
    fetchStatistic: builder.query<
      IPagination<IStatisticUser>,
      { pagination: { current: number; pageSize: number } }
    >({
      query: (args) => {
        let params: any = {
          page: args.pagination.current,
          pageSize: args.pagination.pageSize,
        };

        return {
          url: "/product/statistic",
          params,
        };
      },
    }),
  }),
});

export const {
  useFetchProductsQuery,
  useFetchProductQuery,
  useFetchModerationProductsQuery,
  useUpdateProductMutation,
  useUpdateModerationProductMutation,
  useUpdateDescriptionProductMutation,
  useUpdateAttributesProductMutation,
  useAddPhotoProductMutation,
  useUpdatePhotosProductMutation,
  useUpdateStatusPhotosProductMutation,
  useDeletePhotosProductMutation,
  useFetchStatisticQuery,
} = productApi;
