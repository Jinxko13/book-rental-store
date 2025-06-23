<template>
  <div class="flex-1">
    <!-- Heading -->
    <div class="text-2xl mb-8 font-medium text-gray-800 flex justify-start">
      <p>All Products</p>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 mb-10">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :id="product.id"
        :title="product.title"
        :price="product.price"
        :image="product.image?.[0]?.url || '/placeholder.jpg'"
      />
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
      <a-pagination
        :current="currentPage"
        :total="numberRecord"
        :pageSize="pageSize"
        @change="onPageChange"
        :show-size-changer="false"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import ProductCard from './ProductCard.vue';
import axios from 'axios';

// Khai báo reactive state
const products = ref([]);
const numberRecord = ref(0);
const currentPage = ref(1);
const pageSize = 8;

// Hàm gọi API
const fetchProducts = async () => {
  try {
    const response = await axios.get(`http://localhost:8081/api/books?limit=${pageSize}&page=${currentPage.value}`);
    const result = response.data;

    console.log('API result:', result);

    products.value = result.BookList || []; // fallback nếu undefined
    numberRecord.value = result.numberRecord || 0;
  } catch (error) {
    console.error('Error fetching products:', error);
  }
};

// Gọi API khi component mounted
onMounted(fetchProducts);

// Gọi lại API khi chuyển trang
watch(currentPage, fetchProducts);

// Hàm xử lý chuyển trang
const onPageChange = (page) => {
  currentPage.value = page;
};
</script>
