import { BaseQueryFn } from '@reduxjs/toolkit/query';
import axios, { AxiosRequestConfig, AxiosError } from 'axios';

export const API_URL = 'https://pharm.test/api/v1';

export const axiosBaseQuery = (): BaseQueryFn<
{
    url: string
    method?: AxiosRequestConfig['method']
    data?: AxiosRequestConfig['data']
    params?: AxiosRequestConfig['params']
},
unknown,
unknown
> => async ({ url, method, data, params }) => {
    try {
        const result = await axios({ url: API_URL + url, method, data, params, withCredentials: true })
        return { data: result.data }
    } catch (axiosError) {
        let err = axiosError as AxiosError
        return {error: { status: err.response?.status, data: err.response?.data }}
    }
}
