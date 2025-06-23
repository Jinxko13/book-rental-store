<template>
  <a-modal 
    v-model:open="visible" 
    width="550px" 
    :footer="false" 
    centered
    wrap-class-name="location-popup"
    :body-style="{}"
    @cancel="visible = false"
  >
    <template #title>
      <div class="flex items-center justify-between px-6 py-5">
        <span class="text-lg font-bold uppercase">Xem nhà sách còn hàng</span>
        <button @click="visible = false" class="text-gray-400 hover:text-black text-xl cursor-pointer">
          <CloseOutlined />
        </button>
      </div>
    </template>

    <div class="px-6 pb-6 overflow-y-auto max-h-[600px]">
      <div class="flex gap-4 mb-4">
        <a-select v-model:value="province" :options="provinceOptions" placeholder="Chọn Tỉnh/Thành" class="w-1/2" />
        <a-select v-model:value="district" :options="districtOptions" placeholder="Chọn Quận/Huyện" class="w-1/2" />
      </div>

      <div class="space-y-4">
        <div class="flex items-center gap-4 mb-4">
          <img src="https://www.fahasa.com/moc-khoa-go-co-viet-nam.html?fhs_campaign=tuhaovietnam_blockluuniem#lg=1&slide=0" class="w-16 h-16 object-contain" alt="product" />
          <div class="font-medium">Móc Khóa Gỗ Cờ Việt Nam</div>
        </div>

        <div class="space-y-4">
          <div v-for="i in 5" :key="i" class="border-t pt-4">
            <div class="font-semibold">Nhà sách mẫu {{ i }}</div>
            <div class="text-sm">Địa chỉ mẫu {{ i }}</div>
            <div class="flex justify-between items-center">
              <div class="text-sm text-red-600 mt-1">
                <span class="mr-1"><PhoneOutlined /></span>028xxxxxxx
              </div>
              <a class="text-sm text-blue-600 font-medium" href="#">Xem bản đồ</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </a-modal>
</template>

<script setup lang="ts">
import { CloseOutlined, PhoneOutlined } from '@ant-design/icons-vue'

const visible = defineModel('open')
const province = ref()
const district = ref()

const provinceOptions = [
  { label: 'Hồ Chí Minh', value: 'hcm' },
  { label: 'Hà Nội', value: 'hn' },
]

const districtOptions = computed(() => {
  if (province.value === 'hcm') return [
    { label: 'Quận 1', value: 'q1' },
    { label: 'Thủ Đức', value: 'td' }
  ]
  if (province.value === 'hn') return [
    { label: 'Hai Bà Trưng', value: 'hbt' },
    { label: 'Cầu Giấy', value: 'cg' }
  ]
  return []
})
</script>

<style>
.location-popup .ant-modal-content {
  max-height: 700px;
  overflow: hidden;
  border-radius: 12px;
  padding: 0 !important;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

.location-popup .ant-modal-body {
  padding: 0 !important;
  max-height: 600px;
  overflow-y: auto;
}

.location-popup .ant-modal-close,
.location-popup .ant-modal-close-x {
  display: none !important;
}

.location-popup .ant-modal-title .flex.items-center.space-x-2 {
  justify-content: space-between;
  padding: 20px 24px !important;
}
</style>
