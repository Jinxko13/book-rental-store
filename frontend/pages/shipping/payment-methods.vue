<template>
    <div class="checkout-page bg-gray-50 min-h-screen py-8">
      <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Thanh Toán</h1>
          <p class="text-gray-600 mt-2">Hoàn tất đơn hàng của bạn</p>
        </div>
  
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column - Forms -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Shipping Information -->
            <div class="bg-white rounded-lg shadow p-6">
              <h2 class="text-xl font-semibold mb-4">Thông Tin Giao Hàng</h2>
              <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ *</label>
                    <input 
                      v-model="shippingInfo.firstName" 
                      type="text" 
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Nhập họ"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên *</label>
                    <input 
                      v-model="shippingInfo.lastName" 
                      type="text" 
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Nhập tên"
                    />
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                  <input 
                    v-model="shippingInfo.email" 
                    type="email" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="example@email.com"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                  <input 
                    v-model="shippingInfo.phone" 
                    type="tel" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0123456789"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ *</label>
                  <input 
                    v-model="shippingInfo.address" 
                    type="text" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Số nhà, tên đường"
                  />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thành phố *</label>
                    <select 
                      v-model="shippingInfo.city" 
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="">Chọn thành phố</option>
                      <option value="hanoi">Hà Nội</option>
                      <option value="hcm">TP. Hồ Chí Minh</option>
                      <option value="danang">Đà Nẵng</option>
                      <option value="haiphong">Hải Phòng</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện *</label>
                    <input 
                      v-model="shippingInfo.district" 
                      type="text" 
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Quận/Huyện"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã bưu điện</label>
                    <input 
                      v-model="shippingInfo.zipCode" 
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="100000"
                    />
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú đơn hàng</label>
                  <textarea 
                    v-model="shippingInfo.notes" 
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)"
                  ></textarea>
                </div>
              </form>
            </div>
  
            <!-- Shipping Method -->
            <div class="bg-white rounded-lg shadow p-6">
              <h2 class="text-xl font-semibold mb-4">Phương Thức Giao Hàng</h2>
              <div class="space-y-3">
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                  <input 
                    type="radio" 
                    name="shipping" 
                    value="standard" 
                    v-model="selectedShipping"
                    class="mr-3"
                  />
                  <div class="flex-1">
                    <div class="flex justify-between items-center">
                      <span class="font-medium">Giao hàng tiêu chuẩn</span>
                      <span class="text-green-600 font-medium">Miễn phí</span>
                    </div>
                    <p class="text-sm text-gray-500">5-7 ngày làm việc</p>
                  </div>
                </label>
                
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                  <input 
                    type="radio" 
                    name="shipping" 
                    value="express" 
                    v-model="selectedShipping"
                    class="mr-3"
                  />
                  <div class="flex-1">
                    <div class="flex justify-between items-center">
                      <span class="font-medium">Giao hàng nhanh</span>
                      <span class="font-medium">$5.99</span>
                    </div>
                    <p class="text-sm text-gray-500">2-3 ngày làm việc</p>
                  </div>
                </label>
                
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                  <input 
                    type="radio" 
                    name="shipping" 
                    value="overnight" 
                    v-model="selectedShipping"
                    class="mr-3"
                  />
                  <div class="flex-1">
                    <div class="flex justify-between items-center">
                      <span class="font-medium">Giao hàng hỏa tốc</span>
                      <span class="font-medium">$12.99</span>
                    </div>
                    <p class="text-sm text-gray-500">1 ngày làm việc</p>
                  </div>
                </label>
              </div>
            </div>
  
            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow p-6">
              <h2 class="text-xl font-semibold mb-4">Phương Thức Thanh Toán</h2>
              <div class="space-y-4">
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                  <input 
                    type="radio" 
                    name="payment" 
                    value="card" 
                    v-model="selectedPayment"
                    class="mr-3"
                  />
                  <span class="font-medium">💳 Thẻ tín dụng/ghi nợ</span>
                </label>
                
                <div v-if="selectedPayment === 'card'" class="ml-8 space-y-4 bg-gray-50 p-4 rounded-lg">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số thẻ *</label>
                    <input 
                      v-model="cardInfo.number" 
                      type="text" 
                      placeholder="1234 5678 9012 3456"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn *</label>
                      <input 
                        v-model="cardInfo.expiry" 
                        type="text" 
                        placeholder="MM/YY"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">CVV *</label>
                      <input 
                        v-model="cardInfo.cvv" 
                        type="text" 
                        placeholder="123"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      />
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên chủ thẻ *</label>
                    <input 
                      v-model="cardInfo.name" 
                      type="text" 
                      placeholder="NGUYEN VAN A"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                </div>
                
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                  <input 
                    type="radio" 
                    name="payment" 
                    value="momo" 
                    v-model="selectedPayment"
                    class="mr-3"
                  />
                  <span class="font-medium">📱 Ví MoMo</span>
                </label>
                
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                  <input 
                    type="radio" 
                    name="payment" 
                    value="zalopay" 
                    v-model="selectedPayment"
                    class="mr-3"
                  />
                  <span class="font-medium">💰 ZaloPay</span>
                </label>
                
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                  <input 
                    type="radio" 
                    name="payment" 
                    value="cod" 
                    v-model="selectedPayment"
                    class="mr-3"
                  />
                  <span class="font-medium">💵 Thanh toán khi nhận hàng (COD)</span>
                </label>
              </div>
            </div>
          </div>
  
          <!-- Right Column - Order Summary -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
              <h2 class="text-xl font-semibold mb-4">Tóm Tắt Đơn Hàng</h2>
              
              <!-- Order Items -->
              <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                <div v-for="item in orderItems" :key="item.id" class="flex items-center space-x-3">
                  <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center">
                    <span class="text-xs font-medium">{{ item.name.charAt(0) }}</span>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-medium">{{ item.name }}</p>
                    <p class="text-xs text-gray-500">Số lượng: {{ item.quantity }}</p>
                  </div>
                  <p class="text-sm font-medium">${{ (item.price * item.quantity).toFixed(2) }}</p>
                </div>
              </div>
              
              <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                  <span>Tạm tính ({{ totalItems }} sản phẩm)</span>
                  <span>${{ subtotal.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span>Phí vận chuyển</span>
                  <span>{{ shippingCost === 0 ? 'Miễn phí' : '$' + shippingCost.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span>Thuế</span>
                  <span>${{ tax.toFixed(2) }}</span>
                </div>
                <div class="border-t pt-2">
                  <div class="flex justify-between text-lg font-semibold">
                    <span>Tổng cộng</span>
                    <span>${{ totalAmount.toFixed(2) }}</span>
                  </div>
                </div>
              </div>
              
              <button 
                @click="placeOrder" 
                :disabled="!isFormValid"
                class="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
              >
                <span v-if="isProcessing">Đang xử lý...</span>
                <span v-else>Đặt Hàng - ${{ totalAmount.toFixed(2) }}</span>
              </button>
              
              <div class="flex items-center justify-center mt-3 text-sm text-gray-500">
                <span class="mr-1">🔒</span>
                Thanh toán an toàn và bảo mật
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  
  // Sample order items (in real app, this would come from props or store)
  const orderItems = ref([
    { id: 1, name: 'iPhone 15 Pro', price: 999.99, quantity: 1, selected: true },
    { id: 2, name: 'AirPods Pro', price: 249.99, quantity: 2, selected: true },
    { id: 3, name: 'MacBook Air M2', price: 1199.99, quantity: 1, selected: true }
  ]);
  
  // Form data
  const shippingInfo = ref({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    district: '',
    zipCode: '',
    notes: ''
  });
  
  const cardInfo = ref({
    number: '',
    expiry: '',
    cvv: '',
    name: ''
  });
  
  const selectedShipping = ref('standard');
  const selectedPayment = ref('card');
  const isProcessing = ref(false);
  
  // Computed properties
  const subtotal = computed(() => {
    return orderItems.value.reduce((total, item) => total + item.price * item.quantity, 0);
  });
  
  const totalItems = computed(() => {
    return orderItems.value.reduce((count, item) => count + item.quantity, 0);
  });
  
  const shippingCost = computed(() => {
    const costs = {
      standard: 0,
      express: 5.99,
      overnight: 12.99
    };
    return costs[selectedShipping.value] || 0;
  });
  
  const tax = computed(() => {
    return subtotal.value * 0.1; // 10% tax
  });
  
  const totalAmount = computed(() => {
    return subtotal.value + shippingCost.value + tax.value;
  });
  
  const isFormValid = computed(() => {
    const requiredFields = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'district'];
    const basicInfoValid = requiredFields.every(field => shippingInfo.value[field].trim() !== '');
    
    if (selectedPayment.value === 'card') {
      const cardFields = ['number', 'expiry', 'cvv', 'name'];
      const cardInfoValid = cardFields.every(field => cardInfo.value[field].trim() !== '');
      return basicInfoValid && cardInfoValid;
    }
    
    return basicInfoValid;
  });
  
  // Methods
  const placeOrder = async () => {
    if (!isFormValid.value) {
      alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
      return;
    }
    
    isProcessing.value = true;
    
    // Simulate API call
    setTimeout(() => {
      alert(`Đặt hàng thành công! Mã đơn hàng: #${Date.now()}`);
      isProcessing.value = false;
      
      // In real app, redirect to success page or clear cart
      console.log('Order placed:', {
        shippingInfo: shippingInfo.value,
        paymentMethod: selectedPayment.value,
        shippingMethod: selectedShipping.value,
        items: orderItems.value,
        total: totalAmount.value
      });
    }, 2000);
  };
  
  // Load cart items from localStorage if available
  onMounted(() => {
    try {
      const storedCart = JSON.parse(localStorage.getItem('cart') || '[]');
      if (storedCart.length > 0) {
        // Filter selected items only
        orderItems.value = storedCart.filter(item => item.selected);
      }
    } catch (error) {
      console.error('Error loading cart:', error);
    }
  });
  </script>
  
  <style scoped>
  /* Custom styles if needed */
  .checkout-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }
  
  input:focus, select:focus, textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  .sticky {
    position: sticky;
  }
  </style>