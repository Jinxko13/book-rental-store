<template>
  <a-layout-sider width="200" style="background: #fff">
    <a-menu
      v-model:selectedKeys="sidebarSelectedKeys"
      v-model:openKeys="openKeys"
      mode="inline"
      :style="{ height: '100%', borderRight: 0 }"
    >
      <a-sub-menu key="sub1">
        <template #title>
          <span>
            <user-outlined />
            Quản lý người dùng
          </span>
        </template>
        <a-menu-item key="1">Danh sách</a-menu-item>
        <a-menu-item key="2">Thêm mới</a-menu-item>
      </a-sub-menu>
      <a-sub-menu key="sub2">
        <template #title>
          <span>
            Quản lý sách
          </span>
        </template>
        <a-menu-item key="3">Danh sách</a-menu-item>
        <a-menu-sub-item></a-menu-sub-item>
        <a-menu-item key="4">Thêm mới</a-menu-item>
      </a-sub-menu>
      <a-sub-menu key="sub3">
        <template #title>
          <span>
            <shopping-outlined />
            Quản lí đơn thuê
          </span>
        </template>
      </a-sub-menu>
    </a-menu>
  </a-layout-sider>
</template>

<script setup>

const openKeys = ref(['sub1'])
const sidebarSelectedKeys = ref(['1'])
</script>