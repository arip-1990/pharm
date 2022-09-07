import { createApi } from '@reduxjs/toolkit/query/react';
import { ICity } from '../models/ICity';
import { apiBaseQuery } from './api';

export const cityApi = createApi({
    reducerPath: 'cityApi',
    baseQuery: apiBaseQuery(),
    endpoints: (builder) => ({
      fetchCities: builder.query<ICity[], void>({
        query: () => ({url: '/city'})
      }),
      setCity: builder.mutation<void, string>({
        query: (city) => ({
          url: '/city',
          method: 'post',
          data: {city}
        })
      })
    }),
  })

export const { useFetchCitiesQuery, useSetCityMutation } = cityApi;
