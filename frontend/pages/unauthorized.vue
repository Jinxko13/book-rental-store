<!-- pages/unauthorized.vue -->
<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="max-w-md w-full">
      <div class="bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4 text-red-600">Không có quyền truy cập</h1>
        <p class="mb-6">Bạn không có quyền truy cập vào trang này.</p>
        <button 
          @click="navigateTo('/')"
          class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
          Trở về trang chủ
        </button>
      </div>
    </div>
  </div>
</template>