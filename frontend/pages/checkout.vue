<template>
    <div class="rental-confirmation bg-white rounded-lg p-12 w-full max-w-4xl mx-auto mt-10">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold mb-2">Xác Nhận Đơn Thuê Sách</h1>
        <p class="text-gray-600">Vui lòng xem lại đơn thuê sách và hoàn tất đặt chỗ</p>
    </div>

    <!-- Order Summary -->
    <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Tóm Tắt Đơn Hàng</h2>
        <div class="space-y-3">
            <div v-for="item in selectedItems" :key="item.id" 
                class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0">
                <div class="flex-1">
                    <h3 class="font-medium">{{ item.title || 'Tên sách không xác định' }}</h3>
                    <p class="text-sm text-gray-600">{{ item.author || 'Tác giả không xác định' }}</p>
                    <p class="text-sm text-gray-500">Ngày trả: {{ formatDate(returnDate) }}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">{{ formatPrice(item.price * item.quantity) }}</p>
                    <p class="text-sm text-gray-500">SL: {{ item.quantity }}</p>
                </div>
            </div>
        </div>
        
        <!-- Total -->
        <div class="border-t border-gray-300 pt-4 mt-4 space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Phí thuê:</span>
                <span class="text-sm">{{ formatPrice(rentalFee) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Phí đặt cọc:</span>
                <span class="text-sm">{{ formatPrice(depositFee) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Giảm giá:</span>
                <span class="text-sm">-{{ formatPrice(discountAmount) }}</span>
            </div>
            <div class="flex justify-between items-center border-t border-gray-200 pt-2">
                <span class="text-lg font-semibold">Tổng phí thuê:</span>
                <span class="text-lg font-bold text-blue-600">{{ formatPrice(totalAmount - discountAmount) }}</span>
            </div>
            <p class="text-sm text-gray-500">{{ selectedItems.length }} cuốn sách đến {{ formatDate(returnDate) }}</p>
            <p class="text-sm text-gray-500">Tiền đặt cọc sẽ được hoàn trả tại quầy khi quý khách trả sách.</p>
        </div>
    </div>

    <!-- Promotion apply -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Mã giảm giá</h2>
        <div class="flex gap-2">
            <input 
            v-model="promoCode"
            type="text" 
            placeholder="Nhập mã giảm giá"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <button 
            @click="applyPromoCode"
            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200"
            >
            Áp dụng
            </button>
        </div>
    </div>

    <!-- Return Date Selection -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Ngày Trả Sách</h2>
        <div class="max-w-md">
            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ngày trả sách *</label>
            <input type="date" v-model="returnDate" 
                    :min="minReturnDate"
                    :max="maxReturnDate"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            <div class="mt-2 text-sm text-gray-600">
                <p v-if="rentalDays > 0">Thời gian thuê: {{ rentalDays }} ngày</p>
                <p class="text-gray-500">Thời gian thuê tối đa là 30 ngày</p>
            </div>
        </div>
    </div>

    <!-- Payment Method -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Phương Thức Thanh Toán</h2>
        <div class="space-y-3">
            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="paymentMethod" value="store" class="mr-3">
                <div class="flex-1">
                    <div class="font-medium">Trả tiền tại cửa hàng</div>
                    <div class="text-sm text-gray-600">Thanh toán khi đến nhận sách tại cửa hàng</div>
                </div>
                <div class="text-green-600 font-medium">Khuyến nghị</div>
            </label>
            
            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="radio" v-model="paymentMethod" value="online" class="mr-3">
                <div class="flex-1">
                    <div class="font-medium">Thanh toán trực tuyến</div>
                    <div class="text-sm text-gray-600">Thanh toán an toàn trực tuyến qua thẻ tín dụng hoặc ví điện tử</div>
                </div>
            </label>
        </div>
    </div>

    <!-- Store Information -->
    <div class="mb-8 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-3 flex items-center">
            <span class="mr-2">📍</span> Thông Tin Cửa Hàng
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="font-medium">Địa chỉ:</p>
                <p class="text-gray-700">123 Đường Chính, Trung Tâm<br>Thành phố, Tỉnh 12345</p>
            </div>
            <div>
                <p class="font-medium">Giờ làm việc:</p>
                <p class="text-gray-700">Thứ 2 - Thứ 6: 9:00 - 20:00<br>Thứ 7 - Chủ nhật: 10:00 - 18:00</p>
            </div>
            <div>
                <p class="font-medium">Liên hệ:</p>
                <p class="text-gray-700">Điện thoại: (555) 123-4567<br>Email: info@bookrental.com</p>
            </div>
            <div>
                <p class="font-medium">Hướng dẫn nhận sách:</p>
                <p class="text-gray-700">Mang theo email xác nhận và CMND/CCCD</p>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions -->
    <div class="mb-8">
        <label class="flex items-start">
            <input type="checkbox" v-model="agreedToTerms" class="mt-1 mr-3">
            <div class="text-sm">
                <span>Tôi đồng ý với </span>
                <a href="#" class="text-blue-600 hover:underline">Điều khoản và Điều kiện</a>
                <span> và </span>
                <a href="#" class="text-blue-600 hover:underline">Chính sách thuê sách</a>
                <div class="text-gray-600 mt-1">
                    • Sách phải được trả đúng hạn để tránh phí phạt chậm trả<br>
                    • Sách bị hư hỏng hoặc mất sẽ bị tính phí thay thế<br>
                    • Đặt chỗ có hiệu lực trong 24 giờ kể từ khi xác nhận
                </div>
            </div>
        </label>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <button @click="goBack" 
                class="flex-1 bg-gray-200 text-gray-800 py-3 px-6 rounded-lg hover:bg-gray-300 transition-colors">
            ← Quay lại giỏ hàng
        </button>
        
        <button @click="confirmRental" 
                :disabled="!canConfirm"
                :class="[
                    'flex-1 py-3 px-6 rounded-lg font-medium transition-colors',
                    canConfirm 
                    ? 'bg-blue-600 text-white hover:bg-blue-700' 
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]">
            <span v-if="paymentMethod === 'online'">💳 Thanh toán ngay & Xác nhận thuê</span>
            <span v-else>📋 Xác nhận đơn thuê sách</span>
        </button>
    </div>

    <!-- Security Badge -->
    <div class="flex justify-center items-center mt-6 text-gray-500 text-sm">
        <span class="mr-1">🔒</span> 
        <span>Thông tin của bạn được bảo mật và mã hóa</span>
    </div>
</div>
</template>
    
<script setup>
import { ref, computed, onMounted, h } from 'vue';
import { message, Modal } from 'ant-design-vue';

const route = useRouter();
const rentalApi = useRentalOrders();
const productStore = useProductStore()

// Reactive data
const selectedItems = ref([]);
const returnDate = ref('');
const promoCode = ref('')
const discountAmount = ref(0)
const paymentMethod = ref('store'); // Default to store payment
const agreedToTerms = ref(false);
const promotionApi = usePromotions();
const promotion = ref(null)

// Constants
const today = new Date();
const RENTAL_BASE_PRICE = 3000; // Base price per day
const MAX_RENTAL_DAYS = 30;

// Date constraints
const minReturnDate = computed(() => {
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    return tomorrow.toISOString().split('T')[0];
});

const maxReturnDate = computed(() => {
    const maxDate = new Date(today);
    maxDate.setDate(today.getDate() + MAX_RENTAL_DAYS);
    return maxDate.toISOString().split('T')[0];
});

// Computed properties
const rentalDays = computed(() => {
    if (!returnDate.value) return 0;
    const returnDateObj = new Date(returnDate.value);
    const rentalDateObj = new Date(today);
    const diffTime = returnDateObj - rentalDateObj;
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
});

const priceMultiplier = computed(() => {
    const days = rentalDays.value;
    if (days <= 7) return 1.0;
    if (days <= 14) return 1.25;
    if (days <= 21) return 1.5;
    return 1.75;
});

const rentalFee = computed(() => {
    if (rentalDays.value <= 0) return 0;
    
    return selectedItems.value.reduce((total, item) => {
        return total + (RENTAL_BASE_PRICE * rentalDays.value * priceMultiplier.value * item.quantity);
    }, 0);
});

const depositFee = computed(() => {
    return selectedItems.value.reduce((total, item) => {
        return total + (item.price * item.quantity);
    }, 0);
});

const totalAmount = computed(() => {
    return rentalFee.value + depositFee.value;
});

const canConfirm = computed(() => {
    return returnDate.value !== '' &&
            rentalDays.value > 0 &&
            agreedToTerms.value &&
            selectedItems.value.length > 0;
});

const initializeDefaultReturnDate = () => {
    const defaultReturnDate = new Date(today);
    defaultReturnDate.setDate(today.getDate() + 7);
    returnDate.value = defaultReturnDate.toISOString().split('T')[0];
};

const loadCartItems = () => {
    try {
        const selectedCartItems = productStore.getCheckedProducts();
        
        if (selectedCartItems.length === 0) {
            message.warning('Không có sản phẩm nào được chọn. Đang chuyển hướng về giỏ hàng...');
            route.back();
            return;
        }
        
        selectedItems.value = selectedCartItems;
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu giỏ hàng:', error);
        selectedItems.value = [];
        message.error('Có lỗi xảy ra khi tải dữ liệu giỏ hàng');
    }
};

const clearSelectedFromCart = () => {
    try {
        productStore.clearCheckedProducts();
    } catch (error) {
        console.error('Lỗi khi cập nhật giỏ hàng:', error);
    }
};

// Event handlers
const goBack = () => {
    message.info('Đang quay lại giỏ hàng...');
    route.back();
};

const showConfirmationModal = () => {
    const paymentText = paymentMethod.value === 'online' ? 'Thanh toán trực tuyến' : 'Trả tiền tại cửa hàng';
    const buttonText = paymentMethod.value === 'online' ? 'Tiến hành thanh toán' : 'Xác nhận đặt chỗ';
    
    Modal.confirm({
        title: 'Xác nhận đơn thuê sách',
        content: () => 
            h('div', [
                h('p', `Bạn sắp xác nhận đơn thuê sách cho ${selectedItems.value.length} cuốn sách.`),
                h('p', [
                    h('strong', 'Tổng tiền: '),
                    h('span', formatPrice(totalAmount.value))
                ]),
                h('p', [
                    h('strong', 'Ngày trả: '),
                    h('span', formatDate(returnDate.value))
                ]),
                h('p', [
                    h('strong', 'Số ngày thuê: '),
                    h('span', `${rentalDays.value} ngày`)
                ]),
                h('p', [
                    h('strong', 'Phương thức thanh toán: '),
                    h('span', paymentText)
                ])
            ]),
        okText: buttonText,
        cancelText: 'Hủy',
        onOk() {
            processRentalOrder();
        }
    });
};

const confirmRental = () => {
    if (!canConfirm.value) {
        message.warning('Vui lòng chọn ngày trả và đồng ý với các điều khoản.');
        return;
    }
    showConfirmationModal();
};

const processRentalOrder = async () => {
    const loadingMessage = paymentMethod.value === 'online' 
        ? 'Đang xử lý thanh toán...' 
        : 'Đang tạo đặt chỗ...';

    const rentalOrderData = createRentalOrderData();

    try {
        message.loading(loadingMessage, 0);
        const orderNumber = 'RNT' + Date.now().toString().substr(-6);
        await rentalApi.createRentalOrder(rentalOrderData);
        
        showSuccessMessage(orderNumber);
        clearSelectedFromCart();
        route.push('/');
    } catch (error) {
        console.error('Lỗi khi tạo đơn thuê:', error);
        message.error('Không thể tạo đơn thuê sách. Vui lòng thử lại.');
    } finally {
        message.destroy();
    }
};

const createRentalOrderData = () => {
    const today = new Date();
    const rentalDate = today.toISOString().split('T')[0];
    return {
        rental_date: rentalDate,
        due_date: returnDate.value,
        books: selectedItems.value.map(item => ({
            book_id: item.id,
            quantity: item.quantity
        })),
        deposit: depositFee.value,
        paid: paymentMethod.value === 'online' ? totalAmount.value : 0,
        discount: promotion.value ? promotion.value.id : null,
    };
};

const showSuccessMessage = (orderNumber) => {
    if (paymentMethod.value === 'online') {
        message.success({
            content: `Thanh toán thành công! Đơn thuê sách #${orderNumber} đã được xác nhận. Vui lòng kiểm tra email để biết hướng dẫn nhận sách.`,
            duration: 3
        });
    } else {
        message.success({
            content: `Đặt chỗ thành công! Đơn thuê sách #${orderNumber} đã sẵn sàng để nhận. Vui lòng đến cửa hàng trong vòng 24 giờ.`,
            duration: 3
        });
    }
};

const applyPromoCode = async () => {
  if (!promoCode.value.trim()) {
    message.warning('Vui lòng nhập mã giảm giá.')
    return
  }
  try {
    const response = await promotionApi.applyPromotions(promoCode.value)
    if (response.success) {
      discountAmount.value = rentalFee.value * response.promotion.discount_percent / 100
      promotion.value = response.promotion
      message.success('Mã giảm giá đã được áp dụng thành công!')
    } 
  } catch (error) {
    message.error('Mã giảm giá không hợp lệ.')
  }
}

// Lifecycle
onMounted(async () => {
    loadCartItems();
    initializeDefaultReturnDate();
});

definePageMeta({
    middleware: 'auth'
})
</script>