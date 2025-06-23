<template>
  <div class="flex flex-wrap -mx-2">
    <div
      v-for="item in products"
      :key="item.id"
      class="basis-1/5 px-2 mb-6"
    >
      <a
        :href="`/product/${item.id}`"
        class="product-item block border border-gray-200 p-2 text-[13px] rounded hover:shadow transition duration-300 bg-white"
      >
        <img :src="item.image" alt="product" class="w-full h-[250px] object-contain" />
        <div class="product-title mt-2 h-[36px] leading-snug line-clamp-2 text-[#333] font-normal">
          {{ item.title }}
        </div>
        <div class="product-price flex items-center gap-2 mt-1">
          <span class="text-[#C92127] font-bold text-sm">{{ item.price }} đ</span>
          <span class="bg-[#C92127] text-white text-[11px] font-medium rounded leading-none">
            -{{ item.discount }}%
          </span>
        </div>
        <div class="price-old text-gray-400 text-xs line-through">{{ item.oldPrice }} đ</div>
        <div class="product-star text-[#666] text-xs mt-1">★★★★★ (0)</div>
      </a>
    </div>
  </div>
</template> 

<script>
export default {
  data() {
    return {
      products: [
        { id: 1, image: 'https://cdn1.fahasa.com/media/catalog/product/3/9/3900000169516.jpg', title: 'Sách 1 - Ám Hắc Quán', price: '96.000', oldPrice: '120.000', discount: 20 },
        { id: 2, image: 'https://cdn1.fahasa.com/media/catalog/product/3/9/3900000125055.jpg', title: 'Sách 2 - Quán Canh Hầm 24H', price: '84.000', oldPrice: '105.000', discount: 20 },
        { id: 3, image: 'https://cdn1.fahasa.com/media/catalog/product/3/9/3900000141383.jpg', title: 'Sách 3 - Nobu Dị Giới', price: '88.000', oldPrice: '110.000', discount: 20 },
        { id: 4, image: 'https://cdn1.fahasa.com/media/catalog/product/3/9/3900000262750.jpg', title: 'Sách 4 - Kỳ Án Thập Giác Quán', price: '92.000', oldPrice: '115.000', discount: 20 },
        { id: 5, image: 'https://cdn1.fahasa.com/media/catalog/product/3/9/3900000262743.jpg', title: 'Sách 5 - Bí Ẩn Trong Màn Đêm', price: '97.000', oldPrice: '121.000', discount: 20 },
        { id: 6, image: 'https://cdn1.fahasa.com/media/catalog/product/6/9/6920240831131.jpg', title: 'Sách 6 - Hắc Ám Thư Quán', price: '89.000', oldPrice: '111.000', discount: 20 },
        { id: 7, image: 'https://cdn1.fahasa.com/media/catalog/product/6/9/6920240311015.jpg', title: 'Sách 7 - Hồi Ức Quán Trọ', price: '91.000', oldPrice: '114.000', discount: 20 },
        { id: 8, image: 'https://cdn1.fahasa.com/media/catalog/product/6/9/6920240311176.jpg', title: 'Sách 8 - Truyện Kinh Dị Nhật', price: '90.000', oldPrice: '113.000', discount: 20 },
        { id: 9, image: 'https://cdn1.fahasa.com/media/catalog/product/6/9/6971680456125.jpg', title: 'Sách 9 - Quán Rượu Dị Giới', price: '86.000', oldPrice: '108.000', discount: 20 },
        { id: 10, image: 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935337529257.jpg', title: 'Sách 10 - Bí Ẩn Thành Phố Cũ', price: '93.000', oldPrice: '116.000', discount: 20 },
        { id: 11, image: 'https://cdn1.fahasa.com/media/catalog/product/3/9/3900000225762.jpg', title: 'Sách 11 - Ám Hắc Quán 2', price: '95.000', oldPrice: '119.000', discount: 20 },
        { id: 12, image: 'https://cdn1.fahasa.com/media/catalog/product/8/9/8936134262576.jpg', title: 'Sách 12 - Lời Nguyền Quán Cũ', price: '98.000', oldPrice: '123.000', discount: 20 },
        { id: 13, image: 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935339407942.jpg', title: 'Sách 13 - Vụ Án Lặng Lẽ', price: '87.000', oldPrice: '109.000', discount: 20 },
        { id: 14, image: 'https://cdn1.fahasa.com/media/catalog/product/3/9/3900000272933-dd.jpg', title: 'Sách 14 - Nhật Ký Ánh Trăng', price: '85.000', oldPrice: '106.000', discount: 20 },
        { id: 15, image: 'https://cdn1.fahasa.com/media/catalog/product/9/7/9781579824426.jpg', title: 'Sách 15 - Bí Mật Góc Phố', price: '94.000', oldPrice: '118.000', discount: 20 },
        { id: 16, image: 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_217512.jpg', title: 'Sách 16 - Quán Bóng Tối', price: '99.000', oldPrice: '124.000', discount: 20 },
        { id: 17, image: 'https://cdn1.fahasa.com/media/catalog/product/3/9/3900000173179.jpg', title: 'Sách 17 - Trở Lại Quán Xưa', price: '96.000', oldPrice: '120.000', discount: 20 },
        { id: 18, image: 'https://cdn1.fahasa.com/media/catalog/product/1/5/1505010970767.jpg', title: 'Sách 18 - Căn Phòng Bí Ẩn', price: '88.000', oldPrice: '110.000', discount: 20 },
        { id: 19, image: 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935256603908.jpg', title: 'Sách 19 - Bóng Ma Quán Trọ', price: '91.000', oldPrice: '114.000', discount: 20 },
        { id: 20, image: 'https://cdn1.fahasa.com/media/catalog/product/1/8/1880000997826.jpg', title: 'Sách 20 - Hồi Ký Thập Giác', price: '92.000', oldPrice: '115.000', discount: 20 },
      ]

    };
  }
};
</script>


<style scoped>
.gutter-box {
  background: #0092ff;
  padding: 8px 0;
}

.product-item{
  text-decoration: none;
}

.product-title,
.price-old{
  font-size: 14px;
  margin-top: 5px;
}

.product-price span:nth-child(1){
  line-height: 1.25;
  font-size: 16px !important;
  color: #C92127;
  font-weight: 600;
}

.product-price span:nth-child(2){
  line-height: 1.25;
  font-size: 16px !important;
  background-color: #C92127;
  font-weight: 600;
  color: #fff;
  padding: 4px 5px;
}

.product-star {
  font-size: 14px;
}

.product-show{
  display: inline-block;
  text-decoration: none;
  padding: 10px 60px;
  text-align: center;
  border: 1px solid #C92127;
  font-size: 14px;
  color: #000;
  background-color: #fff;
  font-weight: 700;
}

.hover\:shadow:hover{
  --un-shadow: var(--un-shadow-inset) 0 5px 15px 0 var(--un-shadow-color, rgb(0 0 0 / 0.1)), var(--un-shadow-inset) 0 1px 2px -1px var(--un-shadow-color, rgb(0 0 0 / 0.1)) !important;
    box-shadow: var(--un-ring-offset-shadow), var(--un-ring-shadow), var(--un-shadow) !important;
}
</style>