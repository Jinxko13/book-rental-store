<template>
    <div class="order-confirmation bg-gray-50 min-h-screen py-8">
      <div class="max-w-4xl mx-auto px-4">
        <!-- Success Header -->
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Đặt hàng thành công!</h1>
          <p class="text-gray-600">Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.</p>
        </div>
  
        <!-- Order Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
              <h2 class="text-2xl font-semibold text-gray-900">Đơn hàng #{{ orderInfo.orderNumber }}</h2>
              <p class="text-gray-600 mt-1">Ngày đặt: {{ formatDate(orderInfo.orderDate) }}</p>
            </div>
            <div class="mt-4 md:mt-0">
              <span :class="getStatusClass(orderInfo.status)" class="px-4 py-2 rounded-full text-sm font-medium">
                {{ getStatusText(orderInfo.status) }}
              </span>
            </div>
          </div>
  
          <!-- Order Progress -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Trạng thái đơn hàng</h3>
            <div class="flex items-center justify-between">
              <div v-for="(step, index) in orderSteps" :key="index" class="flex flex-col items-center flex-1">
                <div class="relative">
                  <div :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center border-2',
                    step.completed ? 'bg-green-500 border-green-500 text-white' : 
                    step.active ? 'bg-blue-500 border-blue-500 text-white' : 
                    'bg-gray-200 border-gray-300 text-gray-500'
                  ]">
                    <svg v-if="step.completed" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span v-else class="text-sm font-medium">{{ index + 1 }}</span>
                  </div>
                  <div v-if="index < orderSteps.length - 1" :class="[
                    'absolute top-5 left-10 w-full h-0.5',
                    step.completed ? 'bg-green-500' : 'bg-gray-300'
                  ]" style="width: calc(100vw / 4 - 2.5rem);"></div>
                </div>
                <div class="mt-3 text-center">
                  <p class="text-sm font-medium text-gray-900">{{ step.title }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ step.description }}</p>
                  <p v-if="step.date" class="text-xs text-gray-400 mt-1">{{ formatDate(step.date) }}</p>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Estimated Delivery -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <div>
                <p class="font-medium text-blue-900">Dự kiến giao hàng</p>
                <p class="text-sm text-blue-700">{{ formatDate(orderInfo.estimatedDelivery) }}</p>
              </div>
            </div>
          </div>
        </div>
  
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Order Details -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">Chi tiết đơn hàng</h3>
            
            <!-- Items List -->
            <div class="space-y-4 mb-6">
              <div v-for="item in orderInfo.items" :key="item.id" class="flex items-center space-x-4 pb-4 border-b border-gray-100 last:border-b-0">
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                  <span class="text-lg font-bold text-gray-600">{{ item.name.charAt(0) }}</span>
                </div>
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900">{{ item.name }}</h4>
                  <p class="text-sm text-gray-500">Số lượng: {{ item.quantity }}</p>
                  <p class="text-sm text-gray-500">Đơn giá: ${{ item.price.toFixed(2) }}</p>
                </div>
                <div class="text-right">
                  <p class="font-medium text-gray-900">${{ (item.price * item.quantity).toFixed(2) }}</p>
                </div>
              </div>
            </div>
  
            <!-- Order Summary -->
            <div class="border-t pt-4">
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span>Tạm tính ({{ totalItems }} sản phẩm)</span>
                  <span>${{ orderInfo.subtotal.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span>Phí vận chuyển</span>
                  <span>{{ orderInfo.shippingCost === 0 ? 'Miễn phí' : '$' + orderInfo.shippingCost.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span>Thuế</span>
                  <span>${{ orderInfo.tax.toFixed(2) }}</span>
                </div>
                <div v-if="orderInfo.discount > 0" class="flex justify-between text-sm text-green-600">
                  <span>Giảm giá</span>
                  <span>-${{ orderInfo.discount.toFixed(2) }}</span>
                </div>
                <div class="border-t pt-2 mt-3">
                  <div class="flex justify-between text-lg font-semibold">
                    <span>Tổng cộng</span>
                    <span>${{ orderInfo.total.toFixed(2) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Shipping & Payment Info -->
          <div class="space-y-6">
            <!-- Shipping Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h3 class="text-xl font-semibold mb-4">Thông tin giao hàng</h3>
              <div class="space-y-3">
                <div>
                  <p class="text-sm text-gray-500">Người nhận</p>
                  <p class="font-medium">{{ orderInfo.shipping.fullName }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Số điện thoại</p>
                  <p class="font-medium">{{ orderInfo.shipping.phone }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Email</p>
                  <p class="font-medium">{{ orderInfo.shipping.email }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Địa chỉ</p>
                  <p class="font-medium">{{ orderInfo.shipping.address }}</p>
                  <p class="text-sm text-gray-600">{{ orderInfo.shipping.district }}, {{ orderInfo.shipping.city }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Phương thức giao hàng</p>
                  <p class="font-medium">{{ getShippingMethodText(orderInfo.shipping.method) }}</p>
                </div>
                <div v-if="orderInfo.shipping.notes">
                  <p class="text-sm text-gray-500">Ghi chú</p>
                  <p class="font-medium">{{ orderInfo.shipping.notes }}</p>
                </div>
              </div>
            </div>
  
            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h3 class="text-xl font-semibold mb-4">Thông tin thanh toán</h3>
              <div class="space-y-3">
                <div>
                  <p class="text-sm text-gray-500">Phương thức thanh toán</p>
                  <p class="font-medium">{{ getPaymentMethodText(orderInfo.payment.method) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Trạng thái thanh toán</p>
                  <span :class="getPaymentStatusClass(orderInfo.payment.status)" class="inline-block px-3 py-1 rounded-full text-sm font-medium">
                    {{ getPaymentStatusText(orderInfo.payment.status) }}
                  </span>
                </div>
                <div v-if="orderInfo.payment.transactionId">
                  <p class="text-sm text-gray-500">Mã giao dịch</p>
                  <p class="font-medium font-mono text-sm">{{ orderInfo.payment.transactionId }}</p>
                </div>
                <div v-if="orderInfo.payment.method === 'card' && orderInfo.payment.cardLast4">
                  <p class="text-sm text-gray-500">Thẻ thanh toán</p>
                  <p class="font-medium">**** **** **** {{ orderInfo.payment.cardLast4 }}</p>
                </div>
              </div>
            </div>
  
            <!-- Tracking Information -->
            <div v-if="orderInfo.tracking.trackingNumber" class="bg-white rounded-lg shadow-md p-6">
              <h3 class="text-xl font-semibold mb-4">Thông tin vận chuyển</h3>
              <div class="space-y-3">
                <div>
                  <p class="text-sm text-gray-500">Mã vận đơn</p>
                  <p class="font-medium font-mono">{{ orderInfo.tracking.trackingNumber }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Đơn vị vận chuyển</p>
                  <p class="font-medium">{{ orderInfo.tracking.carrier }}</p>
                </div>
                <button class="w-full mt-3 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                  Theo dõi đơn hàng
                </button>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
          <button @click="downloadInvoice" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
            📄 Tải hóa đơn
          </button>
          <button @click="goToOrders" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
            📋 Xem đơn hàng của tôi
          </button>
          <button @click="continueShopping" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
            🛍️ Tiếp tục mua sắm
          </button>
        </div>
  
        <!-- Support Info -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6 text-center">
          <h3 class="text-lg font-semibold mb-2">Cần hỗ trợ?</h3>
          <p class="text-gray-600 mb-4">Liên hệ với chúng tôi nếu bạn có bất kỳ câu hỏi nào về đơn hàng</p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="tel:1900123456" class="flex items-center justify-center text-blue-600 hover:text-blue-800">
              📞 1900 123 456
            </a>
            <a href="mailto:support@example.com" class="flex items-center justify-center text-blue-600 hover:text-blue-800">
              ✉️ support@example.com
            </a>
            <a href="#" class="flex items-center justify-center text-blue-600 hover:text-blue-800">
              💬 Chat trực tuyến
            </a>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  
  // Sample order data
  const orderInfo = ref({
    orderNumber: 'ORD-' + Date.now(),
    orderDate: new Date(),
    estimatedDelivery: new Date(Date.now() + 5 * 24 * 60 * 60 * 1000), // 5 days from now
    status: 'confirmed',
    items: [
      { id: 1, name: 'iPhone 15 Pro Max 256GB', price: 1199.99, quantity: 1 },
      { id: 2, name: 'AirPods Pro (2nd Generation)', price: 249.99, quantity: 1 },
      { id: 3, name: 'iPhone 15 Case - MagSafe', price: 49.99, quantity: 2 }
    ],
    subtotal: 1549.96,
    shippingCost: 0,
    tax: 154.99,
    discount: 50.00,
    total: 1654.95,
    shipping: {
      fullName: 'Nguyễn Văn A',
      phone: '0123456789',
      email: 'nguyenvana@email.com',
      address: '123 Đường ABC, Phường XYZ',
      district: 'Quận 1',
      city: 'TP. Hồ Chí Minh',
      method: 'standard',
      notes: 'Giao hàng trong giờ hành chính'
    },
    payment: {
      method: 'card',
      status: 'paid',
      transactionId: 'TXN123456789',
      cardLast4: '1234'
    },
    tracking: {
      trackingNumber: 'VN123456789',
      carrier: 'Giao Hàng Nhanh'
    }
  });
  
  const orderSteps = ref([
    {
      title: 'Xác nhận',
      description: 'Đơn hàng đã được xác nhận',
      completed: true,
      active: false,
      date: new Date()
    },
    {
      title: 'Đóng gói',
      description: 'Đơn hàng đang được đóng gói',
      completed: false,
      active: true,
      date: null
    },
    {
      title: 'Vận chuyển',
      description: 'Đơn hàng đang được vận chuyển',
      completed: false,
      active: false,
      date: null
    },
    {
      title: 'Giao hàng',
      description: 'Đơn hàng đã được giao',
      completed: false,
      active: false,
      date: null
    }
  ]);
  
  // Computed properties
  const totalItems = computed(() => {
    return orderInfo.value.items.reduce((count, item) => count + item.quantity, 0);
  });
  
  // Methods  
  const getStatusClass = (status) => {
    const classes = {
      'pending': 'bg-yellow-100 text-yellow-800',
      'confirmed': 'bg-blue-100 text-blue-800',
      'processing': 'bg-purple-100 text-purple-800',
      'shipped': 'bg-indigo-100 text-indigo-800',
      'delivered': 'bg-green-100 text-green-800',
      'cancelled': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
  };
  
  const getStatusText = (status) => {
    const texts = {
      'pending': 'Chờ xử lý',
      'confirmed': 'Đã xác nhận',
      'processing': 'Đang xử lý',
      'shipped': 'Đã gửi hàng',
      'delivered': 'Đã giao hàng',
      'cancelled': 'Đã hủy'
    };
    return texts[status] || 'Không xác định';
  };
  
  const getPaymentStatusClass = (status) => {
    const classes = {
      'pending': 'bg-yellow-100 text-yellow-800',
      'paid': 'bg-green-100 text-green-800',
      'failed': 'bg-red-100 text-red-800',
      'refunded': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
  };
  
  const getPaymentStatusText = (status) => {
    const texts = {
      'pending': 'Chờ thanh toán',
      'paid': 'Đã thanh toán',
      'failed': 'Thanh toán thất bại',
      'refunded': 'Đã hoàn tiền'
    };
    return texts[status] || 'Không xác định';
  };
  
  const getShippingMethodText = (method) => {
    const methods = {
      'standard': 'Giao hàng tiêu chuẩn (5-7 ngày)',
      'express': 'Giao hàng nhanh (2-3 ngày)',
      'overnight': 'Giao hàng hỏa tốc (1 ngày)'
    };
    return methods[method] || 'Không xác định';
  };
  
  const getPaymentMethodText = (method) => {
    const methods = {
      'card': 'Thẻ tín dụng/ghi nợ',
      'momo': 'Ví MoMo',
      'zalopay': 'ZaloPay',
      'cod': 'Thanh toán khi nhận hàng'
    };
    return methods[method] || 'Không xác định';
  };
  
  const downloadInvoice = () => {
    // Simulate download
    alert('Đang tải hóa đơn...');
    console.log('Downloading invoice for order:', orderInfo.value.orderNumber);
  };
  
  const goToOrders = () => {
    // Navigate to orders page
    alert('Chuyển đến trang đơn hàng của tôi...');
    console.log('Navigating to orders page');
  };
  
  const continueShopping = () => {
    // Navigate to shop
    alert('Chuyển đến trang sản phẩm...');
    console.log('Navigating to shop');
  };
  
  // Load order data from URL params or localStorage
  onMounted(() => {
    // In real app, you would get order ID from route params
    // and fetch order data from API
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('orderId');
    
    if (orderId) {
      console.log('Loading order data for ID:', orderId);
      // Fetch order data from API
    }
  });
  </script>
  
  <style scoped>
  .order-confirmation {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }
  
  /* Progress bar custom styles */
  .relative::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 100%;
    transform: translateY(-50%);
    width: 100%;
    height: 2px;
    background-color: #e5e7eb;
    z-index: -1;
  }
  
  .relative.completed::after {
    background-color: #10b981;
  }
  
  /* Responsive progress steps */
  @media (max-width: 640px) {
    .relative::after {
      display: none;
    }
  }
  </style>