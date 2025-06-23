<template>
    <!-- Modal Trả sách với Preview Chi phí -->
    <a-modal 
      :footer="null"
      title="Xác nhận trả sách" 
      :open="visible" 
      @cancel="handleCancel"
      :confirmLoading="confirmLoading"
      width="1000px">
      
      <!-- Bước 1: Đánh giá tình trạng sách -->
      <div v-if="returnStep === 1">
        <div class="mb-4">
          <p><strong>Đơn thuê:</strong> #{{ selectedRental?.id }}</p>
          <p><strong>Người thuê:</strong> {{ selectedRental?.user?.name }}</p>
          <p><strong>Ngày thuê:</strong> {{ formatDate(selectedRental?.rental_date) }}</p>
          <p><strong>Ngày trả dự kiến:</strong> {{ formatDate(selectedRental?.due_date) }}</p>
        </div>
        
        <div class="mb-4">
          <h4 class="font-medium mb-3">Đánh giá tình trạng sách khi trả:</h4>
          <div class="space-y-4">
            <div v-for="detail in selectedRental?.rental_details" :key="detail.id" 
                 class="border rounded-lg p-4 bg-gray-50">
              <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                  <h5 class="font-medium text-gray-900">{{ detail.book?.title }}</h5>
                  <p class="text-sm text-gray-600">Số lượng: {{ detail.quantity }}</p>
                </div>
              </div>
              
              <!-- Đánh giá tình trạng cho từng cuốn sách -->
              <div v-for="i in detail.quantity" :key="`${detail.id}-${i}`" 
                   class="mb-3 p-3 border border-gray-200 rounded bg-white">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-sm font-medium">Cuốn {{ i }}:</span>
                  <a-select 
                    v-model:value="bookConditions[`${detail.book.id}-${i}`]" 
                    placeholder="Chọn tình trạng"
                    style="width: 200px"
                    @change="updateBookCondition(detail.book.id, i, $event)">
                    <a-select-option value="good">
                      <span class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Tốt
                      </span>
                    </a-select-option>
                    <a-select-option value="damaged">
                      <span class="flex items-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        Bị hư hỏng
                      </span>
                    </a-select-option>
                    <a-select-option value="lost">
                      <span class="flex items-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        Mất
                      </span>
                    </a-select-option>
                  </a-select>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex justify-end gap-2">
          <a-button @click="handleCancel">Hủy</a-button>
          <a-button type="primary" @click="previewReturnCost" :loading="previewLoading">
            Xem trước chi phí
          </a-button>
        </div>
      </div>
  
      <!-- Bước 2: Preview chi phí trước khi trả -->
      <div v-if="returnStep === 2 && previewData">
        <div class="mb-6">
          <h3 class="text-lg font-bold mb-4 text-center">Thông tin chi phí trả sách</h3>
          
          <!-- Thông tin đơn thuê -->
          <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="font-semibold mb-3 text-blue-800">Thông tin đơn thuê</h4>
            <div class="grid grid-cols-2 gap-4">
              <div><strong>Mã đơn:</strong> #{{ previewData.rental_info.rental_id }}</div>
              <div><strong>Người thuê:</strong> {{ previewData.rental_info.user_name }}</div>
              <div><strong>Ngày thuê:</strong> {{ formatDate(previewData.rental_info.rental_date) }}</div>
              <div><strong>Ngày hết hạn:</strong> {{ formatDate(previewData.rental_info.due_date) }}</div>
              <div><strong>Tiền cọc:</strong> {{ formatPrice(previewData.rental_info.deposit) }}</div>
              <div><strong>Phí thuê:</strong> {{ formatPrice(previewData.rental_info.rental_fee) }}</div>
            </div>
          </div>
  
          <!-- Chi tiết sách -->
          <div class="mb-6">
            <h4 class="font-semibold mb-3">Chi tiết tình trạng sách</h4>
            <a-table 
              :dataSource="previewData.books_detail" 
              :pagination="false"
              :scroll="{ x: 600 }"
              rowKey="book_id">
              <a-table-column key="book_name" title="Tên sách" dataIndex="book_name" />
              <a-table-column key="status" title="Tình trạng">
                <template #default="{ record }">
                  <a-tag :color="getBookStatusColor(record.status)">
                    {{ getBookStatusText(record.status) }}
                  </a-tag>
                </template>
              </a-table-column>
              <a-table-column key="penalty_amount" title="Phí phạt">
                <template #default="{ record }">
                  {{ formatPrice(record.penalty_amount) }}
                </template>
              </a-table-column>
            </a-table>
          </div>
  
          <!-- Bảng tính chi phí -->
          <div class="mb-6">
            <h4 class="font-semibold mb-3">Bảng tính chi phí</h4>
            <div class="bg-white border rounded-lg overflow-hidden">
              <div class="p-4 space-y-3">
                <div class="flex justify-between items-center">
                  <span>Tiền cọc ban đầu:</span>
                  <span class="font-medium text-green-600">{{ formatPrice(previewData.rental_info.deposit) }}</span>
                </div>
                
                <div class="border-t pt-3">
                  <div class="text-sm text-gray-600 mb-2">Các khoản khấu trừ:</div>
                  
                  <div class="flex justify-between items-center pl-4">
                    <span>- Phí thuê sách:</span>
                    <span class="text-red-600">{{ formatPrice(previewData.fee_breakdown.rental_fee) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center pl-4" v-if="previewData.is_overdue">
                    <span>- Phí trễ hạn:</span>
                    <span class="text-red-600">{{ formatPrice(previewData.fee_breakdown.late_fee) }}</span>
                  </div>
                  
                  <div class="flex justify-between items-center pl-4" v-if="previewData.has_penalties">
                    <span>- Phí phạt (hư hỏng/mất):</span>
                    <span class="text-red-600">{{ formatPrice(previewData.fee_breakdown.penalty_fee) }}</span>
                  </div>
                </div>
                
                <div class="border-t pt-3">
                  <div class="flex justify-between items-center font-medium">
                    <span>Tổng khấu trừ:</span>
                    <span class="text-red-600">{{ formatPrice(previewData.fee_breakdown.total_deductions) }}</span>
                  </div>
                </div>
                
                <!-- Hiển thị kết quả cuối cùng: hoàn lại hoặc thu thêm -->
                <div class="border-t pt-3 -m-4 p-4 mt-3" 
                     :class="previewData.fee_breakdown.refund_amount >= 0 ? 'bg-green-50' : 'bg-red-50'">
                  <div class="flex justify-between items-center text-lg font-bold">
                    <span>
                      {{ previewData.fee_breakdown.refund_amount >= 0 ? 'Số tiền hoàn lại:' : 'Số tiền cần thu thêm:' }}
                    </span>
                    <span :class="previewData.fee_breakdown.refund_amount >= 0 ? 'text-green-600' : 'text-red-600'">
                      {{ formatPrice(Math.abs(previewData.fee_breakdown.refund_amount)) }}
                    </span>
                  </div>
                  
                  <!-- Thông báo rõ ràng về việc thu thêm tiền -->
                  <div v-if="previewData.fee_breakdown.refund_amount < 0" 
                       class="mt-2 text-sm text-red-700 bg-red-100 p-2 rounded border-l-4 border-red-400">
                    <div class="flex items-center">
                      <span class="font-medium">⚠️ Lưu ý:</span>
                      <span class="ml-2">Khách hàng cần thanh toán thêm {{ formatPrice(Math.abs(previewData.fee_breakdown.refund_amount)) }} do chi phí vượt quá tiền cọc.</span>
                    </div>
                  </div>
                  
                  <!-- Thông báo về việc hoàn tiền -->
                  <div v-else-if="previewData.fee_breakdown.refund_amount > 0" 
                       class="mt-2 text-sm text-green-700 bg-green-100 p-2 rounded border-l-4 border-green-400">
                    <div class="flex items-center">
                      <span class="font-medium">✅ Thông báo:</span>
                      <span class="ml-2">Sẽ hoàn lại {{ formatPrice(previewData.fee_breakdown.refund_amount) }} cho khách hàng.</span>
                    </div>
                  </div>
                  
                  <!-- Trường hợp vừa đủ -->
                  <div v-else 
                       class="mt-2 text-sm text-gray-700 bg-gray-100 p-2 rounded border-l-4 border-gray-400">
                    <div class="flex items-center">
                      <span class="font-medium">ℹ️ Thông báo:</span>
                      <span class="ml-2">Tiền cọc vừa đủ chi trả các khoản phí, không hoàn lại và không thu thêm.</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Cảnh báo nếu có -->
          <div v-if="previewData.is_overdue || previewData.has_penalties || previewData.fee_breakdown.refund_amount < 0" class="mb-4">
            <a-alert 
              :type="previewData.fee_breakdown.refund_amount < 0 ? 'error' : 'warning'" 
              show-icon>
              <template #message>
                <div>
                  <div v-if="previewData.fee_breakdown.refund_amount < 0" class="mb-2 font-medium text-red-600">
                    🚨 YÊU CẦU THANH TOÁN THÊM: {{ formatPrice(Math.abs(previewData.fee_breakdown.refund_amount)) }}
                  </div>
                  <div v-if="previewData.is_overdue" class="mb-1">
                    ⚠️ Đơn thuê đã quá hạn, phát sinh phí trễ hạn
                  </div>
                  <div v-if="previewData.has_penalties">
                    ⚠️ Có sách bị hư hỏng hoặc mất, phát sinh phí phạt
                  </div>
                </div>
              </template>
            </a-alert>
          </div>
  
          <!-- Trạng thái tổng thể -->
          <div class="mb-6 p-3 rounded-lg" :class="getOverallConditionClass(previewData.overall_condition)">
            <div class="flex items-center justify-between">
              <span class="font-medium">Tình trạng tổng thể:</span>
              <a-tag :color="getBookStatusColor(previewData.overall_condition)" class="text-sm">
                {{ getBookStatusText(previewData.overall_condition) }}
              </a-tag>
            </div>
          </div>
        </div>
        
        <div class="flex justify-end gap-2">
          <a-button @click="returnStep = 1">Quay lại</a-button>
          <a-button 
            :type="previewData.fee_breakdown.refund_amount < 0 ? 'danger' : 'primary'" 
            @click="confirmReturnBooks" 
            :loading="confirmLoading">
            {{ previewData.fee_breakdown.refund_amount < 0 ? 'Xác nhận và Thu tiền' : 'Xác nhận trả sách' }}
          </a-button>
        </div>
      </div>
    </a-modal>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  import { message } from 'ant-design-vue';
  import { format } from 'date-fns';
  import { vi } from 'date-fns/locale';
  
  // Props
  const props = defineProps({
    visible: {
      type: Boolean,
      default: false
    },
    selectedRental: {
      type: Object,
      default: () => null
    },
    rentalService: {
      type: Object,
      required: true
    }
  });
  
  // Emits
  const emit = defineEmits(['cancel', 'success']);
  
  // Reactive data
  const bookConditions = ref({});
  const returnStep = ref(1);
  const previewData = ref(null);
  const confirmLoading = ref(false);
  const previewLoading = ref(false);
  
  // Watch for modal visibility changes
  watch(() => props.visible, (newVal) => {
    if (newVal && props.selectedRental) {
      initializeBookConditions(props.selectedRental);
      returnStep.value = 1;
      previewData.value = null;
    }
  });
  
  // Methods
  const initializeBookConditions = (rental) => {
    bookConditions.value = {};
    if (rental.rental_details) {
      rental.rental_details.forEach(detail => {
        for (let i = 1; i <= detail.quantity; i++) {
          const key = `${detail.book.id}-${i}`;
          bookConditions.value[key] = 'good';
        }
      });
    }
  };
  
  const updateBookCondition = (bookId, bookIndex, condition) => {
    const key = `${bookId}-${bookIndex}`;
    bookConditions.value[key] = condition;
  };
  
  const validateBookConditions = () => {
    if (!props.selectedRental?.rental_details) return true;
    
    for (const detail of props.selectedRental.rental_details) {
      for (let i = 1; i <= detail.quantity; i++) {
        const key = `${detail.book.id}-${i}`;
        if (!bookConditions.value[key]) {
          message.error(`Chưa đánh giá tình trạng cho cuốn ${i} của sách "${detail.book?.title}"`);
          return false;
        }
      }
    }
    
    return true;
  };
  
  const getReturnData = () => {
    const returnData = [];
    
    Object.entries(bookConditions.value).forEach(([key, status]) => {
      const [bookId, bookIndex] = key.split('-');
      returnData.push({
        book_id: parseInt(bookId),
        book_index: parseInt(bookIndex),
        status: status
      });
    });
    
    return returnData;
  };
  
  const previewReturnCost = async () => {
    const allBooksEvaluated = validateBookConditions();
    if (!allBooksEvaluated) {
      return;
    }
    
    previewLoading.value = true;
    try {
      const returnData = {
        rental_id: props.selectedRental.id,
        book_conditions: getReturnData()
      };
      
      const response = await props.rentalService.previewReturnCost(returnData);
      
      if (response.success) {
        previewData.value = response;
        console.log(response)
        returnStep.value = 2;
      } else {
        message.error(response.message || 'Không thể tải thông tin chi phí!');
      }
    } catch (error) {
      console.error('Error previewing return cost:', error);
      message.error('Có lỗi xảy ra khi tải thông tin chi phí!');
    } finally {
      previewLoading.value = false;
    }
  };
  
  const confirmReturnBooks = async () => {
    const allBooksEvaluated = validateBookConditions();
    if (!allBooksEvaluated) {
      return;
    }
    
    // Xác nhận nếu cần thu thêm tiền
    if (previewData.value.fee_breakdown.refund_amount < 0) {
      const additionalAmount = Math.abs(previewData.value.fee_breakdown.refund_amount);
      const confirmed = confirm(
        `Khách hàng cần thanh toán thêm ${formatPrice(additionalAmount)}. Bạn có chắc chắn muốn tiếp tục?`
      );
      if (!confirmed) {
        return;
      }
    }
    
    confirmLoading.value = true;
    try {
      const returnData = {
        rental_id: props.selectedRental.id,
        book_conditions: getReturnData(),
        total_compensation: previewData.value.total_compensation,
        additional_payment_required: previewData.value.fee_breakdown.refund_amount < 0,
        additional_amount: previewData.value.fee_breakdown.refund_amount < 0 ? Math.abs(previewData.value.fee_breakdown.refund_amount) : 0
      };
      
      const response = await props.rentalService.returnRentalOrder(returnData);
      
      if (response.data.success) {
        if (previewData.value.fee_breakdown.refund_amount < 0) {
          message.success(`Trả sách thành công! Đã thu thêm ${formatPrice(Math.abs(previewData.value.fee_breakdown.refund_amount))}`);
        } else if (previewData.value.fee_breakdown.refund_amount > 0) {
          message.success(`Trả sách thành công! Đã hoàn lại ${formatPrice(previewData.value.fee_breakdown.refund_amount)}`);
        } else {
          message.success('Trả sách thành công!');
        }
        emit('success');
        handleCancel();
      }
    } catch (error) {
      console.error('Error returning books:', error);
      message.error('Có lỗi xảy ra khi trả sách!');
    } finally {
      confirmLoading.value = false;
    }
  };
  
  const handleCancel = () => {
    bookConditions.value = {};
    returnStep.value = 1;
    previewData.value = null;
    emit('cancel');
  };
  
  const getBookStatusColor = (status) => {
    const colors = {
      good: 'green',
      damaged: 'yellow',
      lost: 'red'
    };
    return colors[status] || 'default';
  };
  
  const getBookStatusText = (status) => {
    const texts = {
      good: 'Tốt',
      damaged: 'Bị hư hỏng',
      lost: 'Mất'
    };
    return texts[status] || 'Không xác định';
  };
  
  const getOverallConditionClass = (condition) => {
    const classes = {
      good: 'bg-green-50 border-green-200',
      damaged: 'bg-yellow-50 border-yellow-200',
      lost: 'bg-red-50 border-red-200'
    };
    return classes[condition] || 'bg-gray-50 border-gray-200';
  };
  </script>