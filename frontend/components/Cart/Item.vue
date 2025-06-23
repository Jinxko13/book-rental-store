<template>
  <div class="cart-item bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-300">
    <!-- Mobile Layout -->
    <div class="flex flex-col sm:hidden gap-4">
      <!-- Image and Basic Info -->
      <div class="flex gap-3">
        <div class="flex-shrink-0">
          <img 
            :src="item.image" 
            :alt="item.title" 
            class="w-20 h-20 object-cover rounded-lg border border-gray-100"
            @error="handleImageError"
          />
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="text-sm font-medium text-gray-900 line-clamp-2 leading-tight">
            {{ item.title }}
          </h3>
          <p class="text-lg font-semibold text-blue-600 mt-1">
            {{ formatPrice(item.price) }}
          </p>
        </div>
        <!-- Remove Button (Mobile) -->
        <button
          @click="removeItem"
          class="flex-shrink-0 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors duration-200"
          :title="`Remove ${item.title} from cart`"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
        </button>
      </div>

      <!-- Quantity and Total (Mobile) -->
      <div class="flex items-center justify-between">
        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-gray-50">
          <button
            @click="decrementQuantity"
            :disabled="item.quantity <= 1"
            class="px-3 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors duration-200"
            :title="item.quantity <= 1 ? 'Minimum quantity reached' : 'Decrease quantity'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
          </button>
          <div class="px-4 py-2 bg-white border-x border-gray-300 min-w-[60px] text-center font-medium">
            {{ item.quantity }}
          </div>
          <button
            @click="incrementQuantity"
            :disabled="item.quantity >= maxQuantity"
            class="px-3 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors duration-200"
            :title="item.quantity >= maxQuantity ? 'Số lượng sách đạt tối đa' : 'Tăng số lượng'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
          </button>
        </div>
        <div class="text-right">
          <p class="text-sm text-gray-500">Total</p>
          <p class="text-lg font-bold text-gray-900">{{ formatPrice(itemTotal) }}</p>
        </div>
      </div>
    </div>

    <!-- Desktop Layout -->
    <div class="hidden sm:flex items-center gap-6">
      <!-- Product Image -->
      <div class="flex-shrink-0">
        <img 
          :src="item.image" 
          :alt="item.title" 
          class="w-24 h-24 object-cover rounded-lg border border-gray-100"
          @error="handleImageError"
        />
      </div>

      <!-- Product Info -->
      <div class="flex-1 min-w-0">
        <h3 class="text-base font-medium text-gray-900 line-clamp-2 leading-relaxed">
          {{ item.title }}
        </h3>
        <p class="text-xl font-semibold text-blue-600 mt-2">
          {{ formatPrice(item.price) }}
        </p>
        <!-- <p class="text-sm text-gray-500 mt-1">
          ID: {{ item.id }}
        </p> -->
      </div>

      <!-- Quantity Controls -->
      <div class="flex-shrink-0">
        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-gray-50">
          <button
            @click="decrementQuantity"
            :disabled="item.quantity <= 1"
            class="px-4 py-3 text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors duration-200"
            :title="item.quantity <= 1 ? 'Minimum quantity reached' : 'Decrease quantity'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
          </button>
          <div class="px-6 py-3 bg-white border-x border-gray-300 min-w-[80px] text-center font-medium text-gray-900">
            {{ item.quantity }}
          </div>
          <button
            @click="incrementQuantity"
            :disabled="item.quantity >= maxQuantity"
            class="px-4 py-3 text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors duration-200"
            :title="item.quantity >= maxQuantity ? 'Số lượng sách đạt tối đa' : 'Tăng số lượng'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
          </button>
        </div>
        <p class="text-xs text-gray-500 text-center mt-1">
          Nhiều nhất: {{ maxQuantity }}
        </p>
      </div>

      <!-- Total Price -->
      <div class="flex-shrink-0 text-right min-w-[100px]">
        <p class="text-sm text-gray-500">Total</p>
        <p class="text-xl font-bold text-gray-900">{{ formatPrice(itemTotal) }}</p>
      </div>

      <!-- Remove Button -->
      <div class="flex-shrink-0">
        <a-popconfirm
          title="Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?"
          ok-text="Có"
          cancel-text="Không"
          @confirm="removeItem"
        >
          <button
            class="p-3 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors duration-200"
            :title="`Xóa ${item.title} khỏi giỏ hàng`"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </a-popconfirm>
      </div>
    </div>

    <!-- Stock Warning -->
    <div v-if="item.quantity >= maxQuantity" class="mt-3 p-2 bg-amber-50 border border-amber-200 rounded-lg">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <p class="text-sm text-amber-800">
          Số lượng sách đạt tối đa. Chỉ còn {{ maxQuantity }} sách trong kho.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

// Interface cho cart item
interface CartItem {
  id: string | number
  image: string
  price: number
  quantity: number
  title: string
  maxQuantity?: number
}

// Props
const props = defineProps<{
  item: CartItem
}>()

// Events
const emit = defineEmits<{
  'update:quantity': [id: string | number, quantity: number]
  'remove': [id: string | number]
}>()

// Computed
const maxQuantity = computed(() => props.item.maxQuantity || 10)
const itemTotal = computed(() => props.item.price * props.item.quantity)

// Methods
const incrementQuantity = () => {
  if (props.item.quantity < maxQuantity.value) {
    emit('update:quantity', props.item.id, props.item.quantity + 1)
  }
}

const decrementQuantity = () => {
  if (props.item.quantity > 1) {
    emit('update:quantity', props.item.id, props.item.quantity - 1)
  }
}

const removeItem = () => {
  emit('remove', props.item.id)
}

const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement
  target.src = '/images/placeholder-book.jpg' // Fallback image
}
</script>

<style scoped>
.cart-item {
  transition: all 0.3s ease;
}

.cart-item:hover {
  transform: translateY(-1px);
}

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Smooth transitions for quantity buttons */
button:disabled {
  transition: all 0.2s ease;
}

button:not(:disabled):hover {
  transform: scale(1.05);
}

/* Focus styles for accessibility */
button:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Custom scrollbar for quantity input if needed */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}
</style>