import { createApi } from '@reduxjs/toolkit/query/react';
import { axiosBaseQuery } from './api';
import { IUser } from '../models/IUser';


export const userApi = createApi({
  reducerPath: 'userApi',
  baseQuery: axiosBaseQuery(),
  endpoints: (builder) => ({
    fetchUsers: builder.query<IUser[], void>({
      query: () => ({url: '/user'})
    }),
    fetchUser: builder.query<IUser, string>({
      query: (id) => ({url: '/user' + id})
    }),
  }),
});

export const { useFetchUserQuery, useFetchUsersQuery } = userApi;
