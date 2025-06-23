<template>
  <div class="group bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden h-full flex flex-col">
    <!-- Product Image -->
    <div class="relative overflow-hidden aspect-[3/4] bg-gray-100 flex-shrink-0">
      <img
        :src="image"
        :alt="title"
        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 cursor-pointer"
        @click="handleRoute"
        @error="handleImageError"
      />
      <!-- Quick view button -->
      <button
        class="absolute top-3 right-3 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 opacity-0 group-hover:opacity-100 transition-all duration-300"
        @click="handleQuickView"
      >
        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
      </button>
    </div>

    <!-- Product Details -->
    <div class="p-4 flex flex-col flex-1 min-h-[200px]">
      <h3 class="font-semibold text-gray-800 text-lg mb-2 group-hover:text-blue-600 transition-colors h-14 flex items-start overflow-hidden">
        <span class="line-clamp-2">{{ title }}</span>
      </h3>
      <div class="h-6 mb-2">
        <div v-if="author" class="text-sm text-gray-600 line-clamp-1">
          <span class="font-medium">Tác giả:</span> {{ author }}
        </div>
      </div>
      <div class="h-8 mb-3 overflow-hidden">
        <div v-if="category.length > 0" class="flex flex-wrap gap-1">
          <span
            v-for="(cat, index) in category.slice(0, 2)"
            :key="index"
            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"
          >
            {{ cat }}
          </span>
          <span v-if="category.length > 2" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">
            +{{ category.length - 2 }}
          </span>
        </div>
      </div>
      
      <!-- Price và Quantity -->
      <div class="flex items-center justify-between mb-4 flex-1 items-end">
        <p class="text-xl font-bold text-gray-900">
          {{ formatPrice(price) }}
        </p>
        <div v-if="quantity !== undefined" class="text-sm text-gray-500">
          {{ quantity > 0 ? `Còn ${quantity} cuốn` : 'Hết hàng' }}
        </div>
      </div>

      <!-- Action Button - Luôn ở cuối -->
      <button
        @click="handleAddToCart"
        :disabled="quantity === 0"
        class="w-full py-2 px-4 bg-gray-900 text-white rounded-md hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors duration-200 text-sm font-medium"
      >
        {{ quantity === 0 ? 'Hết hàng' : 'Thêm vào giỏ' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useProductStore } from '~/stores/cart'
import { useRouter } from 'vue-router'

// Props
const props = defineProps({
  id: {
    type: Number,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  price: {
    type: String,
    required: true
  },
  image: {
    type: String,
    required: true
  },
  category: {
    type: Array,
    default: () => []
  },
  author: {
    type: String,
    default: ''
  },
  quantity: {
    type: Number,
    default: undefined
  }
})
// Composables
const productStore = useProductStore()
const router = useRouter()

// State
const imageError = ref(false)

// Methods
const handleRoute = () => {
  router.push(`/product?id=${props.id}`)
}

const handleQuickView = (event) => {
  event.stopPropagation()
  console.log('Xem nhanh sản phẩm:', props.id)
}

const handleAddToCart = () => {
  if (props.quantity === 0) return
  
  const book = {
    id: props.id,
    title: props.title,
    price: props.price,
    image: props.image,
    quantity: 1,
    maxQuantity: props.quantity
  }
  try {
    productStore.addToCart(book)
    message.success(`Đã thêm ${book.quantity} cuốn sách "${book.title}" vào giỏ hàng!`)
  } catch (error) {
    message.error(error.message)
  }
}

const handleImageError = () => {
  imageError.value = true
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>