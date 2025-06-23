<template>
  <div class="w-full p-4 bg-white rounded-lg">
    <div class="productDetail px-3 md:px-0" v-if="images.length">
      <a-carousel arrows dots-class="slick-dots slick-thumb" class="w-full">
        <template #customPaging="{ i }">
          <a>
            <img :src="images[i]" />
          </a>
        </template>

        <div v-for="(img, i) in images" :key="i">
          <img :src="img" @click="showPreview(i)" class="thumbnail-img" />
        </div>
      </a-carousel>

      <a-image-preview-group :preview="{ visible, current: previewIndex, onVisibleChange: val => (visible = val), infinite: true }">
        <div class="hidden-preview-images">
          <a-image v-for="(img, i) in images" :key="i" :src="img" />
        </div>
      </a-image-preview-group>

      <a-button block class="mt-4 border border-white text-white hover:text-[#0e345a] hover:bg-white transition-all duration-300" @click="handleAddToCart">
        Thêm vào giỏ hàng
      </a-button>
    </div>
    <div v-else class="text-center text-gray-400">Không có hình ảnh sản phẩm</div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{ product: { title: string; images?: string[] } }>()
const emit = defineEmits<{ 'add-to-cart': [] }>()

const visible = ref(false)
const previewIndex = ref(0)
const successMessage = ref('')

const showPreview = (i: number) => {
  previewIndex.value = i
  visible.value = true
}

const handleAddToCart = () => {
  emit('add-to-cart')
  successMessage.value = `Đã thêm sản phẩm "${props.product.title}" vào giỏ hàng!`
  setTimeout(() => (successMessage.value = ''), 3000)
}

const images = computed(() => props.product?.images?.length ? props.product.images : ['https://cdn1.fahasa.com/media/catalog/product/9/7/9786043561272_1_1.jpg'])
</script>

<style scoped>
.hidden-preview-images { display: none; }
.productDetail .thumbnail-img { max-width: 450px !important; max-height: 450px !important; }

:deep(.ant-carousel .slick-dots) {
  position: relative;
  height: auto;
}
:deep(.ant-carousel .slick-slide img) {
  border: 5px solid #fff;
  display: block;
  margin: auto;
  max-width: 80%;
  cursor: pointer;
}
:deep(.ant-carousel .slick-arrow) {
  display: none !important;
}
:deep(.ant-carousel .slick-thumb li) {
  width: 60px;
  height: 45px;
}
:deep(.ant-carousel .slick-thumb li img) {
  width: 100%;
  height: 100%;
  filter: grayscale(100%);
  display: block;
}
:deep(.ant-carousel .slick-thumb li.slick-active img) {
  filter: grayscale(0%);
}

@media (max-width: 768px) {
  .productDetail {
    padding: 0 12px;
  }
  .productDetail .thumbnail-img {
    max-width: 100% !important;
    max-height: 300px !important;
    object-fit: contain;
    margin: 0 auto;
  }
  .productDetail :deep(.slick-slide img) {
    max-width: 100% !important;
    height: auto !important;
    object-fit: contain;
  }
  .productDetail :deep(.slick-dots) {
    bottom: -20px !important;
    display: none !important;
  }
  .productDetail :deep(.slick-thumb li) {
    width: 60px;
    height: 60px;
  }
}
</style>

<style>
.hidden-preview-images {
  display: none;
}
.productDetail .thumbnail-img {
  max-width: 450px !important;
  max-height: 450px !important;
}
.productDetail .slick-list {
  margin-bottom: 16px !important;
}
.ant-image-preview-root .ant-image-preview-img {
  max-width: 600px !important;
  max-height: 600px !important;
}
.ant-image-preview-mask {
  background-color: #000 !important;
}
.ant-image-preview-img {
  height: 100%;
}
</style>