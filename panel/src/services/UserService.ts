import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';
import { IUser } from '../models/IUser';


export const userApi = createApi({
  reducerPath: 'userApi',
  baseQuery: fetchBaseQuery({
    baseUrl: 'http://pharm.test/api',
    credentials: "include"
  }),
  endpoints: (builder) => ({
    fetchUser: builder.query<IUser[], void>({
      query: () => '/user'
    }),
  }),
});

export const { useFetchUserQuery } = userApi;
