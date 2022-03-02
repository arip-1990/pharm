import { BaseQueryFn } from '@reduxjs/toolkit/query';
import axios, { AxiosRequestConfig, AxiosError } from 'axios';

export const API_URL = process.env.API_URL || 'http://pharm.test';

export const axiosBaseQuery = (): BaseQueryFn<
{
    url: string
    method?: AxiosRequestConfig['method']
    headers?: AxiosRequestConfig['headers']
    data?: AxiosRequestConfig['data']
    params?: AxiosRequestConfig['params']
    onProgress?: AxiosRequestConfig['onUploadProgress']
},
unknown,
unknown
> => async ({ url, method, headers, data, params, onProgress }) => {
    try {
        const result = await axios({ url: `${API_URL}/api/v1${url}`, method, headers, data, params, onUploadProgress: onProgress, withCredentials: true })
        return { data: result.data }
    } catch (axiosError) {
        let err = axiosError as AxiosError
        return {error: { status: err.response?.status, data: err.response?.data }}
    }
}
