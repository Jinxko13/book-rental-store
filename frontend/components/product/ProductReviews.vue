<template>
  <div class="bg-white p-6 rounded-lg border border-gray-200 text-sm">
    <h3 class="text-base font-bold mb-4">Đánh giá sản phẩm</h3>
    <div class="flex flex-col md:flex-row items-start">
      <div class="flex flex-col items-center justify-center w-full md:w-1/3 text-center">
        <div class="text-5xl font-bold leading-none mb-1">
          {{ ratingSummary.average }}<span class="text-xl">/5</span>
        </div>
        <a-rate :value="ratingSummary.average" disabled allow-half />
        <div class="text-gray-500 text-sm mt-1">
          ({{ ratingSummary.total }} đánh giá)
        </div>
      </div>
      <div class="w-full md:w-2/3 md:pl-10 mt-4 md:mt-0">
        <div v-for="star in 5" :key="star" class="flex items-center mb-2">
          <span class="w-12 text-sm">{{ 6 - star }} sao</span>
          <div class="flex-1">
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden cursor-pointer hover:bg-gray-300 transition-colors" @click="toggleRatingFilter(6 - star)">
              <div
                class="h-full bg-yellow-400 transition-all duration-300"
                :style="{ width: `${getRatingPercentage(6 - star)}%` }"
              ></div>
            </div>
          </div>
          <span class="w-10 text-sm text-right ml-2">
            {{ getRatingPercentage(6 - star) }}%
          </span>
        </div>
        <div v-if="isAuthenticated && !hasReviewed" class="mt-6">
          <a-rate v-model:value="userRating" allow-half />
          <a-textarea
            v-model:value="userComment"
            :rows="4"
            class="mt-2"
            placeholder="Viết nhận xét..."
          />
          <input type="file" @change="handleFileChange" accept="image/*" class="mt-2" />
          <a-button type="primary" class="mt-2" :loading="submitting" @click="submitFeedback">
            Gửi đánh giá
          </a-button>
        </div>
        <div v-else-if="isAuthenticated && hasReviewed" class="text-sm text-green-600 mt-4">
          Bạn đã gửi đánh giá cho sản phẩm này. Cảm ơn bạn!
        </div>
        <div v-else class="text-sm text-gray-600 mt-4 text-center md:text-left">
          Chỉ có thành viên mới có thể viết nhận xét.
          Vui lòng
          <router-link to="/login" class="text-blue-600 underline hover:text-blue-800">
            đăng nhập
          </router-link>
          hoặc
          <router-link to="/register" class="text-blue-600 underline hover:text-blue-800">
            đăng ký
          </router-link>.
        </div>
      </div>
    </div>

    <!-- Bộ lọc đánh giá -->
    <div class="mt-6 border-t pt-4">
      <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-sm font-medium">Lọc theo:</span>
        <a-button 
          size="small" 
          :type="selectedRating === null ? 'primary' : 'default'"
          @click="setRatingFilter(null)"
        >
          Tất cả ({{ ratingSummary.total }})
        </a-button>
        <a-button 
          v-for="star in 5" 
          :key="star"
          size="small"
          :type="selectedRating === star ? 'primary' : 'default'"
          @click="setRatingFilter(star)"
          class="flex items-center gap-1"
        >
          {{ star }} <span class="text-xs">⭐</span>
          ({{ getRatingCount(star) }})
        </a-button>
      </div>
      
      <!-- Hiển thị trạng thái lọc -->
      <div v-if="selectedRating !== null" class="text-sm text-blue-600 mb-3">
        <span>Đang hiển thị đánh giá {{ selectedRating }} sao</span>
        <a-button type="link" size="small" @click="clearFilter" class="ml-2 p-0">
          Xóa bộ lọc
        </a-button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-4">
      <a-spin size="small" />
      <div class="ml-2">Đang tải đánh giá...</div>
    </div>
    <div v-else-if="reviews.length > 0" class="mt-4">
      <h4 class="font-semibold mb-4">
        Nhận xét từ khách hàng 
        <span v-if="selectedRating">({{ reviews.length }} đánh giá {{ selectedRating }} sao)</span>
        <span v-else>({{ reviews.length }} đánh giá)</span>
      </h4>
      <div class="space-y-4">
        <div
          v-for="review in displayedReviews"
          :key="`review-${review.id}-${review.rating}`"
          class="border-b border-gray-100 pb-4 last:border-b-0"
        >
          <div class="flex items-start justify-between mb-2">
            <div>
              <div class="font-medium">{{ review.user?.name || 'Ẩn danh' }}</div>
              <a-rate 
                :key="`rating-${review.id}-${review.rating}`"
                :value="review.rating" 
                disabled 
                size="small" 
                class="rating-display"
              />
              <div class="text-xs text-gray-400 mt-1">
                Điểm: {{ review.rating }}/5
              </div>
            </div>
            <div class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</div>
          </div>
          <p class="text-gray-600 text-sm leading-relaxed">
            {{ review.comment }}
          </p>
          <img
            v-if="review.image_url"
            :src="review.image_url"
            class="mt-2 max-w-xs rounded"
            alt="Review image"
          />
        </div>
      </div>

      <div v-if="reviews.length > displayedReviews.length" class="text-center mt-4">
        <a-button type="link" @click="loadMoreReviews">Xem thêm đánh giá</a-button>
      </div>
    </div>
    <div v-else-if="selectedRating" class="mt-8 text-center text-gray-500">
      Không có đánh giá {{ selectedRating }} sao cho sản phẩm này
    </div>
    <div v-else class="mt-8 text-center text-gray-500">
      Chưa có đánh giá nào cho sản phẩm này
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { message } from 'ant-design-vue'

interface Review {
  id: number
  user: { id: number; name: string }
  rating: number
  comment: string
  created_at: string
  image_url?: string
}
const { $axios } = useNuxtApp()
const authStore = useAuthStore()

const isAuthenticated = authStore.isAuthenticated
const currentUser = authStore.user
const props = defineProps<{ bookId?: string | number }>()

// State variables
const reviews = ref<Review[]>([])
const displayLimit = ref(3)
const userRating = ref(0)
const userComment = ref('')
const imageFile = ref<File | null>(null)
const submitting = ref(false)
const hasReviewed = ref(false)
const loading = ref(false)
const selectedRating = ref<number | null>(null)

const ratingSummary = ref({
  average: 0,
  total: 0,
  distribution: { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 },
})

const displayedReviews = computed(() => {
  if (!reviews.value || reviews.value.length === 0) {
    return []
  }
  
  const result = reviews.value.slice(0, displayLimit.value).map(review => ({
    ...review,
    rating: Number(review.rating) || 0,
    user: review.user || { id: 0, name: 'Ẩn danh' },
    comment: String(review.comment || '').trim() || 'Không có nhận xét'
  }))
  return result
})

const getRatingPercentage = (star: number): number => {
  return ratingSummary.value.distribution[star] || 0
}

const getRatingCount = (star: number): number => {
  if (!ratingSummary.value.total) return 0
  const percentage = ratingSummary.value.distribution[star] || 0
  return Math.round((percentage / 100) * ratingSummary.value.total)
}

// Filter functions
const setRatingFilter = async (rating: number | null) => {
  selectedRating.value = rating
  displayLimit.value = 3 // Reset display limit when changing filter
  await loadReviews()
}

const toggleRatingFilter = async (rating: number) => {
  if (selectedRating.value === rating) {
    selectedRating.value = null
  } else {
    selectedRating.value = rating
  }
  displayLimit.value = 3
  await loadReviews()
}

const clearFilter = async () => {
  selectedRating.value = null
  displayLimit.value = 3
  await loadReviews()
}

const handleFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement)?.files?.[0]
  if (!file) return
  const isImage = file.type.startsWith('image/')
  const isSizeOK = file.size <= 2 * 1024 * 1024
  if (!isImage) {
    message.error('Tệp phải là hình ảnh (jpg, png, webp...)')
    imageFile.value = null
    return
  }
  if (!isSizeOK) {
    message.error('Ảnh không được lớn hơn 2MB')
    imageFile.value = null
    return
  }
  imageFile.value = file
}

const submitFeedback = async () => {
  if (!props.bookId || submitting.value) return
  submitting.value = true
  const formData = new FormData()
  formData.append('book_id', props.bookId.toString())
  formData.append('rating', userRating.value.toString())
  formData.append('comment', userComment.value)
  if (imageFile.value) formData.append('image', imageFile.value)
  try {
    await $axios.post('/api/reviews', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    message.success('Đánh giá đã được gửi thành công!')
    await initializeData()
    userRating.value = 0
    userComment.value = ''
    imageFile.value = null
  } catch (err: any) {
    console.error('Gửi đánh giá lỗi:', err?.response?.data || err.message)
    if (err?.response?.data?.errors?.image?.length) {
      message.error(err.response.data.errors.image[0])
    } else {
      message.error('Không thể gửi đánh giá. Vui lòng thử lại.')
    }
  } finally {
    submitting.value = false
  }
}

const loadMoreReviews = () => {
  displayLimit.value += 3
}

const loadReviews = async () => {
  if (!props.bookId) return
  
  loading.value = true
  
  try {
    let url = `/api/book/${props.bookId}/reviews`
    if (selectedRating.value !== null) {
      url += `?rating=${selectedRating.value}`
    }
    
    const { data } = await $axios.get(url)
    reviews.value = data
  } catch (err) {
    console.error('Lỗi tải đánh giá:', err)
    reviews.value = []
  } finally {
    loading.value = false
  }
}

const loadRatingSummary = async () => {
  if (!props.bookId) return
  try {
    const { data } = await $axios.get(`/api/book/${props.bookId}/reviews/summary`)
    ratingSummary.value = data
  } catch (err) {
    console.error('Lỗi khi tải tổng quan đánh giá:', err)
  }
}

const checkHasReviewed = async () => {
  if (!props.bookId || !isAuthenticated.value) {
    hasReviewed.value = false
    return
  }
  try {
    const { data } = await $axios.get(`/api/book/${props.bookId}/reviews/check`)
    hasReviewed.value = data.has_reviewed
  } catch (err) {
    console.error('Lỗi khi kiểm tra hasReviewed:', err)
    hasReviewed.value = false
  }
}

const initializeData = async () => {
  if (!props.bookId) return
  
  try {
    await loadReviews()
    await loadRatingSummary()
    await checkHasReviewed()
  } catch (error) {
    console.error('initializeData: Error', error)
  }
}

let watchTimeout: ReturnType<typeof setTimeout> | null = null

watch(
  () => props.bookId,
  async (newBookId, oldBookId) => {
    if (watchTimeout) {
      clearTimeout(watchTimeout)
    }
    
    watchTimeout = setTimeout(async () => {
      if (newBookId && newBookId !== oldBookId) {
        await initializeData()
      }
    }, 150)
  },
  { immediate: true }
)

onMounted(async () => {
  if (props.bookId) {
    await initializeData()
  }
})
</script>

<style scoped>
.rating-display {
  pointer-events: none;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
