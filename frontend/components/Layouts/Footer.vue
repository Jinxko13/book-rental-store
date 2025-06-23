<template>
  <footer class="bg-[#0A3251] text-white px-4 sm:px-0 py-12">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Grid Section -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Info -->
        <div>
          <h3 class="font-bold text-lg mb-4">BINK. Publishers</h3>
          <address class="not-italic space-y-1 text-sm">
            <p>82 Duy Tan, Cau Giay, Ha Noi</p>
            <p>Viet Nam, Ho Chi Minh</p>
            <p>123-456-7890</p>
            <p><a href="mailto:info@my-domain.com" class="hover:underline">info@my-domain.com</a></p>
          </address>
        </div>

        <!-- Shop Links -->
        <div>
          <h3 class="font-bold text-lg mb-4">Shop</h3>
          <ul class="space-y-2 text-sm">
            <li><NuxtLink to="" class="hover:underline">FAQ</NuxtLink></li>
            <li><NuxtLink to="" class="hover:underline">Shipping & Returns</NuxtLink></li>
            <li><NuxtLink to="" class="hover:underline">Store Policy</NuxtLink></li>
            <li><NuxtLink to="" class="hover:underline">Payment Methods</NuxtLink></li>
          </ul>
        </div>

        <!-- Social -->
        <div>
          <h3 class="font-bold text-lg mb-4">Socials</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:underline">Facebook</a></li>
            <li><a href="#" class="hover:underline">Twitter</a></li>
            <li><a href="#" class="hover:underline">Instagram</a></li>
            <li><a href="#" class="hover:underline">Pinterest</a></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h3 class="font-bold text-lg mb-2">Be the First to Know</h3>
          <p class="mb-4 text-sm">Sign up for our newsletter</p>
          <form @submit.prevent="subscribeToNewsletter" class="space-y-4 text-sm">
            <div>
              <label for="email" class="block mb-1">
                Enter your email here <span class="text-red-400">*</span>
              </label>
              <input
                type="email"
                id="email"
                v-model="email"
                required
                class="w-full p-2 text-gray-900 bg-white rounded"
              />
            </div>
            <div class="flex items-start gap-2">
              <input
                type="checkbox"
                id="subscribe"
                v-model="subscribeConsent"
                class="mt-1"
              />
              <label for="subscribe">
                Yes, subscribe me to your newsletter. <span class="text-red-400">*</span>
              </label>
            </div>
            <button
              type="submit"
              class="bg-white text-gray-800 px-4 py-2 font-medium rounded hover:bg-gray-200 transition"
            >
              Subscribe
            </button>
          </form>
        </div>
      </div>

      <!-- Divider -->
      <div class="border-t border-[#2a517a] mt-12 pt-6 text-center text-sm">
        <p>
          © {{ new Date().getFullYear() }} by BINK. Publishers. Powered and secured by
          <a href="https://wix.com" class="hover:underline">Wix</a>
        </p>
      </div>

      <!-- Scroll to Top -->
      <div class="text-right mt-4">
        <button @click="scrollToTop" class="text-white hover:text-gray-300">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
          </svg>
        </button>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')
const subscribeConsent = ref(false)

const subscribeToNewsletter = () => {
  if (!subscribeConsent.value) {
    alert('Vui lòng đồng ý với điều khoản đăng ký')
    return
  }

  console.log('Đăng ký với email:', email.value)
  email.value = ''
  subscribeConsent.value = false
  alert('Cảm ơn bạn đã đăng ký!')
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>