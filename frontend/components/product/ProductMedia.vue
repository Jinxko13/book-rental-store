<template>
  <div class="productMedia flex flex-col space-y-4" v-if="product">
    <!-- PHẦN GIÁ & TÊN -->
    <div class="productMedia-Content bg-white p-4 sm:p-6 rounded-lg border-gray-200">
      <div class="price-mobile">
        <div class="flex items-center space-x-2">
          <span class="text-price font-bold text-red-600 text-3xl mb-3">{{ product.price.toLocaleString('vi-VN') }} đ</span>
          <span v-if="product.oldPrice" class="text-del line-through text-gray-400 text-md">{{ product.oldPrice }} đ</span>
          <span v-if="product.discount" class="text-voucher bg-red-600 text-white text-md font-bold px-1.5 py-0.5 rounded">
            -{{ product.discount }}%
          </span>
        </div>
      </div>
      <h2 class="title text-2xl font-semibold mb-2">{{ product.title }}</h2>
      <div class="trademark mb-2 text-sm">
        <span class="mr-5">Tác Giả: </span>
        <span class="font-semibold">{{ product.brand || 'N/A' }}</span>
      </div>

      <div class="icon-feedback mb-2">
        <div class="flex items-center space-x-2">
          <a-rate v-if="starCount > 0" :value="rating" :count="starCount" :key="starCount" class="custom-rate" disabled />
          <span class="text-yellow-500 text-sm">({{ ratingCount }} đánh giá)</span>
          <div class="rounded px-2 py-0.5 text-sm leading-none text-black">
            Đã bán {{ sold }}
          </div>
        </div>
      </div>

      <div class="product_view_msg_mobile w-full mt-4 text-sm font-bold bg-blue-50 rounded px-3 py-2 inline-block cursor-pointer transition"
        @click="showStorePopup = true" style="color:#2489f4">
        23 nhà sách còn hàng
      </div>

      <PopupMarket v-model:visible="showStorePopup" :productTitle="product" />
    </div>

    <!-- PHẦN VẬN CHUYỂN -->
    <div class="productMedia-Delivery bg-white p-4 sm:p-6 rounded-lg border-gray-200 text-sm">
      <div class="mb-3">
        <h3 class="font-bold text-base mb-1">Thông tin vận chuyển</h3>
        <p>
          Giao hàng đến <span class="font-semibold text-black">{{ fullAddress }}</span>
          <span class="text-blue-600 cursor-pointer ml-6 font-semibold" @click="showLocation = true">Thay đổi</span>
        </p>
        <PopupLocation v-model:visible="showLocation" @confirm="val => { fullAddress = val.address }" />
        <div class="flex mt-6">
          <div class="text-lg font-bold mr-3"><CarOutlined /></div>
          <div class="expected_delivery">
            <p class="text-green-600 font-semibold mb-1">Giao hàng tiêu chuẩn</p>
            <p class="text-black-600 mb-2">Dự kiến giao <span class="font-semibold">Thứ sáu – 25/04</span></p>
          </div>
        </div>
      </div>

      <!-- ƯU ĐÃI -->
      <div class="mb-5">
        <h4 class="font-bold mb-2">
          <span class="mr-4">Ưu đãi liên quan</span>
          <span class="text-blue-500 text-sm cursor-pointer" @click="showCoupons = true">
            Xem thêm <RightOutlined />
          </span>
        </h4>
        <PopupCoupon v-model:visible="showCoupons" />
        <div class="flex gap-2 flex-wrap">
          <div class="bg-yellow-300 px-2 py-1 rounded text-xs">Mã giảm 50k - tối đa 5%</div>
          <div class="bg-yellow-200 px-2 py-1 rounded text-xs">Mã giảm 100k - tối đa 10%</div>
          <div class="bg-green-400 px-2 py-1 rounded text-xs">Mã giảm 20k - hỗ trợ phí ship</div>
          <div class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Home credit: giảm thêm</div>
        </div>
      </div>

      <!-- SỐ LƯỢNG - Sử dụng v-model để đồng bộ với parent -->
      <div class="flex items-center space-x-2 font-semibold">
        <label class="font-semibold">Số lượng:</label>
        <div class="flex items-center rounded px-2">
          <button @click="decrease" class="px-2 text-lg text-gray-500 hover:text-black cursor-pointer">−</button>
          <span class="px-4 w-6 text-center">{{ localQuantity }}</span>
          <button @click="increase" class="px-2 text-lg text-gray-500 hover:text-black cursor-pointer">+</button>
        </div>
      </div>
      
      <div v-if="showLimitMessage" class="text-red-500 text-sm mt-2">
        Số lượng sách trong kho đã đạt giới hạn
      </div>
    </div>

    <!-- MÔ TẢ -->
    <div class="productDescription bg-white p-4 sm:p-6 rounded-lg border-gray-200 text-sm">
      <h3 class="text-base font-bold mb-4">Mô tả sản phẩm</h3>
      <p class="font-semibold mb-3">{{ product.title }}</p>
      <p>{{ product.description }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watchEffect, onMounted, onBeforeUnmount, watch } from 'vue'
import { CarOutlined, RightOutlined } from '@ant-design/icons-vue'
import PopupMarket from '~/components/product/PopupMarket.vue'
import PopupLocation from '~/components/product/PopupLocation.vue'
import PopupCoupon from '~/components/product/PopupCoupon.vue'

const props = defineProps<{
  product: {
    id: number
    title: string
    price: string
    description: string
    brand?: string
    images?: string[]
    oldPrice?: string
    discount?: number
    quantity?: number
  }
  quantity: number // Nhận quantity từ parent
}>()

// Định nghĩa emit để gửi quantity về parent
const emit = defineEmits<{
  'update:quantity': [value: number]
}>()

const rating = ref(0)
const ratingCount = ref(0)
const starCount = ref(5)
const sold = ref(0)

// Sử dụng localQuantity để quản lý giá trị local
const localQuantity = ref(props.quantity || 1)

// Watch để đồng bộ với prop từ parent
watch(() => props.quantity, (newVal) => {
  localQuantity.value = newVal
}, { immediate: true })

// Watch để emit về parent khi có thay đổi
watch(localQuantity, (newVal) => {
  emit('update:quantity', newVal)
})

const maxQuantity = computed(() => props.product?.quantity || 1)
const showLimitMessage = computed(() => localQuantity.value >= maxQuantity.value)

const showStorePopup = ref(false)
const showLocation = ref(false)
const showCoupons = ref(false)
const fullAddress = ref('Phường Bến Nghé, Quận 1, Hồ Chí Minh')

const increase = () => {
  if (localQuantity.value < maxQuantity.value) {
    localQuantity.value++
  }
}

const decrease = () => {
  if (localQuantity.value > 1) {
    localQuantity.value--
  }
}

watchEffect(() => {
  if (!props.product) return
  sold.value = props.product.quantity || 0
})

const updateRatingResponsive = () => {
  starCount.value = window.innerWidth <= 768 ? 1 : 5
}

onMounted(() => {
  updateRatingResponsive()
  window.addEventListener('resize', updateRatingResponsive)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateRatingResponsive)
})
</script>