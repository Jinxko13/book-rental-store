<template>
  <div class="flex justify-center pt-28 pb-28 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-6xl text-center">
      <!-- Title -->
      <h1 class="text-[#0A3251] text-4xl md:text-5xl font-bold uppercase tracking-wide">
        Sách yêu thích tuần
      </h1>
      <p class="text-[#0A3251] text-lg font-medium">Quyển sách tuyệt nhất</p>

      <!-- Content -->
      <div v-if="BookItem" class="flex flex-col lg:flex-row gap-10 mt-14 w-full">
        <!-- Book Info -->
        <div class="w-full lg:w-1/2 text-left space-y-4">
          <h2 class="text-2xl text-[#0A3251] font-semibold">{{ BookItem.title }}</h2>
          <div class="italic text-gray-600 text-base">Bởi {{ BookItem.author.name }}</div>

          <!-- Categories -->
          <div class="space-x-1 text-sm text-gray-500">
            <span
              v-for="(category, index) in BookItem.category"
              :key="category.id"
            >
              {{ category.name }}<span v-if="index < BookItem.category.length - 1">,</span>
            </span>
          </div>

          <!-- Price -->
          <div class="text-2xl font-bold text-[#0A3251] mt-3">
            {{ formatPrice(BookItem.price) }}
          </div>

          <!-- Stock Status -->
          <div
            :class="BookItem.quantity > 0 ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'"
            class="text-sm inline-block rounded px-2 py-1"
          >
            {{ BookItem.quantity > 0 ? 'Còn hàng' : 'Hết hàng' }}
          </div>

          <!-- Description -->
          <div class="mt-5">
            <h3 class="text-base font-semibold text-[#0A3251] mb-2">Mô tả</h3>
            <p class="text-sm text-gray-800 leading-relaxed text-justify">
              {{ BookItem.description }}
            </p>
          </div>

          <!-- Add to Cart Button -->
          <div v-if="BookItem.quantity > 0" class="flex items-center gap-4 mt-8">
            <button
              @click="handleAddToCart"
              class="bg-[#0A3251] text-white font-medium px-5 py-2 rounded hover:bg-[#072238] transition"
            >
              Thêm vào giỏ hàng
            </button>
          </div>
        </div>

        <!-- Book Image -->
        <div class="w-full lg:w-1/2 flex items-center justify-center max-h-[500px]">
          <img
            v-if="BookItem.image?.length"
            :src="BookItem.image[0].url"
            :alt="BookItem.title"
            class="max-w-full max-h-full object-contain"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const productStore = useProductStore()

const props = defineProps({
  BookItem: Object
})

const selectedQuantity = ref(1)

const decreaseQuantity = () => {
  if (selectedQuantity.value > 1) {
    selectedQuantity.value--
  }
}

const increaseQuantity = () => {
  if (selectedQuantity.value < props.BookItem.quantity) {
    selectedQuantity.value++
  }
}

const handleAddToCart = () => {
  const product = {
    id: props.BookItem.id,
    title: props.BookItem.title,
    price: props.BookItem.price,
    quantity: 1,
    image: props.BookItem.image[0].url,
    maxQuantity: props.BookItem.quantity
  }
  try {
    productStore.addToCart(product)
    message.success(`Đã thêm ${product.quantity} sản phẩm ${product.title} vào giỏ hàng!`)
  } catch (error) {
    message.error(error.message)
  }
}
</script>
