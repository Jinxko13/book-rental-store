<template>
  <div class="bg-[#fff] py-16 px-4 sm:px-0">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl lg:shadow-lg shadow-custom px-6 sm:px-10 py-12 text-[#033363]">
      <!-- Title -->
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-center mb-8">
        Giới thiệu về Nhà sách của chúng tôi
      </h1>

      <!-- Content -->
      <div class="space-y-6 text-base sm:text-lg leading-relaxed text-justify">
        <p>
          Chào mừng bạn đến với <strong class="font-semibold text-[#0250c5]">BINK.Publisher</strong> – nơi những cuốn sách được tuyển chọn kỹ lưỡng, truyền cảm hứng và đầy tinh tế dành riêng cho bạn.
          Chúng tôi tin rằng một cuốn sách hay không chỉ nằm ở nội dung – mà còn là cảm xúc khi bạn cầm nó trên tay. Mỗi trang sách, mỗi dòng chữ đều mang đến một trải nghiệm độc đáo và sâu sắc.
        </p>

        <p>
          Tại BINK.Publisher, bạn sẽ khám phá những ấn bản nghệ thuật, sách thiết kế đẹp mắt, sách tâm lý học, văn học đương đại và nhiều thể loại khác – tất cả được trình bày với sự chăm chút và thẩm mỹ,
          lý tưởng để đọc, trưng bày hoặc làm quà tặng.
        </p>

        <p>
          Chúng tôi mong muốn trở thành một điểm đến yên bình dành cho những người yêu sách, trân trọng cái đẹp và tìm kiếm một góc nhỏ tĩnh lặng giữa nhịp sống hối hả.
          <span class="italic text-[#0250c5]"> Cảm ơn bạn đã ghé thăm!</span>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
useHead({
    title: 'Giới thiệu - BINK.Publisher',
    meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { hid: 'description', name: 'description', content: 'Giới thiệu về Nhà sách của chúng tôi' },
    ],
    // link: [
    //     { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
    // ],
})
</script>

<style scoped>
.shadow-custom{
  --tw-shadow: 0px 9px 42px 14px var(--tw-shadow-color, rgb(0 0 0 / 0.1)), 0 4px 6px -4px var(--tw-shadow-color, rgb(0 0 0 / 0.1));
}
</style>
