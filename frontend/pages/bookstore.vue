<template>
  <div class="store-hero">
    <div class="hero-content">
      <h2 class="subtitle">Cửa hàng</h2>
      <h1 class="title">SÁCH ONLINE</h1>
    </div>
    
    <!-- Search Bar -->
    <div class="search-container">
      <div class="search-wrapper">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm kiếm sách theo tên, tác giả..."
            class="search-input"
            @input="handleSearchInput"
            @keyup.enter="handleSearch"
          />
          <div class="search-icon">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          
          <!-- Clear search button -->
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="clear-search-btn"
          >
            <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <!-- Search suggestions or quick filters -->
        <div v-if="showSuggestions && searchQuery" class="search-suggestions">
          <div class="suggestion-item" @click="handleQuickSearch('tiểu thuyết')">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Tìm kiếm "{{ searchQuery }}" trong tiểu thuyết
          </div>
          <div class="suggestion-item" @click="handleQuickSearch('tác giả')">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Tìm kiếm "{{ searchQuery }}" trong tác giả
          </div>
        </div>
      </div>
    </div>
    
    <div class="store-content">
      <FilterSidebar 
        ref="filterSidebar"
        :initial-category-id="initialCategoryId"
        @filter-changed="handleFilterChange"
      />
      <BookList 
        :filters="activeFilters"
        :search-query="searchQuery"
        @products-loaded="handleProductsLoaded"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted, onBeforeMount } from 'vue'
import FilterSidebar from '~/components/bookstore/FilterSidebar.vue'
import BookList from '~/components/bookstore/BookList.vue'

const route = useRoute()
const categoriesApi = useCategories()

const filterSidebar = ref(null)
const searchQuery = ref('')
const showSuggestions = ref(false)
const cats = ref([])
const initialCategoryId = ref(null)

let searchTimeout: NodeJS.Timeout | null = null

const activeFilters = reactive({
  categories: [],
  authors: [],
  search: '',
  priceRange: {
    min: null,
    max: null
  }
})

const handleFilterChange = (filters: any) => {
  activeFilters.categories = filters.categories
  activeFilters.authors = filters.authors
  activeFilters.priceRange = filters.priceRange
}

const handleProductsLoaded = (data: any) => {
  console.log('Sản phẩm đã tải:', data)
}

const handleSearchInput = () => {
  showSuggestions.value = searchQuery.value.length > 2
  
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  searchTimeout = setTimeout(() => {
    handleSearch()
  }, 500)
}

const handleSearch = () => {
  activeFilters.search = searchQuery.value.trim()
  showSuggestions.value = false
  
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
}

const clearSearch = () => {
  searchQuery.value = ''
  activeFilters.search = ''
  showSuggestions.value = false
  
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
}

const handleQuickSearch = (type: string) => {
  // Implement quick search logic based on type
  handleSearch()
  showSuggestions.value = false
}

const fetchCategories = async () => {
  try {
    const response = await categoriesApi.getCategories()
    cats.value = response.data
    
    checkUrlCategory()
  } catch (error) {
    console.error('Lỗi khi tải danh mục')
  }
}

const checkUrlCategory = () => {
  const categoryName = route.query.category as string
  if (!categoryName || !cats.value.length) return
  
  const category = cats.value.find((c: any) => 
    c.name.toLowerCase() === categoryName.toLowerCase()
  )
  
  if (category) {
    initialCategoryId.value = category.id
    activeFilters.categories = [category.id]
  }
}

// Watch route changes
watch(() => route.query.category, (newCategory) => {
  if (!newCategory) {
    initialCategoryId.value = null
    activeFilters.categories = []
    return
  }
  
  if (cats.value.length > 0) {
    checkUrlCategory()
  }
}, { immediate: true })

onMounted(() => {
  checkUrlCategory()
  fetchCategories()
})

useHead({
  title: 'Cửa hàng sách - BINK Publishers',
  meta: [
    { name: 'description', content: 'Cửa hàng sách của BINK Publishers' }
  ]
})
</script>

<style scoped>
.store-hero {
  text-align: center;
  padding: 40px 0;
  margin-top: 20px;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

.subtitle {
  font-size: 24px;
  color: #555;
  margin-bottom: 10px;
  font-weight: 400;
}

.title {
  font-size: 48px;
  color: #0c2b4a;
  font-weight: 700;
  letter-spacing: 2px;
  margin: 0;
}

/* Search Bar Styles */
.search-container {
  max-width: 600px;
  margin: 30px auto 0;
  padding: 0 20px;
}

.search-wrapper {
  position: relative;
}

.search-input {
  width: 100%;
  padding: 12px 50px 12px 20px;
  border: 2px solid #e1e5e9;
  border-radius: 50px;
  font-size: 16px;
  outline: none;
  transition: all 0.3s ease;
  background: #fff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.search-input:focus {
  border-color: #0c2b4a;
  box-shadow: 0 4px 20px rgba(12, 43, 74, 0.15);
}

.search-input::placeholder {
  color: #9ca3af;
}

.search-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}

.clear-search-btn {
  position: absolute;
  right: 45px;
  top: 50%;
  transform: translateY(-50%);
  padding: 4px;
  border: none;
  background: none;
  cursor: pointer;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.clear-search-btn:hover {
  background-color: #f3f4f6;
}

.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #e1e5e9;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  z-index: 10;
  margin-top: 8px;
  overflow: hidden;
}

.suggestion-item {
  padding: 12px 16px;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #374151;
}

.suggestion-item:hover {
  background-color: #f9fafb;
}

.suggestion-item:not(:last-child) {
  border-bottom: 1px solid #f3f4f6;
}

.store-content {
  display: flex;
  gap: 30px;
  margin-top: 40px;
  max-width: 1800px;
  margin-left: auto;
  margin-right: auto;
  padding: 0 20px;
}

@media (max-width: 768px) {
  .store-content {
    flex-direction: column;
    gap: 20px;
  }
  
  .title {
    font-size: 36px;
  }
  
  .subtitle {
    font-size: 20px;
  }
  
  .search-container {
    margin: 20px auto 0;
    padding: 0 15px;
  }
  
  .search-input {
    font-size: 14px;
    padding: 10px 45px 10px 16px;
  }
}
</style>