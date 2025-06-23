<template>
  <a-modal
    :open="props.visible"
    width="550px"
    :footer="false"
    centered
    wrap-class-name="coupon-popup"
    :body-style="{}"
    @cancel="handleCancel"
  >
    <template #title>
      <div class="flex items-center space-x-2 z-10">
        <ArrowLeftOutlined
          v-if="showRulePopup"
          @click="backToList"
          class="cursor-pointer"
        />
        <span class="text-lg font-bold uppercase">
          {{ showRulePopup ? 'Điều kiện áp dụng' : 'Ưu đãi liên quan' }}
        </span>
      <button @click="handleCancel" class="text-gray-400 hover:text-black text-xl border-inherit bg-inherit cursor-pointer">
        <CloseOutlined />
      </button>
      </div>
    </template>

    <!-- Danh sách voucher -->
    <div v-if="!showRulePopup">
      <div v-for="group in groupedCoupons" :key="group.type" class="mb-4">
        <div class="flex justify-between items-center mb-2">
          <h3 class="font-semibold text-base">{{ group.title }}</h3>
          <span class="text-xs text-gray-500">Áp dụng 1 lần</span>
        </div>

        <div v-for="(coupon, idx) in displayCoupons(group)" :key="coupon.id" class="mb-2">
          <div
            class="flex items-start p-4 rounded-xl bg-white shadow-sm border border-gray-200"
          >
            <!-- Icon -->
            <div class="relative w-12 h-12 flex-shrink-0">
              <div class="absolute left-0 top-1/2 -translate-y-1/2 w-2 h-2 bg-white rounded-full z-10"></div>
              <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-2 bg-white rounded-full z-10"></div>
              <div
                class="w-full h-full rounded-lg flex items-center justify-center text-lg font-bold text-white"
                :class="coupon.color || 'bg-yellow-400'"
              >
                %
              </div>
            </div>

            <!-- Nội dung -->
            <div class="ml-4 flex-1">
              <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                  <div class="text-sm font-semibold uppercase text-gray-900">
                    {{ coupon.title }}
                  </div>
                  <div class="text-xs text-gray-500 line-clamp-2 leading-snug">{{ coupon.description }}</div>
                  <div class="text-xs text-blue-500 font-semibold mt-1">
                    HSD: {{ coupon.expired }}
                  </div>
                </div>
                <InfoCircleOutlined
                  class="text-blue-500 cursor-pointer text-lg mt-1"
                  @click="showRules(group.title, coupon)"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Xem thêm / Rút gọn -->
        <div
          v-if="group.items.length > maxVisible[group.type]"
          class="text-center mt-1"
        >
          <a
            class="flex items-center justify-center text-md text-blue-600 cursor-pointer "
            @click="toggleExpand(group.type)"
          >
            <div>{{ expanded[group.type] ? 'Rút gọn' : 'Xem thêm' }}</div>
            <component class="ml-2" :is="expanded[group.type] ? UpOutlined : DownOutlined" />
          </a>
        </div>
      </div>
    </div>

    <!-- Chi tiết voucher -->
    <div v-else>
      <div class="text-sm font-bold mb-2 text-gray-800">{{ currentGroupTitle }}</div>
      <div class="bg-orange-50 rounded p-4 text-sm text-red-500 font-medium space-y-1 mb-4">
        <div v-for="h in currentCoupon.highlights" :key="h">{{ h }}</div>
      </div>
      <ul class="text-sm text-black list-disc pl-5 space-y-1">
        <li v-for="r in currentCoupon.rules" :key="r">{{ r }}</li>
      </ul>
    </div>
  </a-modal>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { InfoCircleOutlined, ArrowLeftOutlined, CloseOutlined, DownOutlined, UpOutlined } from '@ant-design/icons-vue'

const props = defineProps<{ visible: boolean }>()
const emit = defineEmits(['update:visible'])

const handleCancel = () => {
  showRulePopup.value = false
  emit('update:visible', false)
}

const showRulePopup = ref(false)
const currentCoupon = ref<any>({})
const currentGroupTitle = ref('')
const expanded = ref<Record<string, boolean>>({})
const maxVisible = { discount: 3, shipping: 2, payment: 2 }

const groupedCoupons = [
  {
    type: 'discount',
    title: 'Mã giảm giá',
    items: [
      { id: 1, title: 'MÃ GIẢM 50K - TOÀN SÀN', expired: '31/05/2025', description: 'Đơn hàng từ 550k - Không bao gồm các sản phẩm sau Manga, Ngoại Văn, Phiếu Quà Tặng, Sách Giáo Khoa, Mã,...', highlights: ['Giảm giá 50K', 'Đơn từ 550K'], rules: ['Áp dụng đơn từ 550K'] },
      { id: 2, title: 'MÃ GIẢM 100K - TOÀN SÀN', expired: '31/05/2025', description: 'Đơn hàng từ 1.1tr - Không bao gồm các sản phẩm sau Manga, Ngoại Văn, Phiếu Quà Tặng, Sách Giáo Khoa, Mã,...', highlights: ['Giảm giá 100K', 'Đơn từ 1.1tr'], rules: ['Áp dụng đơn từ 1.1 triệu'] },
      { id: 3, title: 'MÃ GIẢM 10K - TOÀN SÀN', expired: '31/05/2025', description: 'Đơn từ 130k', highlights: ['Giảm giá 10K', 'Đơn từ 130K'], rules: ['Áp dụng đơn từ 130K'] },
      { id: 4, title: 'MÃ GIẢM 25K - TOÀN SÀN', expired: '31/05/2025', description: 'Đơn hàng từ 280k - Không bao gồm các sản phẩm sau Manga, Ngoại Văn, Phiếu Quà Tặng, Sách Giáo Khoa, Mã,...', highlights: ['Giảm giá 25K', 'Đơn từ 280K'], rules: ['Áp dụng đơn từ 280K'] }
    ]
  },
  {
    type: 'shipping',
    title: 'Mã vận chuyển',
    items: [
      { id: 5, title: 'MÃ GIẢM 20K - HÀNG NGOÀI SÁCH', expired: '31/05/2025', description: 'Đơn hàng từ 150k', highlights: ['Giảm phí 20K'], rules: ['Đơn hàng từ 150K'], color: 'bg-green-500' }
    ]
  },
  {
    type: 'payment',
    title: 'Mã thanh toán',
    items: [
      { id: 6, title: 'HOME CREDIT: GIẢM 50.000Đ', expired: '30/06/2025', description: 'Áp dụng khi thanh toán bằng Home Credit.', highlights: ['Giảm 50.000Đ qua Home Credit'], rules: ['Chỉ áp dụng Home Credit'], color: 'bg-blue-500' }
    ]
  },
  {
    type: 'shopping',
    title: 'Mã giảm giá từ shop',
    items: [
      { id: 6, title: 'HOME SHOP: GIẢM 50.000Đ', expired: '30/06/2025', description: 'Áp dụng khi thanh toán bằng Home Credit.', highlights: ['Giảm 50.000Đ qua Home Credit'], rules: ['Chỉ áp dụng Home Credit'], color: 'bg-blue-500' }
    ]
  }
]

const showRules = (title: string, coupon: any) => {
  currentGroupTitle.value = title
  currentCoupon.value = coupon
  showRulePopup.value = true
}

const backToList = () => {
  showRulePopup.value = false
}

const toggleExpand = (type: string) => {
  expanded.value[type] = !expanded.value[type]
}

const displayCoupons = (group: any) => {
  return expanded.value[group.type]
    ? group.items
    : group.items.slice(0, maxVisible[group.type])
}
</script>

<style>
.ant-modal-content{
  padding:0 !important;
}

.coupon-popup .ant-modal-content {
  max-height: 700px; /* hoặc thử 680px, tuỳ bạn */
  overflow: hidden;
  border-radius: 12px;
}

.coupon-popup .ant-modal-body {
  max-height: 600px; /* để nội dung scroll trong khối body */
  overflow-y: auto;
  padding: 20px 24px !important;
  z-index: 10;
}

.ant-modal-title {
  padding: 0 !important;
  height: inherit !important;
}

.coupon-popup .ant-modal-close{
  display: none;
}

.coupon-popup .ant-modal-close-x {
  display: none !important;
}
.ant-modal-title .flex.items-center.space-x-2{
  justify-content: space-between;
  padding: 20px 24px !important;
}
</style>
