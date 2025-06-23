<template>
    <a-carousel autoplay arrows>
        <template #prevArrow>
            <div class="custom-slick-arrow" style="left: 10px; z-index: 1">
            <left-circle-outlined />
            </div>
        </template>
        <template #nextArrow>
            <div class="custom-slick-arrow" style="right: 10px">
            <right-circle-outlined />
            </div>
        </template>
        <div><img src="/public/image/image1.jpg" alt="Banner 1" class="carousel-img" /></div>
        <div><img src="/public/image/image1.jpg" alt="Banner 2" class="carousel-img" /></div>
        <div><img src="/public/image/Slide1.jpg" alt="Banner 3" class="carousel-img" /></div>
        <div><img src="/public/image/image2.jpg" alt="Banner 4" class="carousel-img" /></div>
    </a-carousel>
</template>

<script lang="ts" setup>
  import { LeftCircleOutlined, RightCircleOutlined } from '@ant-design/icons-vue';
</script>

<style scoped>
/* For demo */
:deep(.slick-slide) {
    text-align: center;
    height: auto;
    /* line-height: 500px; */
    background: #364d79;
    overflow: hidden;
}

:deep(.slick-slide h3) {
    color: #fff;
}

.carousel-img {
    width: 100%;
    height: 420px;
    object-fit: cover;
    display: block;
}

:deep(.slick-arrow.custom-slick-arrow) {
    width: 25px;
    height: 25px;
    font-size: 25px;
    color: #fff;
    background-color: rgba(31, 45, 61, 0.11);
    transition: ease all 0.3s;
    opacity: 0.3;
    z-index: 1;
}
:deep(.slick-arrow.custom-slick-arrow:before) {
    display: none;
}
:deep(.slick-arrow.custom-slick-arrow:hover) {
    color: #fff;
    opacity: 0.5;
}
  
</style>