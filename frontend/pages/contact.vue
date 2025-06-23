<template>
  <div class="w-full min-h-screen bg-white text-[#033363] py-16 px-4 sm:px-0">
    <div class="max-w-3xl mx-auto lg:shadow-lg rounded-2xl px-6 sm:px-10 py-12 bg-white">
      <!-- Title -->
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-center mb-6">
        Liên hệ BINK.Publisher
      </h1>

      <!-- Subtitle -->
      <p class="text-base sm:text-lg text-center leading-relaxed mb-8">
        Chúng tôi luôn sẵn sàng lắng nghe bạn. Nếu bạn có bất kỳ câu hỏi, góp ý hay cần hỗ trợ, đừng ngần ngại liên hệ với chúng tôi bằng biểu mẫu bên dưới.
      </p>

      <!-- Contact Form -->
      <form class="space-y-6">
        <div>
          <label for="name" class="block text-sm font-medium mb-1">Họ và tên</label>
          <input
            type="text"
            id="name"
            name="name"
            placeholder="Nhập họ và tên của bạn"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0250c5]"
          />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium mb-1">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="email@vidu.com"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0250c5]"
          />
        </div>

        <div>
          <label for="message" class="block text-sm font-medium mb-1">Nội dung</label>
          <textarea
            id="message"
            name="message"
            rows="5"
            placeholder="Nhập nội dung tin nhắn của bạn tại đây..."
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0250c5]"
          ></textarea>
        </div>

        <div class="text-center">
          <button
            type="submit"
            class="bg-[#0250c5] text-white px-6 py-3 rounded-full text-lg font-semibold hover:bg-[#033363] transition duration-300"
          >
            Gửi tin nhắn
          </button>
        </div>
      </form>

      <!-- Alternate Contact -->
      <div class="mt-10 text-center text-sm text-gray-500">
        Hoặc liên hệ với chúng tôi qua email:
        <a href="mailto:contact@binkpublisher.com" class="text-[#0250c5] underline">
          contact@binkpublisher.com
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
useHead({
    title: 'Liên hệ - BINK.Publisher',
    meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { hid: 'description', name: 'description', content: 'Liên hệ BINK.Publisher' },
    ],
    // link: [
    //     { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
    // ],
})
</script>
