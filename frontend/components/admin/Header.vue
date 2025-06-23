<template>
  <a-layout-header class="header">
    <!-- <div class="logo">LOGO</div> -->
    <a-menu
    v-model:selectedKeys="selectedKeys"
    theme="dark"
    mode="horizontal"
    :style="{ lineHeight: '64px' }"
    >
      <a-menu-item key="logo" class="logo" style="color: white" disabled>
        <img src="/public/favicon.ico" alt="Logo" style="height: 40px;" />
      </a-menu-item>
      <a-menu-item key="home">
        <nuxt-link to="/">
          Trang chủ
        </nuxt-link>
      </a-menu-item>
      <a-menu-item key="products">
        <nuxt-link to="/admin">
          Sản phẩm
        </nuxt-link>
      </a-menu-item>
      <a-menu-item key="introduce">
        <nuxt-link to="/admin">
          Giới thiệu
        </nuxt-link>
      </a-menu-item>
      <a-menu-item key="contact">
        <nuxt-link to="/admin">
          Liên hệ
        </nuxt-link>
      </a-menu-item>
    </a-menu>
  </a-layout-header>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'
import { useMenuState } from '~/composables/useMenuState'

const route = useRoute()
const { selectedKeys } = useMenuState()

const pathToKey = {
  '/admin': 'products',

}

</script>

<style scoped>
:deep(.ant-menu-item a) {
  color: inherit;
  text-decoration: none;
  display: block;
}
</style>