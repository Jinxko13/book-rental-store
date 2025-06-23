<template>
  <header class="top-0 left-0 right-0 z-[1000] bg-white py-3 shadow">
    <div class="w-full mx-auto px-6 md:px-12 flex justify-between items-center">
      <!-- Logo Area -->
      <div class="flex items-center">
        <NuxtLink to="/" class="flex items-center bg-blue-900">
          <div class="bg-blue-900 text-white py-2 px-4 font-bold text-xl">BINK.</div>
          <div class="border bg-white border-gray-300 border-l-0 py-2 px-4 text-gray-700 text-sm uppercase">PUBLISHERS</div>
        </NuxtLink>
      </div>

      <!-- Navigation and Right Area -->
      <div class="flex items-center">
        <!-- Navigation Links -->
        <nav class="hidden md:flex gap-7 text-lg">
          <!-- Library Dropdown -->
          <div class="relative">
            <button 
              @click="toggleLibraryDropdown"
              class="text-gray-800 hover:text-blue-900 flex items-center gap-1 focus:outline-none"
            >
              <span>Thư viện sách</span>
              <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': showLibraryDropdown }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            
            <!-- Library Dropdown Menu -->
            <div 
              v-if="showLibraryDropdown"
              ref="libraryDropdownRef"
              class="absolute left-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50 transition-all duration-200 transform origin-top"
              :class="showLibraryDropdown ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
            >
              <div class="py-2">
                <div class="px-4 py-2 text-sm font-semibold text-gray-500 uppercase tracking-wider border-b">
                  Danh mục sách
                </div>
                <NuxtLink 
                  v-for="category in categories" 
                  :key="category.id"
                  :to="`/bookstore?category=${category.name}`" 
                  class="block px-4 py-3 text-gray-800 hover:bg-blue-50 hover:text-blue-900 transition-colors duration-150"
                  @click="closeLibraryDropdown"
                >
                  <div class="flex items-center gap-3">
                    <div>
                      <div class="font-medium">{{ category.name }}</div>
                      <div class="text-xs text-gray-500">{{ category.books_count }} cuốn sách</div>
                    </div>
                  </div>
                </NuxtLink>
                <div class="border-t mt-2 pt-2">
                  <NuxtLink 
                    to="/bookstore" 
                    class="block px-4 py-2 text-blue-600 hover:bg-blue-50 font-medium"
                    @click="closeLibraryDropdown"
                  >
                    Xem tất cả sách →
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>

          <NuxtLink to="/about" class="text-gray-800 hover:text-blue-900">Giới thiệu</NuxtLink>
          <NuxtLink to="/contact" class="text-gray-800 hover:text-blue-900">Liên hệ</NuxtLink>
        </nav>

        <!-- Login/Profile and Cart -->
        <div class="flex items-center gap-6 ml-8 relative">
          <!-- Nếu chưa đăng nhập -->
          <NuxtLink 
            v-if="!isAuthenticated" 
            to="/login" 
            class="text-gray-800 hover:text-blue-900 flex items-center gap-2 transition-colors duration-150"
          >
            <span class="text-xl">👤</span>
            <span>Đăng nhập</span>
          </NuxtLink>

          <!-- Nếu đã đăng nhập -->
          <div v-else class="relative">
            <button 
              @click="toggleProfileDropdown"
              class="flex items-center gap-2 text-gray-800 hover:text-blue-900 focus:outline-none transition-colors duration-150"
            >
              <span class="text-xl">👤</span>
              <span>{{ user.name ?? 'Người dùng A' }}</span>
              <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': showProfileDropdown }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>

            <!-- Profile Dropdown Menu -->
            <div 
              v-if="showProfileDropdown"
              ref="profileDropdownRef"
              class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50 transition-all duration-200 transform origin-top-right"
              :class="showProfileDropdown ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
            >
              <div class="py-2">
                <div class="px-4 py-2 border-b">
                  <div class="font-medium text-gray-900">{{ user.name ?? 'Người dùng A' }}</div>
                  <div class="text-sm text-gray-500">{{ user.email ?? 'user@example.com' }}</div>
                </div>
                
                <NuxtLink 
                  to="/profile" 
                  class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:bg-gray-50 transition-colors duration-150"
                  @click="closeProfileDropdown"
                >
                  <span class="text-lg">⚙️</span>
                  <span>Cài đặt</span>
                </NuxtLink>
                
                <NuxtLink 
                  to="/orders" 
                  class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:bg-gray-50 transition-colors duration-150"
                  @click="closeProfileDropdown"
                >
                  <span class="text-lg">📦</span>
                  <span>Đơn hàng</span>
                </NuxtLink>
                
                <div class="border-t mt-2 pt-2">
                  <button 
                    @click="handleLogout"
                    class="flex items-center gap-3 w-full px-4 py-3 text-red-600 hover:bg-red-50 transition-colors duration-150"
                  >
                    <span class="text-lg">🚪</span>
                    <span>Đăng xuất</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Cart Icon -->
          <NuxtLink 
            to="/shopping-cart" 
            class="relative text-gray-800 hover:text-blue-900 transition-colors duration-150"
          >
            <span class="text-xl">🛒</span>
            <span 
              v-if="cartQuantity > 0"
              class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse"
            >
              {{ cartQuantity }}
            </span>
          </NuxtLink>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const authStore = useAuthStore();
const productStore = useProductStore();
const categoriesApi = useCategories();
const { isAuthenticated, user } = storeToRefs(authStore);
const cartQuantity = computed(() => productStore.totalQuantity);

// Categories data for Library dropdown
const categories = ref([])

// Library dropdown logic
const showLibraryDropdown = ref(false)
const libraryDropdownRef = ref(null)

const toggleLibraryDropdown = () => {
  showLibraryDropdown.value = !showLibraryDropdown.value
  // Đóng profile dropdown nếu đang mở
  if (showLibraryDropdown.value) {
    showProfileDropdown.value = false
  }
}

const closeLibraryDropdown = () => {
  showLibraryDropdown.value = false
}

// Profile dropdown logic
const showProfileDropdown = ref(false)
const profileDropdownRef = ref(null)

const toggleProfileDropdown = () => {
  showProfileDropdown.value = !showProfileDropdown.value
  // Đóng library dropdown nếu đang mở
  if (showProfileDropdown.value) {
    showLibraryDropdown.value = false
  }
}

const closeProfileDropdown = () => {
  showProfileDropdown.value = false
}

// Handle logout
const handleLogout = async () => {
  try {
    closeProfileDropdown()
    navigateTo('/logout')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

// Handle click outside to close dropdowns
const handleClickOutside = (event) => {
  // Check library dropdown
  if (libraryDropdownRef.value && !libraryDropdownRef.value.contains(event.target)) {
    const libraryButton = event.target.closest('button')
    if (!libraryButton || !libraryButton.textContent.includes('Thư viện sách')) {
      showLibraryDropdown.value = false
    }
  }

  // Check profile dropdown
  if (profileDropdownRef.value && !profileDropdownRef.value.contains(event.target)) {
    const profileButton = event.target.closest('button')
    if (!profileButton || !profileButton.textContent.includes(user.value?.name || 'User')) {
      showProfileDropdown.value = false
    }
  }
}

// Handle escape key to close dropdowns
const handleEscapeKey = (event) => {
  if (event.key === 'Escape') {
    showLibraryDropdown.value = false
    showProfileDropdown.value = false
  }
}

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await categoriesApi.getCategories()
    categories.value = response.data
  } catch (error) {
    console.error('Failed to fetch categories:', error)
    categories.value = [
      { id: 1, name: 'Fiction', count: 245 },
      { id: 2, name: 'Non-Fiction', count: 189 },
      { id: 3, name: 'Science', count: 156 },
      { id: 4, name: 'Technology', count: 134 },
      { id: 5, name: 'History', count: 98 },
      { id: 6, name: 'Biography', count: 87 },
      { id: 7, name: 'Business', count: 76 },
      { id: 8, name: 'Arts', count: 65 }
    ]
  }
}

// Add event listeners
onMounted(async () => {
  productStore.initializeFromLocalStorage()
  await fetchCategories()
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscapeKey)
})
</script>