import {createApi} from '@reduxjs/toolkit/query/react';
import {axiosBaseQuery} from './api';
import {IPagination} from '../models/IPagination';
import {IOffer} from "../models/IOffer";


export const offerApi = createApi({
  reducerPath: 'offerApi',
  baseQuery: axiosBaseQuery(),
  endpoints: (builder) => ({
    fetchOffers: builder.query<IPagination<IOffer>, {
      search: { column: string, text: string } | undefined,
      pagination: { current: number, pageSize: number },
      order: { field: string | null, direction: string }
    }>({
      query: (args) => {
        let params: any = {
          page: args.pagination.current,
          pageSize: args.pagination.pageSize,
        };

        if (args.search?.column && args.search?.text) {
          params.searchColumn = args.search.column;
          params.searchText = args.search.text;
        }

        if (args.order.field) {
          params.orderField = args.order.field;
          params.orderDirection = args.order.direction;
        }

        return {
          url: '/offer',
          params
        }
      },
    }),
    fetchOffer: builder.query<IOffer, string>({
      query: (slug) => ({
        url: '/offer/' + slug
      }),
    }),
  }),
});

export const {useFetchOffersQuery, useFetchOfferQuery} = offerApi;
