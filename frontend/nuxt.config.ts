// https://nuxt.com/docs/api/configuration/nuxt-config
import { resolve } from "path";
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: false },
  ssr: true,
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8081/',
      frontendUrl: 'http://localhost:3000'
    },
    fonts: {
      families: {
        Inter: true,
        Roboto: [400, 700]
      }
    },
  },
  nitro: {
    devProxy: {
      '/api': {
        target: 'http://localhost:8081/api',
        changeOrigin: true,
        prependPath: true,
      }
    }
  },
  modules: [
    '@ant-design-vue/nuxt', 
    '@nuxt/fonts',
    '@nuxt/icon',
    '@nuxt/image',
    // '@unocss/nuxt',
    "@pinia/nuxt"
  ],
  alias: {
    "@": resolve(__dirname, "."),
    '@assets': resolve(__dirname, 'assets'),
    '@components': resolve(__dirname, 'components'),
  },
  css: [
    "@/assets/css/main.css",
  ],
  plugins: [
    '~/plugins/fontawesome',
    '~/plugins/antd-icons',
  ],
  vite: {
    plugins: [
      tailwindcss(),
    ],
  },
})