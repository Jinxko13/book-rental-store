import { defineNuxtPlugin } from '#app'
import * as AntIcons from '@ant-design/icons-vue'

export default defineNuxtPlugin((nuxtApp) => {
  for (const [key, component] of Object.entries(AntIcons)) {
    nuxtApp.vueApp.component(key, component)
  }
})