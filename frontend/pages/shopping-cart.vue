<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="mb-8">
        <ol class="flex items-center space-x-4 text-sm">
          <li>
            <NuxtLink to="/" class="text-gray-500 hover:text-gray-700">Trang chủ</NuxtLink>
          </li>
          <li class="flex items-center">
            <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Giỏ hàng</span>
          </li>
        </ol>
      </nav>

      <!-- Main Content -->
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Cart Items Section -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Cart Header -->
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">
                  Giỏ hàng của bạn
                </h1>
                <div class="text-sm text-gray-500">
                  {{ productStore.totalQuantity }} sản phẩm
                </div>
              </div>
            </div>

            <!-- Empty Cart State -->
            <div v-if="productStore.products.length === 0" class="p-12 text-center">
              <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.35 5.4M7 13h10m0 0v6a2"></path>
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">
                Giỏ hàng trống
              </h3>
              <p class="text-gray-500 mb-6">
                Bạn chưa có sản phẩm nào trong giỏ hàng. Hãy khám phá và thêm sách yêu thích của bạn!
              </p>
              <NuxtLink 
                to="/bookstore" 
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Khám phá sách
              </NuxtLink>
            </div>

            <!-- Cart Items -->
            <div v-else>
              <!-- Select All Section -->
              <div class="p-4 border-b border-gray-100 bg-gray-50">
                <label class="flex items-center cursor-pointer">
                  <input 
                    type="checkbox" 
                    :checked="allSelected" 
                    @change="toggleSelectAll"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                  />
                  <span class="ml-3 text-sm font-medium text-gray-700">
                    Chọn tất cả ({{ productStore.products.length }} sản phẩm)
                  </span>
                </label>
              </div>

              <!-- Items List -->
              <div class="divide-y divide-gray-100">
                <div 
                  v-for="item in productStore.products" 
                  :key="item.id" 
                  class="p-4 hover:bg-gray-50 transition-colors duration-150"
                >
                  <div class="flex items-start gap-4">
                    <!-- Checkbox -->
                    <div class="pt-2">
                      <input 
                        type="checkbox" 
                        :id="'item-' + item.id" 
                        :checked="productStore.checkedId.includes(item.id)"
                        @change="productStore.updateCheckedId(item.id)"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                      />
                    </div>

                    <!-- Cart Item Component -->
                    <div class="flex-1">
                      <CartItem 
                        :item="item" 
                        @update:quantity="updateQuantity" 
                        @remove="removeItem" 
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Continue Shopping -->
          <div v-if="productStore.products.length > 0" class="mt-6">
            <NuxtLink 
              to="/bookstore" 
              class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
              Tiếp tục mua sắm
            </NuxtLink>
          </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div v-if="productStore.products.length > 0" class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-8">
            <!-- Summary Header -->
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">
                Tổng quan đơn hàng
              </h2>
            </div>

            <!-- Summary Content -->
            <div class="p-6 space-y-4">
              <!-- Selected Items Info -->
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Sản phẩm đã chọn:</span>
                <span class="font-medium">{{ selectedItemsCount }} / {{ productStore.products.length }}</span>
              </div>

              <!-- Subtotal -->
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tiền cọc tạm tính:</span>
                <span class="font-medium">{{ formatPrice(selectedSubtotal) }}</span>
              </div>

              <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between">
                  <span class="text-base font-semibold text-gray-900">Tổng cộng:</span>
                  <span class="text-xl font-bold text-blue-600">{{ formatPrice(selectedSubtotal) }}</span>
                </div>
              </div>
            </div>

            <!-- Checkout Actions -->
            <div class="p-6 border-t border-gray-200 space-y-3">
              <button 
                @click="checkoutSelected"
                :disabled="selectedItemsCount === 0"
                class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors duration-200 flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Thuê ({{ selectedItemsCount }} sản phẩm)
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 flex items-center gap-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="text-gray-700">{{ loadingMessage }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

// Page metadata
useHead({
  title: 'Giỏ hàng - BINK Publishers',
  meta: [
    { name: 'description', content: 'Xem và quản lý các sản phẩm trong giỏ hàng của bạn tại BINK Publishers' }
  ]
})

// Store
const productStore = useProductStore()
console.log(productStore.products)
// State
const loading = ref(false)
const loadingMessage = ref('')
const router = useRouter()

// Computed properties
const selectedItemsCount = computed(() => {
  return productStore.checkedId.length
})

const selectedSubtotal = computed(() => {
  return productStore.products.reduce((total, item) => {
    return productStore.checkedId.includes(item.id) ? total + (item.price * item.quantity) : total
  }, 0)
})

const allSelected = computed(() => {
  return productStore.products.length > 0 && productStore.checkedId.length === productStore.products.length
})

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    // Unselect all
    productStore.checkedId = []
  } else {
    // Select all
    productStore.checkedId = productStore.products.map(item => item.id)
  }
}

const updateQuantity = (itemId, newQuantity) => {
  const item = productStore.products.find(item => item.id === itemId)
  if (item) {
    const maxQty = item.maxQuantity || 10
    if (newQuantity > maxQty) {
      // Assuming you have a message utility
      console.warn(`Chỉ còn tối đa ${maxQty} cuốn "${item.title}" có thể mua.`)
      return
    }
    if (newQuantity < 1) {
      console.warn('Số lượng phải lớn hơn 0.')
      return
    }
    
    productStore.updateQuantity(itemId, newQuantity)
  }
}

const removeItem = (itemId) => {
  const item = productStore.products.find(item => item.id === itemId)
  if (!item) {
    console.error(`Không tìm thấy sản phẩm có ID: ${itemId}`)
    return
  }
  productStore.removeProduct(itemId)
  message.success(`Đã xóa ${item.title} khỏi giỏ hàng!`)
}

const checkoutSelected = async () => {
  if (selectedItemsCount.value === 0) {
    console.warn('Vui lòng chọn ít nhất một sản phẩm để thanh toán!')
    return
  }

  loading.value = true
  loadingMessage.value = 'Đang chuẩn bị đơn hàng...'
  
  try {
    const selectedItems = productStore.products.filter(item => productStore.checkedId.includes(item.id))
    
    loadingMessage.value = 'Đang chuyển đến trang thanh toán...'
    await navigateTo('/checkout')
  } catch (error) {
    // console.error('Checkout error:', error)
    message.error('Có lỗi xảy ra. Vui lòng thử lại.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Custom scrollbar for cart items */
.max-h-\[400px\]::-webkit-scrollbar {
  width: 6px;
}

.max-h-\[400px\]::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.max-h-\[400px\]::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}

.max-h-\[400px\]::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Animation for loading states */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.cart-item {
  animation: fadeIn 0.3s ease-out;
}

/* Mobile optimizations */
@media (max-width: 640px) {
  .sticky {
    position: relative;
    top: 0;
  }
  
  .lg\:col-span-3 {
    grid-template-columns: 1fr;
  }
  
  .p-6 {
    padding: 1rem;
  }
  
  .p-4 {
    padding: 0.75rem;
  }
}
</style>