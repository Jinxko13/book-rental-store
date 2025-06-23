<template>
  <div class="flex-1">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
      <h2 class="text-2xl font-medium text-gray-800 mb-2 sm:mb-0">
        Tất cả sản phẩm
      </h2>
      <p class="text-base text-gray-600">
        Tìm thấy {{ totalProducts }} cuốn sách
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-10">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
      <p class="text-gray-600 mt-4">Đang tải sách...</p>
    </div>

    <!-- Product Grid -->
    <div v-else-if="products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :id="product.id"
        :title="product.title"
        :price="product.price"
        :image="product.image?.[0]?.url || '/placeholder.jpg'"
        :category="product.category"
        :author="product.author"
        :quantity="product.quantity"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-20">
      <div class="text-gray-400 mb-4">
        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
      </div>
      <p class="text-xl text-gray-600 mb-2">Không tìm thấy sách nào</p>
      <p class="text-gray-500">Hãy thử điều chỉnh bộ lọc hoặc quay lại sau</p>
    </div>

    <!-- Pagination -->
    <div v-if="!isLoading && products.length > 0" class="flex justify-center">
      <a-pagination
        :current="currentPage"
        :total="totalProducts"
        :pageSize="pageSize"
        @change="onPageChange"
        :show-size-changer="false"
        show-quick-jumper
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import ProductCard from './ProductCard.vue'

// Props
const props = defineProps({
  filters: {
    type: Object,
    default: () => ({ categories: [], authors: [] })
  },
  searchQuery: {
    type: String,
    default: ''
  }
})

// Emits
const emit = defineEmits(['products-loaded'])

// Composables
const booksApi = useBooks()
const categoriesApi = useCategories()
const authorsApi = useAuthors()

// State
const products = ref([])
const categories = ref([])
const authors = ref([])
const isLoading = ref(false)
const currentPage = ref(1)
const totalProducts = ref(0)
const pageSize = 12
let debounceTimer = null

// Computed
const hasActiveFilters = computed(() => {
  return props.filters.categories.length > 0 || props.filters.authors.length > 0 || props.filters.priceRange.min !== null || props.filters.priceRange.max !== null
})

// Methods
const fetchProducts = async () => {
  isLoading.value = true
  try {
    const params = {
      limit: pageSize,
      page: currentPage.value
    }

    // Add filters if active
    if (hasActiveFilters.value) {
      if (props.filters.categories.length > 0) {
        params.categories = props.filters.categories.join(',')
      }
      if (props.filters.authors.length > 0) {
        params.authors = props.filters.authors.join(',')
      }
      if (props.filters.priceRange.min !== null || props.filters.priceRange.max !== null) {
        params.price_min = props.filters.priceRange.min
        params.price_max = props.filters.priceRange.max
      }
    }
    
    if (props.searchQuery) {
      params.search = props.searchQuery
    }

    const response = await booksApi.getBooks(params)
    
    products.value = response.data.map(product => ({
      ...product,
      category: getCategoryNames(product.category),
      author: getAuthorName(product.author.id)
    }))
    
    totalProducts.value = response.numberRecord
    
    emit('products-loaded', {
      products: products.value,
      total: totalProducts.value
    })
  } catch (error) {
    console.error('Lỗi khi tải sản phẩm:', error)
    products.value = []
    totalProducts.value = 0
  } finally {
    isLoading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await categoriesApi.getCategories()
    categories.value = response.data 
  } catch (error) {
    console.error('Lỗi khi tải danh mục:', error)
    categories.value = []
  }
}

const fetchAuthors = async () => {
  try {
    const response = await authorsApi.getAuthors()
    authors.value = response.data 
  } catch (error) {
    console.error('Lỗi khi tải tác giả:', error)
    authors.value = []
  }
}

const getCategoryNames = (categoryData) => {
  if (!categoryData) return [];

  let categoryIds = [];

  categoryIds = categoryData.map(cat => cat.id); // Extract IDs from category objects

  return categoryIds.map(id => {
    const category = categories.value.find(cat => cat.id === id);
    return category ? category.name : 'Chưa phân loại';
  }).filter(name => name !== 'Chưa phân loại');
}

const getAuthorName = (authorId) => {
  const author = authors.value.find(auth => auth.id === authorId)
  return author ? author.name : 'Tác giả không rõ'
}

const debouncedFetchProducts = ($delay = 1000) => {
  // Clear existing timer
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
  
  // Set new timer
  debounceTimer = setTimeout(() => {
    fetchProducts()
  }, $delay)
}

const onPageChange = (page) => {
  currentPage.value = page
}

// Watchers
watch(() => props.filters, () => {
  currentPage.value = 1
  debouncedFetchProducts()
}, { deep: true })

watch(() => props.searchQuery, () => {
  currentPage.value = 1
  debouncedFetchProducts()
})

watch(currentPage, fetchProducts)

// Lifecycle
onMounted(async () => {
  await Promise.all([
    fetchCategories(),
    fetchAuthors()
  ])
  await debouncedFetchProducts(5)
})
</script>
