import { createApi } from "@reduxjs/toolkit/query/react";
import moment from "moment";
import { axiosBaseQuery } from "./api";
import { IPagination } from "../models/IPagination";
import { IStatistic } from "../models/IStatistic";

export const statisticApi = createApi({
  reducerPath: "statisticApi",
  baseQuery: axiosBaseQuery(),
  endpoints: (builder) => ({
    fetchStatistics: builder.query<
      IPagination<IStatistic>,
      {
        pagination: { current: number; pageSize: number };
        order: { field: string | null; direction: string };
      }
    >({
      query: (args) => {
        let params: any = {
          page: args.pagination.current,
          pageSize: args.pagination.pageSize,
        };

        if (args.order.field) {
          params.orderField = args.order.field;
          params.orderDirection = args.order.direction;
        }

        return {
          url: "/statistic",
          params,
        };
      },
      transformResponse: (response: IPagination<IStatistic>) => ({
        ...response,
        data: response.data.map((item) => ({
          ...item,
          createdAt: moment(item.createdAt, moment.defaultFormat),
          updatedAt: moment(item.updatedAt, moment.defaultFormat),
        })),
      }),
    }),
  }),
});

export const { useFetchStatisticsQuery } = statisticApi;
