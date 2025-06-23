<template>
  <div class="w-full">
    <!-- Section 1 -->
    <div class="w-full bg-[#0e345a] flex flex-col items-center py-10 lg:py-[50px] px-4">
      <div class="w-full max-w-screen-xl flex flex-col items-center text-center text-white space-y-3 mb-8">
        <p class="font-bold text-2xl sm:text-4xl md:text-6xl lg:text-7xl">
          Cửa hàng cho thuê sách
        </p>
        <h2 class="text-sm sm:text-lg md:text-2xl lg:text-4xl">
          Sách mới nhất
        </h2>
        <div class="w-12 h-0.5 bg-white/50"></div>
      </div>
      <BookSwiper :bookList="newestBook" />
    </div>

    <!-- Section 2 -->
    <div class="w-full bg-[#e7e7e7] flex flex-col items-center py-10 lg:py-[50px] px-4">
      <div class="w-full max-w-screen-xl flex flex-col items-center text-center text-[#0e345a] space-y-3 mb-8">
        <p class="font-bold text-2xl sm:text-4xl md:text-6xl lg:text-7xl">
          Sách được yêu thích nhất
        </p>
        <h2 class="text-sm sm:text-lg md:text-2xl lg:text-4xl">
          Tháng này
        </h2>
        <div class="w-12 h-0.5 bg-black/50"></div>
      </div>
      <BookSwiper :bookList="dataBook" />
    </div>

    <!-- Section 3: CTA -->
    <div class="w-full bg-[#0e345a] text-white flex flex-col items-center text-center px-4 py-10 sm:py-[60px] space-y-6 sm:space-y-8 md:space-y-10">
      <h2 class="text-2xl sm:text-4xl md:text-6xl lg:text-7xl font-bold tracking-wide leading-tight">
        KHÔNG BAO GIỜ LÀ 
        <br class="hidden sm:block" /> 
        QUÁ NHIỀU SÁCH
      </h2>
      <div class="flex justify-center">
        <button class="border-2 border-white text-white px-4 py-1 sm:px-5 sm:py-2 text-sm sm:text-base hover:bg-white hover:text-[#0e345a] transition-all duration-300"
        @click="navigateTo('/about')">
          Câu chuyện của chúng tôi
        </button>
      </div>
      <div class="w-12 h-0.5 bg-white/50"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import BookSwiper from '~/components/book/BookSwiper.vue'
import axios from 'axios'
import { mockBooks } from '~/components/book/MockBook'
import { message } from 'ant-design-vue'

const dataBook = ref(mockBooks)
const newestBook = ref(mockBooks)
const limit = 8
const bookApi = useBooks();

const fetchDataBook = async () => {
  try {
    const res1 = await bookApi.getNewestBook()
    newestBook.value = res1.data.data
    const res2 = await bookApi.getMonthlyBook()
    dataBook.value = res2.data.data
  } catch (err) {
    message.error('Lỗi khi fetch sách')
  }
}

onMounted(() => {
  fetchDataBook()
})
</script>