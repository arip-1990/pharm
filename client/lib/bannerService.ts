import { createApi } from "@reduxjs/toolkit/query/react";
import { apiBaseQuery } from "./api";

import { IBanner } from "../models/IBanner";

export const bannerApi = createApi({
  reducerPath: "bannerApi",
  baseQuery: apiBaseQuery('v2'),
  endpoints: (builder) => ({
    fetchBanners: builder.query<IBanner[], void>({
      query: () => ({ url: "/settings/banners" }),
    }),
  }),
});

// Export hooks for usage in functional components
export const { useFetchBannersQuery } = bannerApi;
