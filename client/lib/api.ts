import axios from 'axios';
import type { AxiosRequestConfig, AxiosError } from 'axios';
import { BaseQueryFn } from '@reduxjs/toolkit/dist/query/baseQueryTypes';

export declare type Error = {
    status: number,
    data: {code: number, message: string}
};

type Args = {
    url: string
    method?: AxiosRequestConfig['method']
    headers?: AxiosRequestConfig['headers']
    data?: AxiosRequestConfig['data']
    params?: AxiosRequestConfig['params']
    onProgress?: AxiosRequestConfig['onUploadProgress']
};

export const API_URL = process.env.NEXT_PUBLIC_API_URL || 'https://api.120на80.рф';
export const COOKIE_DOMAIN = process.env.NEXT_PUBLIC_COOKIE_DOMAIN || '.xn--12080-6ve4g.xn--p1ai';

const instance = axios.create({
    baseURL: `${API_URL}/v1`,
    headers: {'X-Requested-With': 'XMLHttpRequest'},
    withCredentials: true
});

export const apiBaseQuery = (): BaseQueryFn<Args, unknown, Error> => async ({ url, method, headers, data, params, onProgress }) => {
    try {
        const result = await instance({ url, method, headers, data, params, onUploadProgress: onProgress});
        return { data: result.data }
    } catch (error) {
        let err = error as AxiosError;
        return {error: {status: err.response?.status || Number(err.code), data: err.response?.data || err.message}};
    }
}

export default instance;
