import axios, { AxiosRequestConfig } from 'axios';
import { BaseQueryFn } from '@reduxjs/toolkit/query';

export const API_URL = process.env.NEXT_PUBLIC_API_URL || 'https://api.120на80.рф';
export const COOKIE_DOMAIN = process.env.NEXT_PUBLIC_COOKIE_DOMAIN || '.xn--12080-6ve4g.xn--p1ai';

const instance = axios.create({
    baseURL: `${API_URL}/v1`,
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
        const result = await instance({ url, method, headers, data, params, onUploadProgress: onProgress});
        return { data: result.data }
    } catch (error) {
        if (axios.isAxiosError(error))
            return {error: { status: error.response?.status, data: error.response?.data }};
        return {error};
    }
}

instance.interceptors.request.use(config => {
    if (typeof window !== "undefined") {
        let token = localStorage.getItem('token');
        token = token ? JSON.parse(token)?.accessToken : null;
        if (token && config.headers) config.headers['Authorization'] = `Bearer ${token}`;
    }

    return config;
}, error => Promise.reject(error));

export default instance;
