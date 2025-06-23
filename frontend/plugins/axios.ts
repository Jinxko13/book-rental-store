// plugins/axios.ts
import axios, { type AxiosInstance, type AxiosResponse, type AxiosError } from 'axios';
import { defineNuxtPlugin } from '#app';
import { notification } from 'ant-design-vue';

export default defineNuxtPlugin((nuxtApp) => {
    const config = useRuntimeConfig();
    const baseURL = config.public.apiBase;
    const token = useCookie('auth_token');
    const user = useCookie('auth_user');    
    const api: AxiosInstance = axios.create({
        baseURL,
        withCredentials: true,
        timeout: 10000,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    });

    api.interceptors.request.use(
        (request) => {
            if (token.value) {
                request.headers = {
                    ...request.headers,
                    'Authorization': `Bearer ${token.value}`,
                };
            }
            return request;
        }, (error: AxiosError) => {
            return Promise.reject(error);
        }
    );

    api.interceptors.response.use(
        (response: AxiosResponse) => {
          return response;
        },
        (error: AxiosError) => {  
          if (error.response && error.response.status === 401) {
            notification.error({
              message: error.response.data.message,
              description: 'Phiên đăng nhập không hợp lệ',
            });
            token.value = '';
            user.value = null;
            navigateTo('/');
          }
          return Promise.reject(error);
        }
    );

  nuxtApp.provide('axios', api);
});