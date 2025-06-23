<template>
  <div class="flex bg-white border rounded p-3 items-center gap-3">
    <!-- Icon tròn màu -->
    <div :class="`w-10 h-10 flex items-center justify-center rounded text-white text-xl font-bold ${bgColor}`">
      <slot name="icon">%</slot>
    </div>

    <!-- Nội dung chính -->
    <div class="flex-1">
      <div class="font-bold text-sm">{{ coupon.title }}</div>
      <div class="text-xs text-gray-500">HSD: {{ coupon.expired }}</div>
    </div>

    <!-- Mở modal điều kiện -->
    <a @click="show = true" class="text-blue-600 text-sm cursor-pointer">ⓘ</a>

    <!-- Modal Điều kiện -->
    <a-modal
      v-model:open="show"
      title="ĐIỀU KIỆN ÁP DỤNG"
      width="600px"
      centered
      :footer="false"
    >
      <!-- Tiêu đề -->
      <div class="text-sm font-bold mb-2 text-gray-800">{{ groupTitle }}</div>

      <!-- Danh sách các mức giảm -->
      <div class="bg-orange-50 rounded p-4 text-sm text-orange-600 font-medium space-y-2 mb-4">
        <div v-for="highlight in coupon.highlights" :key="highlight">{{ highlight }}</div>
      </div>

      <!-- Điều kiện gạch đầu dòng -->
      <ul class="text-sm text-black list-disc pl-5 space-y-1">
        <li v-for="rule in coupon.rules" :key="rule">{{ rule }}</li>
      </ul>
    </a-modal>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  coupon: {
    title: string
    expired: string
    rules: string[]
    highlights: string[] // new
  }
  bgColor?: string
  groupTitle?: string
}>()

const show = ref(false)
</script>
