<template>
  <div class="bg-white p-4 rounded-lg shadow relative">

    <h2 class="text-base font-bold mb-4">Top Sách Thuê Nhiều Nhất Tuần</h2>
    <Swiper
      :modules="modules"
      :slides-per-view="5"
      :space-between="20"
      :loop="false"
      :navigation="true"
      :breakpoints="{
        0: { slidesPerView: 2 },
        640: { slidesPerView: 3 },
        768: { slidesPerView: 4 },
        1024: { slidesPerView: 5 }
      }"
    >
      <SwiperSlide v-for="item in products" :key="item.id">
        <a
          href="javascript:void(0)"
          @click="$emit('select', item)"
          class="product-item block border border-gray-200 p-2 text-[13px] rounded hover:shadow transition duration-300 bg-white"
        >
          <img
            :src="item.image"
            alt="product"
            class="product-image w-full h-[250px] object-contain"
          />
          <div class="product-title mt-2 h-[36px] leading-snug line-clamp-2 text-[#333] font-normal">
            {{ item.name }}
          </div>
          <div class="product-price flex items-center gap-2 mt-1">
            <span class="text-[#C92127] font-bold text-sm">{{ item.price }} đ</span>
            <span class="bg-[#C92127] text-white text-[11px] font-medium rounded leading-none">
              -{{ item.discount }}%
            </span>
          </div>
          <div class="price-old text-gray-400 text-xs line-through">{{ item.oldPrice }} đ</div>
          <div class="product-star text-[#666] text-xs mt-1">★★★★★ (0)</div>
          <div class="text-xs text-[#888]">Lượt thuê: {{ item.totalRented }}</div>
        </a>
      </SwiperSlide>
    </Swiper>

    <!-- Xem thêm -->
    <div class="text-center mt-4">
      <a href="#" class="product-show hover:!text-white hover:!bg-[#C92127] rounded">Xem thêm</a>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import axios from 'axios'

defineEmits(['select'])

const products = ref<any[]>([])
const range = ref(['', ''])

const fetchWeeklyBooks = async () => {
  try {
    const res = await axios.get('http://localhost:8081/api/top-book-week/recent')

    range.value = res.data.range || ['', '']

    const rawData = res.data.data

      products.value = res.data.data.map((item: any) => {
        const price = parseFloat(item.price)
        const discount = parseFloat(item.discount_percent ?? 0)
        const finalPrice = price * (1 - discount / 100)

        return {
          id: item.id,
          name: item.title,
          image: item.image || '/default-book.png',
          price: finalPrice,
          oldPrice: discount > 0 ? price : '',
          discount: discount,
          totalRented: parseInt(item.total_rented ?? '0'),
          category: item.category ?? '',
        }
      })
    } catch (err) {
    console.error('Lỗi tải top book 7 ngày gần nhất:', err)
  }
}

onMounted(() => {
  fetchWeeklyBooks()
})

const modules = [Navigation]
</script>

<style scoped>
.product-item {
  text-decoration: none;
}
.product-title,
.price-old {
  font-size: 14px;
  margin-top: 5px;
}
.product-price span:nth-child(1) {
  line-height: 1.25;
  font-size: 16px !important;
  color: #C92127;
  font-weight: 600;
}
.product-price span:nth-child(2) {
  line-height: 1.25;
  font-size: 16px !important;
  background-color: #C92127;
  font-weight: 600;
  color: #fff;
  padding: 4px 5px;
}
.product-star {
  font-size: 14px;
}
.product-show {
  display: inline-block;
  text-decoration: none;
  padding: 10px 60px;
  text-align: center;
  border: 1px solid #C92127;
  font-size: 14px;
  color: #000;
  background-color: #fff;
  font-weight: 700;
}
:deep(.swiper) {
  position: relative;
  z-index: 0;
}
:deep(.swiper-button-prev),
:deep(.swiper-button-next) {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  background-color: #fff;
  border: 1px solid #ccc;
  width: 40px;
  height: 40px;
  border-radius: 9999px;
  color: #333;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease-in-out;
}
:deep(.swiper-button-prev) {
  left: -2%;
}
:deep(.swiper-button-next) {
  right: -2%;
}
:deep(.swiper-button-prev:hover),
:deep(.swiper-button-next:hover) {
  border-color: #C92127;
  color: #C92127;
}
:deep(.swiper-button-prev::after),
:deep(.swiper-button-next::after) {
  font-size: 12px;
  font-weight: 600;
}
</style>