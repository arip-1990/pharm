import { createApi } from '@reduxjs/toolkit/query/react';
import { axiosBaseQuery } from '../services/api';
import { IUser } from '../models/IUser';


export const userApi = createApi({
  reducerPath: 'userApi',
  baseQuery: axiosBaseQuery(),
  endpoints: (builder) => ({
    fetchUser: builder.query<IUser[], void>({
      query: () => ({url: '/user'})
    }),
  }),
});

export const { useFetchUserQuery } = userApi;
