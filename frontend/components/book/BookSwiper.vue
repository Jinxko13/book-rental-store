<!-- components/book/BookSwiper.vue -->
<template>
  <Swiper
    v-if="bookList?.length"
    :modules="[Navigation]"
    :slides-per-view="2"
    :space-between="12"
    :breakpoints="swiperBreakpoints"
    navigation
    class="swiper-item w-full max-w-screen-xl px-4"
  >
    <SwiperSlide
      v-for="(book, index) in bookList"
      :key="book.id || index"
      class="!h-auto"
    >
      <a-card
        hoverable
        class="card-item flex flex-col w-full h-full bg-[#0e345a] text-white text-center"
        :body-style="{
          padding: '8px',
          display: 'flex',
          flexDirection: 'column',
          justifyContent: 'space-between'
        }"
        :style="{ border: 'none' }"
      >
        <template #cover>
          <div class="h-[260px] sm:h-[280px] md:h-[300px] overflow-hidden">
            <img
              @click="handleRoute(book.id)"
              :src="book.book_image?.[0]?.image_url || defaultImage"
              alt="Book Cover"
              class="w-full h-full object-cover cursor-pointer"
            />
          </div>
        </template>

        <a-card-meta>
          <template #title>
            <div class="text-[#0e345a] text-sm font-semibold line-clamp-2">
              {{ book.title }}
            </div>
          </template>
          <template #description>
            <div class="text-[#0e345a] text-sm mt-1">
              {{ formatPrice(book.price) }}
            </div>
          </template>
        </a-card-meta>

        <a-button
          block
          class="mt-4 border border-white text-white hover:text-[#0e345a] hover:bg-white transition-all duration-300"
          @click="handleAddToCart(book)"
        >
          Thêm vào giỏ hàng
        </a-button>
      </a-card>
    </SwiperSlide>
  </Swiper>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import { useProductStore } from '~/stores/cart'

const props = defineProps({
  bookList: { type: Array, required: true }
})

const productStore = useProductStore()

const defaultImage =
  'https://cdn1.fahasa.com/media/catalog/product/9/7/9786043561272_1_1.jpg'

const swiperBreakpoints = {
  640: { slidesPerView: 3, spaceBetween: 12 },
  1024: { slidesPerView: 5, spaceBetween: 16 }
}

function handleRoute(id) {
  window.location.href = `/product?id=${id}`
}

function handleAddToCart(book) {
  const product = {
    id: book.id,
    title: book.title,
    price: book.price,
    quantity: 1,
    image: book.book_image?.[0]?.image_url || defaultImage,
    maxQuantity: book.quantity
  }
  try {
    productStore.addToCart(product)
    message.success(`Đã thêm ${product.quantity} sản phẩm ${book.title} vào giỏ hàng!`)
  } catch (error) {
    message.error(error.message)
  }
}
</script>

<style scoped>
.card-item{
  padding: 8px 8px 0 8px;
}
.swiper-item{
  padding-bottom: 20px;
}
</style>