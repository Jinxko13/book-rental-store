<template>
  <a-modal
    v-model:open="visible"
    title="Xem nhà sách còn hàng"
    :footer="null"
    width="550px"
    centered
    class="custom-store-modal"
  >
    <div class="flex items-center mb-4">
      <img :src="productImg" class="w-24 h-24 mr-3 object-contain" />
      <div class="product-popup-title">{{ productTitle?.name }}</div>
    </div>

    <div class="flex space-x-2 mb-4">
      <a-select
        v-model:value="selectedProvince"
        style="width: 240px"
        :options="provinceOptions"
        placeholder="Chọn Tỉnh/Thành"
        @change="onProvinceChange"
        allowClear
      />
      <a-select
        v-model:value="selectedDistrict"
        style="width: 240px"
        :options="districtOptions"
        placeholder="Chọn Quận/Huyện"
        allowClear
      />
    </div>

    <div class="space-y-4 max-h-[480px] overflow-y-auto pr-2">
      <div
        v-for="store in filteredStores"
        :key="store.id"
        class="border-b pb-3"
      >
        <div class="font-semibold">{{ store.name }}</div>
        <div class="text-sm text-gray-600">{{ store.address }}</div>
        <a-row class="flex space-x-3 mt-1" justify="space-between">
          <a :href="'tel:' + store.phone" class="text-red-500 hover:text-red-500">📞 {{ store.phone }}</a>
          <a :href="store.map" target="_blank" class="text-blue-600 text-sm underline">
            Xem bản đồ
          </a>
        </a-row>
      </div>
    </div>
  </a-modal>
</template>

<script setup lang="ts">
const visible = defineModel('visible', { type: Boolean })
const props = defineProps<{ visible: boolean; productTitle: { name: string } | null }>()

const productImg = "/img/3900000124904_0.jpg"

const stores = [...Array(9).keys()].map(i => ({
  id: i + 1,
  name: `Nhà sách mẫu ${i + 1}`,
  address: `Địa chỉ mẫu ${i + 1}`,
  phone: "028xxxxxxx",
  map: "https://maps.google.com",
  province: i % 2 === 0 ? "HCM" : "HN",
  district: i % 3 === 0 ? "Q1" : "Q9"
}))

const selectedProvince = ref<string>()
const selectedDistrict = ref<string>()

const provinceOptions = [
  { label: 'TP.HCM', value: 'HCM' },
  { label: 'Hà Nội', value: 'HN' }
]

const districtMap: Record<string, { label: string; value: string }[]> = {
  HCM: [
    { label: 'Quận 1', value: 'Q1' },
    { label: 'Quận 9', value: 'Q9' }
  ],
  HN: [
    { label: 'Quận Hà Đông', value: 'Hadong' }
  ]
}

const districtOptions = computed(() =>
  selectedProvince.value ? districtMap[selectedProvince.value] || [] : []
)

const onProvinceChange = () => {
  selectedDistrict.value = undefined
}

const filteredStores = computed(() => {
  return stores.filter(store => {
    const matchProvince = !selectedProvince.value || store.province === selectedProvince.value
    const matchDistrict = !selectedDistrict.value || store.district === selectedDistrict.value
    return matchProvince && matchDistrict
  })
})
</script>

<style>
.custom-store-modal .ant-modal-content {
  max-height: 650px;
  padding: 0px;
  border-radius: 12px;
  overflow: hidden;
  flex-direction: column;
}
.custom-store-modal .ant-modal-header {
  border-bottom: none;
  padding-bottom: 0;
}
.ant-modal-title {
  text-align: center !important;
  box-shadow:  0 8px 8px -2px  hsl(0, 0%, 95%);
  padding: 10px;
  height: 50px;
}
.custom-store-modal .ant-modal-body {
  padding: 20px;
  overflow-y: auto !important;
}
</style>