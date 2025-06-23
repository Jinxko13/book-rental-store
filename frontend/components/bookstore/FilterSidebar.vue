<!-- Filter Sidebar Component -->
<template>
  <aside class="w-full lg:w-[250px] shrink-0 lg:pl-5">
    <div class="bg-white lg:bg-transparent p-4 lg:p-0 rounded-lg lg:rounded-none shadow-sm lg:shadow-none">
      <!-- Title -->
      <div class="text-xl mb-6 font-medium text-gray-800">
        <h3>Lọc theo</h3>
      </div>

      <div class="mb-6 border-b border-gray-200 pb-4 px-4 lg:px-0">
        <div class="flex justify-between items-center mb-4">
          <h4 class="text-base text-gray-800 font-medium">Giá</h4>
          <button
            class="text-xl text-gray-500 hover:text-gray-700"
            @click="toggleSection('price')"
          >
          <svg 
            class="w-4 h-4 transition-transform duration-200" 
            :class="{ 'rotate-180': isPriceOpen }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
          </button>
        </div>
        <div v-show="isPriceOpen" class="space-y-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Giá thấp nhất (₫)</label>
            <input
              type="number"
              v-model.number="priceRange.min"
              placeholder="0"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="applyFilters"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Giá cao nhất (₫)</label>
            <input
              type="number"
              v-model.number="priceRange.max"
              placeholder="Không giới hạn"
              min="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="applyFilters"
            />
          </div>
          <div class="space-y-2">
            <button
              v-for="range in priceRanges"
              :key="range.label"
              @click="setPriceRange(range.min, range.max)"
              class="w-full text-left px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-md border border-gray-200"
            >
              {{ range.label }}
            </button>
          </div>
          <button
            @click="clearPriceRange"
            class="w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md border border-red-200"
          >
            Xóa bộ lọc giá
          </button>
        </div>
      </div>
      <!-- Category Filter -->
      <div class="mb-6 border-b border-gray-200 pb-4">
        <div class="flex justify-between items-center mb-4">
          <h4 class="text-base text-gray-800 font-medium">Thể loại</h4>
          <button
            class="text-xl text-gray-500 hover:text-gray-700 transition-colors"
            @click="toggleSection('category')"
          >
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': isCategoryOpen }"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
        </div>
        
        <transition name="fade">
          <div v-show="isCategoryOpen" class="space-y-3">
            <div
              v-for="category in categories"
              :key="category.id"
              class="flex items-center"
            >
              <label class="flex items-center cursor-pointer select-none hover:bg-gray-50 p-1 rounded transition-colors">
                <input
                  type="checkbox"
                  v-model="selectedCategories[category.id]"
                  class="mr-3 text-blue-600 focus:ring-blue-500"
                  @change="applyFilters"
                />
                <span class="text-sm text-gray-700">{{ category.name }}</span>
              </label>
            </div>
          </div>
        </transition>
      </div>

      <!-- Author Filter -->
      <div class="mb-6 border-b border-gray-200 pb-4">
        <div class="flex justify-between items-center mb-4">
          <h4 class="text-base text-gray-800 font-medium">Tác giả</h4>
          <button
            class="text-xl text-gray-500 hover:text-gray-700 transition-colors"
            @click="toggleSection('author')"
          >
            <svg 
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': isAuthorOpen }"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
        </div>
        
        <transition name="fade">
          <div v-show="isAuthorOpen" class="space-y-3">
            <div
              v-for="author in authors"
              :key="author.id"
              class="flex items-center"
            >
              <label class="flex items-center cursor-pointer select-none hover:bg-gray-50 p-1 rounded transition-colors">
                <input
                  type="checkbox"
                  v-model="selectedAuthors[author.id]"
                  class="mr-3 text-blue-600 focus:ring-blue-500"
                  @change="applyFilters"
                />
                <span class="text-sm text-gray-700">{{ author.name }}</span>
              </label>
            </div>
          </div>
        </transition>
      </div>

      <!-- Clear Filters Button -->
      <div v-if="hasActiveFilters" class="mt-6">
        <button
          @click="clearFilters"
          class="w-full px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded transition-colors"
        >
          Xóa tất cả bộ lọc
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'

// Props
const props = defineProps({
  initialCategoryId: {
    type: [Number, null],
    default: null
  }
})

// Emits
const emit = defineEmits(['filter-changed'])

// Composables
const categoriesApi = useCategories()
const authorsApi = useAuthors()

// State
const categories = ref([])
const authors = ref([])
const selectedCategories = reactive({})
const selectedAuthors = reactive({})
const priceRange = reactive({
  min: null,
  max: null
});

const isCategoryOpen = ref(false)
const isAuthorOpen = ref(false)
const isPriceOpen = ref(false)


// Computed
const hasActiveFilters = computed(() => {
  const selectedCats = getSelectedCategoryIds()
  const selectedAuths = getSelectedAuthorIds()
  const selectedPrices = priceRange.min !== null || priceRange.max !== null
  return selectedCats.length > 0 || selectedAuths.length > 0 || selectedPrices
})

// Methods
const toggleSection = (section) => {
  if (section === 'category') {
    isCategoryOpen.value = !isCategoryOpen.value
  } else if (section === 'author') {
    isAuthorOpen.value = !isAuthorOpen.value
  } else if (section === 'price') {
    isPriceOpen.value = !isPriceOpen.value
  }
}

const getSelectedCategoryIds = () => {
  return Object.entries(selectedCategories)
    .filter(([_, selected]) => selected)
    .map(([id]) => parseInt(id))
}

const getSelectedAuthorIds = () => {
  return Object.entries(selectedAuthors)
    .filter(([_, selected]) => selected)
    .map(([id]) => parseInt(id))
}

const applyFilters = () => {
  const filters = {
    categories: getSelectedCategoryIds(),
    authors: getSelectedAuthorIds(),
    priceRange: priceRange
  }
  emit('filter-changed', filters)
}

const clearPriceRange = () => {
  priceRange.min = null
  priceRange.max = null
}

const clearFilters = () => {
  // Reset all selections
  categories.value.forEach(category => {
    selectedCategories[category.id] = false
  })
  authors.value.forEach(author => {
    selectedAuthors[author.id] = false
  })
  clearPriceRange()
  applyFilters()
}

const setInitialCategory = (categoryId) => {
  if (categoryId && selectedCategories.hasOwnProperty(categoryId)) {
    selectedCategories[categoryId] = true
    // Emit ngay lập tức để cập nhật filter
    nextTick(() => {
      applyFilters()
    })
  }
}

const fetchCategories = async () => {
  try {
    const response = await categoriesApi.getCategories()
    categories.value = response.data || response
    
    // Initialize reactive objects sau khi có dữ liệu
    categories.value.forEach(category => {
      selectedCategories[category.id] = false
    })
    
    // Áp dụng initial category nếu có
    if (props.initialCategoryId) {
      setInitialCategory(props.initialCategoryId)
    }
  } catch (error) {
    console.error('Lỗi khi tải danh mục:', error)
    // Fallback categories
    categories.value = [
      { id: 1, name: 'Tiểu thuyết' },
      { id: 2, name: 'Thiết kế & Nghệ thuật' },
      { id: 3, name: 'Phong cách sống' },
      { id: 4, name: 'Sách của tháng' },
      { id: 5, name: 'Bán chạy nhất' },
      { id: 6, name: 'Hướng dẫn du lịch' }
    ]
    
    // Initialize với fallback data
    categories.value.forEach(category => {
      selectedCategories[category.id] = false
    })
    
    if (props.initialCategoryId) {
      setInitialCategory(props.initialCategoryId)
    }
  }
}

const fetchAuthors = async () => {
  try {
    const response = await authorsApi.getAuthors()
    authors.value = response.data || response
  } catch (error) {
    console.error('Lỗi khi tải tác giả:', error)
    // Fallback authors
    authors.value = [
      { id: 1, name: 'Nguyễn Nhật Ánh' },
      { id: 2, name: 'Tô Hoài' },
      { id: 3, name: 'Ngô Tất Tố' },
      { id: 4, name: 'Vũ Trọng Phụng' },
      { id: 5, name: 'Nguyễn Du' }
    ]
  }
  
  // Initialize reactive objects
  authors.value.forEach(author => {
    selectedAuthors[author.id] = false
  })
}

// Watch initialCategoryId prop changes
watch(() => props.initialCategoryId, (newCategoryId, oldCategoryId) => {
  if (newCategoryId !== oldCategoryId) {
    // Reset previous selection nếu có
    if (oldCategoryId && selectedCategories.hasOwnProperty(oldCategoryId)) {
      selectedCategories[oldCategoryId] = false
    }
    
    // Set new selection
    if (newCategoryId) {
      setInitialCategory(newCategoryId)
    }
  }
})

watch(hasActiveFilters, () => {
  const activeCats = getSelectedCategoryIds()
  if (activeCats.length > 0){
    isCategoryOpen.value = true
  }
  const activeAuths = getSelectedAuthorIds()
  if (activeAuths.length > 0){
    isAuthorOpen.value = true
  }
  const activePrices = priceRange.min !== null || priceRange.max !== null
  if (activePrices){
    isPriceOpen.value = true
  }
})

// Lifecycle
onMounted(async () => {
  await Promise.all([fetchCategories(), fetchAuthors()])
})

</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>