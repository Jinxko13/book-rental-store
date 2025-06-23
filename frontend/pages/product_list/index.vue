<template>
  <div class="min-h-screen p-6 bg-gray-100">
    <div class="container mx-auto space-y-6">
        <Product_list :products="products" @select="selected = $event" />
    </div>
  </div>
</template>

<script setup lang="ts">
import Product_list from '~/components/product_list/Product_list.vue'
</script>