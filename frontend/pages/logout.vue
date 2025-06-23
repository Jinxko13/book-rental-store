<template>
    <div class="flex justify-center items-center h-screen">
      <a-spin size="large" />
      <p class="ml-4">Đang đăng xuất...</p>
    </div>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router';
  import { onMounted } from 'vue';
  import { message } from 'ant-design-vue';
  
  const authStore = useAuthStore();

  onMounted(async () => {
    try {
      await authStore.logout();
    } catch (error) {
      console.error('Có lỗi xảy ra khi đăng xuất');
    } finally {
      await navigateTo('/');
    }
  });
  </script>