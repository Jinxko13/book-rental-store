<!-- layouts/admin.vue -->
<template>
  <a-layout style="min-height: 100vh">
    <a-layout-sider v-model:collapsed="collapsed" collapsible>
      <div class="logo p-4">
        <h2 class="text-white text-lg font-bold">Book Admin</h2>
      </div>
      <a-menu
        v-model:selectedKeys="selectedKeys"
        theme="dark"
        mode="inline"
      >
        <a-menu-item key="dashboard">
          <template #icon><HomeOutlined /></template>
          <span>Dashboard</span>
          <NuxtLink to="/admin/dashboard"></NuxtLink>
        </a-menu-item>
        <a-menu-item key="books">
          <template #icon><BookOutlined /></template>
          <span>Quản lý sách</span>
          <NuxtLink to="/admin/books"></NuxtLink>
        </a-menu-item>
        <a-menu-item key="categories">
          <template #icon><AppstoreOutlined /></template>
          <span>Quản lý danh mục</span>
          <NuxtLink to="/admin/categories"></NuxtLink>
        </a-menu-item>
        <a-menu-item key="authors">
          <template #icon><UserOutlined /></template>
          <span>Tác giả</span>
          <NuxtLink to="/admin/authors"></NuxtLink>
        </a-menu-item>
        <a-menu-item key="member-groups">
          <template #icon><TeamOutlined /></template>
          <span>Nhóm thành viên</span>
          <NuxtLink to="/admin/user-groups"></NuxtLink>
        </a-menu-item>
        <a-menu-item key="rental-orders">
          <template #icon><ShoppingCartOutlined /></template>
          <span>Đơn cho thuê</span>
          <NuxtLink to="/admin/rental-orders"></NuxtLink>
        </a-menu-item>
        <a-menu-item key="rental-tops">
          <template #icon><ShoppingCartOutlined /></template>
          <span>Danh sách bán chạy</span>
          <NuxtLink to="/admin/rental-tops"></NuxtLink>
        </a-menu-item>
        <a-menu-item key="overdue-orders">
          <template #icon><ClockCircleOutlined /></template>
          <span>Đơn quá hạn</span>
          <NuxtLink to="/admin/overdue-orders"></NuxtLink>
        </a-menu-item>
        <!-- <a-menu-item key="promotions">
          <template #icon><GiftOutlined /></template>
          <span>Quản lý khuyến mại</span>
          <NuxtLink to="/admin/promotions"></NuxtLink>
        </a-menu-item> -->
      </a-menu>
    </a-layout-sider>
    <a-layout>
      <a-layout-header class="bg-white p-0 flex items-center justify-between px-6">
        <a-button
          type="text"
          @click="collapsed = !collapsed"
        >
          <MenuFoldOutlined v-if="!collapsed" />
          <MenuUnfoldOutlined v-else />
        </a-button>
        <div class="flex items-center">
          <!-- <a-badge count="5" class="mr-4">
            <BellOutlined style="font-size: 16px;" />
          </a-badge> -->
          <a-dropdown>
            <a class="flex items-center" @click.prevent>
              <a-avatar src="https://api.dicebear.com/7.x/avataaars/svg?seed=admin" />
              <span class="ml-2">{{ authStore.user?.name ? authStore.user?.name : 'Admin User' }}</span>
            </a>
            <template #overlay>
              <a-menu>
                <a-menu-item key="0">
                  <NuxtLink to="/admin/profile" class="flex items-center">
                  <UserOutlined />
                    <span class="ml-2">Thông tin cá nhân</span>
                  </NuxtLink>
                </a-menu-item>
                <!-- <a-menu-item key="1">
                  <NuxtLink to="/admin/settings" class="flex items-center">
                    <SettingOutlined />
                    <span class="ml-2">Cài đặt</span>
                  </NuxtLink>
                </a-menu-item> -->
                <a-menu-divider />
                <a-menu-item key="3">
                  <NuxtLink to="/logout" class="flex items-center"></NuxtLink>
                  <LogoutOutlined />
                  Đăng xuất
                </a-menu-item>
              </a-menu>
            </template>
          </a-dropdown>
        </div>
      </a-layout-header>
      <a-layout-content class="p-6">
        <a-breadcrumb class="mb-4">
          <a-breadcrumb-item>Admin</a-breadcrumb-item>
          <a-breadcrumb-item>{{ currentBreadcrumb }}</a-breadcrumb-item>
        </a-breadcrumb>
        <div class="bg-white p-6 min-h-full">
          <slot />
        </div>
      </a-layout-content>
      <a-layout-footer class="text-center">
        Hệ thống quản lý thư viện ©2025
      </a-layout-footer>
    </a-layout>
  </a-layout>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
  HomeOutlined,
  BookOutlined,
  AppstoreOutlined,
  UserOutlined,
  HeartOutlined,
  TeamOutlined,
  ShoppingCartOutlined,
  ClockCircleOutlined,
  GiftOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  SettingOutlined,
  LogoutOutlined,
  BellOutlined
} from '@ant-design/icons-vue';

const collapsed = ref(false);
const route = useRoute();
const selectedKeys = computed(() => {
  const path = route.path;
  const key = path.split('/').pop();
  return [key];
});
const authStore = useAuthStore();

const currentBreadcrumb = computed(() => {
  const key = selectedKeys.value[0];
  const menuMap = {
    'dashboard': 'Dashboard',
    'books': 'Quản lý sách',
    'categories': 'Quản lý danh mục',
    'authors': 'Quản lý tác giả',
    'favorites': 'Quản lý yêu thích',
    'member-groups': 'Quản lý nhóm thành viên',
    'rental-orders': 'Quản lý đơn cho thuê',
    'overdue-orders': 'Quản lý đơn quá hạn',
    'promotions': 'Quản lý khuyến mại',
    'profile': 'Thông tin cá nhân'
  };
  return menuMap[key] || 'Dashboard';
});
</script>

<style scoped>
.logo {
  height: 64px;
  margin: 0;
  overflow: hidden;
}
</style>