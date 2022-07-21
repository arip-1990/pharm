import axios, { AxiosRequestConfig, AxiosError } from 'axios';
import { BaseQueryFn } from '@reduxjs/toolkit/query';

export const API_URL = 'http://api.pharm.test';

const instance = axios.create({
    baseURL: API_URL + '/v2',
    headers: {'X-Requested-With': 'XMLHttpRequest'},
    withCredentials: true
});

export const apiBaseQuery = (): BaseQueryFn<
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
        const result = await instance({ url, method, headers, data, params, onUploadProgress: onProgress})
        return { data: result.data }
    } catch (error) {
        if (axios.isAxiosError(error))
            return {error: { status: error.response.status, data: error.response.data }};
    }
}

export default instance;