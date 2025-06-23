<!-- ProductPage.vue (Refactored with quantity limit) -->
<template>
  <div class="min-h-screen p-6 bg-gray-100">
    <div class="container mx-auto space-y-6">
      <Transition name="fade">
        <div v-if="alertMessage" class="flex items-center bg-white border border-green-400 text-green-700 px-4 py-3 rounded shadow" role="alert">
          <span class="mr-2 text-green-500 text-xl">✔️</span>
          <span>{{ alertMessage }}</span>
        </div>
      </Transition>

      <div class="grid grid-cols-1 lg:grid-cols-10 gap-6">
        <div class="lg:col-span-4 rounded border-gray-200">
          <ProductDetail v-if="selected" :product="selected" @add-to-cart="handleAddToCart" />
        </div>
        <div class="lg:col-span-6 rounded border-gray-200">
          <ProductMedia v-if="selected" :product="selected" v-model:quantity="selectedQuantity" />
        </div>
      </div>

      <!-- <div class="bg-white p-6 rounded-lg border border-gray-200 text-sm">
        <h3 class="text-base font-bold mb-4">Đánh giá sản phẩm</h3>
        <div class="flex flex-col md:flex-row items-start">
          <div class="flex flex-col items-center justify-center w-full md:w-1/3 text-center">
            <div class="text-5xl font-bold leading-none mb-1">
              0<span class="text-xl">/5</span>
            </div>
            <a-rate :value="0" disabled allow-half />
            <div class="text-gray-500 text-sm mt-1">(0 đánh giá)</div>
          </div>

          <div class="w-full md:w-2/3 md:pl-10 mt-4 md:mt-0">
            <div v-for="i in 5" :key="i" class="flex items-center mb-2">
              <span class="w-12 text-sm">{{ 6 - i }} sao</span>
              <div class="flex-1">
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-yellow-400" style="width: 0%"></div>
                </div>
              </div>
              <span class="w-10 text-sm text-right ml-2">0%</span>
            </div>
            <div class="text-sm text-gray-600 mt-4 text-center md:text-left">
              Chỉ có thành viên mới có thể viết nhận xét.
              Vui lòng
              <a href="/login" class="text-blue-600 underline">đăng nhập</a>
              hoặc
              <a href="/register" class="text-blue-600 underline">đăng ký</a>.
            </div>
          </div>
        </div>
      </div> -->

      <ProductReviews :book-id="selected?.id" @select="handleSelect" />


      <ProductSlider :products="products" @select="handleSelect" />
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { message } from 'ant-design-vue';
import { useRoute, useRouter } from 'vue-router'
import ProductDetail from '~/components/product/ProductDetail.vue'
import ProductMedia from '~/components/product/ProductMedia.vue'
import ProductSlider from '~/components/product/ProductSlider.vue'
import ProductReviews from '~/components/product/ProductReviews.vue'

const router = useRouter()
const route = useRoute()
const bookApi = useBooks()
const productStore = useProductStore()

const selected = ref(null)
const loading = ref(true)
const selectedQuantity = ref(1)
const alertMessage = ref('')
const successMessage = ref('')

function handleSelect(product) {
  router.push(`/product?id=${product.id}`).then(() => window.location.reload())
}

const handleAddToCart = () => {
  if (!selected.value) return

  const product = {
    id: selected.value.id,
    title: selected.value.title,
    price: selected.value.price,
    quantity: selectedQuantity.value,
    image: selected.value.images[0] || '',
    maxQuantity: selected.value.quantity || 1
  }

  try {
    productStore.addToCart(product)
    message.success(`Đã thêm ${product.quantity} sản phẩm "${selected.value.title}" vào giỏ hàng!`)
  } catch (error) {
    message.error(error.message)
  }
}

watch(() => route.query.id, async (id) => {
  if (!id) return
  loading.value = true
  selectedQuantity.value = 1

  try {
    const raw = await bookApi.getBookById(id)
    selected.value = {
      id: raw.id,
      title: raw.title,
      images: Array.isArray(raw.image) && raw.image.length > 0
        ? raw.image.map(img => img.url)
        : ['https://cdn1.fahasa.com/media/catalog/product/9/7/9786043561272_1_1.jpg'],
      brand: raw.author?.name || 'N/A',
      price: raw.price,
      oldPrice: '',
      discount: 0,
      quantity: raw.quantity || 0,
      specs: {},
      description: raw.description || ''
    }
  } catch (err) {
    console.error('Lỗi khi load sách:', err)
  } finally {
    loading.value = false
  }
}, { immediate: true })

const products = []

onBeforeMount(() => {
  const route = useRoute();
  if (!route.query.id) {
    return navigateTo('/');
  }  
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .min-h-screen.p-6.bg-gray-100 {
    padding: 0;
    box-shadow: none;
    background: #ffffff;
  }
  .grid.grid-cols-1.gap-6 {
    gap: 0 !important;
  }
}
</style>
