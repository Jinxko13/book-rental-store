<template>
  <div class="container mx-auto px-12 py-8 ">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Đơn thuê của tôi</h2>
    </div>

    <a-card class="mb-6">
      <div class="flex flex-wrap gap-4">
        <a-select v-model:value="selectedStatus" placeholder="Lọc theo trạng thái" style="width: 200px" @change="handleSearch">
          <a-select-option value="">Tất cả trạng thái</a-select-option>
          <a-select-option value="renting">Đang thuê</a-select-option>
          <a-select-option value="overdue">Quá hạn</a-select-option>
          <a-select-option value="returned">Đã trả</a-select-option>
          <a-select-option value="cancelled">Đã hủy</a-select-option>
        </a-select>

        <a-button type="primary" @click="handleSearch">
          <template #icon>
            <SearchOutlined />
          </template>
          Lọc
        </a-button>
      </div>
    </a-card>

    <a-table :dataSource="rentals" :columns="columns" :pagination="pagination" :loading="loading"
      @change="handleTableChange" rowKey="id" :scroll="{ x: 1200 }">
      <template #bodyCell="{ column, record }">
        <!-- Ngày thuê -->
        <template v-if="column.key === 'rental_date'">
          {{ formatDate(record.rental_date) }}
        </template>

        <!-- Ngày trả dự kiến -->
        <template v-if="column.key === 'due_date'">
          {{ formatDate(record.due_date) }}
        </template>

        <!-- Trạng thái -->
        <template v-if="column.key === 'status'">
          <a-tag :color="getStatusColor(record.status)">
            {{ getStatusText(record.status) }}
          </a-tag>
        </template>

        <!-- Tiền cọc -->
        <template v-if="column.key === 'deposit'">
          {{ formatPrice(record.deposit) }}
        </template>

        <!-- Phí thuê -->
        <template v-if="column.key === 'rental_fee'">
          {{ formatPrice(record.rental_fee) }}
        </template>

        <!-- Hành động -->
        <template v-if="column.key === 'actions'">
          <div class="flex gap-2">
            <a-button type="primary" size="small" @click="showDetailsModal(record)">
              <template #icon>
                <EyeOutlined />
              </template>
              Chi tiết
            </a-button>
            <a-button v-if="record.status === 'renting'" type="primary" danger size="small"
              @click="showCancelModal(record)" :loading="cancelLoading && selectedRental?.id === record.id">
              <template #icon>
                <CloseOutlined />
              </template>
              Hủy đơn
            </a-button>
          </div>
        </template>
      </template>
    </a-table>

    <!-- Modal Chi tiết đơn thuê -->
    <a-modal :title="'Chi tiết đơn thuê #' + selectedRental?.id" :visible="detailsModalVisible" @cancel="cancelDetailsModal"
      :confirmLoading="confirmLoading" width="800px" :maskClosable="false">
      <div class="px-8">
        <div class="mb-4">
          <h3 class="font-bold mb-2">Thông tin đơn thuê</h3>
          <div class="flex flex-col gap-2">
            <div><strong>Ngày thuê:</strong> {{ formatDate(selectedRental?.rental_date) }}</div>
            <div><strong>Ngày trả dự kiến:</strong> {{ formatDate(selectedRental?.due_date) }}</div>
            <div><strong>Tiền cọc:</strong> {{ formatPrice(selectedRental?.deposit) }}</div>
            <div><strong>Phí thuê:</strong> {{ formatPrice(selectedRental?.rental_fee) }}</div>
            <div><strong>Đã thanh toán:</strong> {{ formatPrice(selectedRental?.paid) }}</div>
            <div><strong>Trạng thái:</strong>
              <a-tag :color="getStatusColor(selectedRental?.status)">
                {{ getStatusText(selectedRental?.status) }}
              </a-tag>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <h3 class="font-bold mb-2">Danh sách sách</h3>
          <a-table :dataSource="selectedRental?.rental_details" :columns="detailsColumns" :pagination="false"
            rowKey="id" :scroll="{ x: 800 }">
            <template #bodyCell="{ column, record }">
              <template v-if="column.key === 'book_title'">
                {{ record.book?.title }}
              </template>
              <template v-if="column.key === 'book_price'">
                {{ formatPrice(record.book_price) }}
              </template>
              <template v-if="column.key === 'quantity'">
                {{ record.quantity }}
              </template>
            </template>
          </a-table>
        </div>
      </div>

      <template #footer>
        <a-button @click="cancelDetailsModal">Đóng</a-button>
      </template>
    </a-modal>

    <a-modal :title="'Chi tiết đơn thuê #' + selectedRental?.id" :visible="detailsModalVisible"
      @cancel="cancelDetailsModal" :confirmLoading="confirmLoading" width="800px" :maskClosable="false">
      <div class="px-8">
        <div class="mb-4">
          <h3 class="font-bold mb-2">Thông tin đơn thuê</h3>
          <div class="flex flex-col gap-2">
            <div><strong>Ngày thuê:</strong> {{ formatDate(selectedRental?.rental_date) }}</div>
            <div><strong>Ngày trả dự kiến:</strong> {{ formatDate(selectedRental?.due_date) }}</div>
            <div><strong>Tiền cọc:</strong> {{ formatPrice(selectedRental?.deposit) }}</div>
            <div><strong>Phí thuê:</strong> {{ formatPrice(selectedRental?.rental_fee) }}</div>
            <div><strong>Đã thanh toán:</strong> {{ formatPrice(selectedRental?.paid) }}</div>
            <div><strong>Trạng thái:</strong>
              <a-tag :color="getStatusColor(selectedRental?.status)">
                {{ getStatusText(selectedRental?.status) }}
              </a-tag>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <h3 class="font-bold mb-2">Danh sách sách</h3>
          <a-table :dataSource="selectedRental?.rental_details" :columns="detailsColumns" :pagination="false"
            rowKey="id" :scroll="{ x: 800 }">
            <template #bodyCell="{ column, record }">
              <template v-if="column.key === 'book_title'">
                {{ record.book?.title }}
              </template>
              <template v-if="column.key === 'book_price'">
                {{ formatPrice(record.book_price) }}
              </template>
              <template v-if="column.key === 'quantity'">
                {{ record.quantity }}
              </template>
            </template>
          </a-table>
        </div>
      </div>

      <template #footer>
        <a-button @click="cancelDetailsModal">Đóng</a-button>
      </template>
    </a-modal>

    <!-- Modal xác nhận hủy đơn -->
    <a-modal title="Hủy đơn thuê" :visible="cancelModalVisible" @ok="confirmCancelOrder" @cancel="cancelCancelModal"
      :confirmLoading="cancelLoading" :maskClosable="false" :okText="canCancelOnline ? 'Xác nhận hủy' : 'Đã hiểu'"
      cancelText="Đóng" :okType="canCancelOnline ? 'danger' : 'primary'">
      <div class="py-4">
        <!-- Trường hợp có thể hủy online (paid = 0.00) -->
        <template v-if="canCancelOnline">
          <p class="mb-4">
            <strong>Bạn có chắc chắn muốn hủy đơn thuê #{{ selectedRental?.id }}?</strong>
          </p>
          <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
            <p class="text-yellow-800 text-sm">
              <strong>Lưu ý:</strong> Khi hủy đơn thuê, bạn sẽ cần trả lại tất cả sách đã thuê.
              Tiền cọc sẽ được hoàn trả sau khi kiểm tra tình trạng sách.
            </p>
          </div>
          <div class="text-sm text-gray-600">
            <p><strong>Ngày thuê:</strong> {{ formatDate(selectedRental?.rental_date) }}</p>
            <p><strong>Tổng giá trị:</strong> {{ formatPrice(selectedRental?.rental_fee) }}</p>
          </div>
        </template>

        <!-- Trường hợp đã thanh toán, không thể hủy online -->
        <template v-else>
          <div class="text-center">
            <div class="mb-4">
              <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                <ExclamationCircleOutlined class="text-orange-500 text-2xl" />
              </div>
              <h3 class="text-lg font-semibold mb-2">Không thể hủy đơn online</h3>
              <p class="text-gray-600 mb-4">
                Đơn thuê #{{ selectedRental?.id }} đã được thanh toán nên không thể hủy trực tuyến.
              </p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-4">
              <p class="text-blue-800 text-sm">
                <strong>Để hủy đơn thuê này, bạn cần:</strong><br>
                • Đến trực tiếp cửa hàng/thư viện<br>
                • Mang theo giấy tờ tùy thân<br>
                • Trả lại tất cả sách đã thuê<br>
                • Hoàn tất thủ tục hủy với nhân viên
              </p>
            </div>

            <div class="text-sm text-gray-600 text-left">
              <p><strong>Thông tin đơn thuê:</strong></p>
              <p><strong>Ngày thuê:</strong> {{ formatDate(selectedRental?.rental_date) }}</p>
              <p><strong>Đã thanh toán:</strong> {{ formatPrice(selectedRental?.paid) }}</p>
              <p><strong>Tổng giá trị:</strong> {{ formatPrice(selectedRental?.rental_fee) }}</p>
            </div>
          </div>
        </template>
      </div>
    </a-modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { message } from 'ant-design-vue';
import {
  SearchOutlined,
  EyeOutlined,
  CheckOutlined,
  CloseOutlined,
  ExclamationCircleOutlined
} from '@ant-design/icons-vue';
import { format } from 'date-fns';
import { vi } from 'date-fns/locale';

const rentalApi = useRentalOrders();
// Data
const rentals = ref([]);
const loading = ref(false);
const selectedStatus = ref('');
const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0
});
const selectedRental = ref(null);
const detailsModalVisible = ref(false);
const confirmLoading = ref(false);
const cancelModalVisible = ref(false);
const cancelLoading = ref(false);

// Computed property to check if order can be cancelled online
const canCancelOnline = computed(() => {
  if (!selectedRental.value) return false;
  const paid = parseFloat(selectedRental.value.paid || 0);
  return paid === 0;
});

// Columns cho bảng chính
const columns = [
  {
    title: 'Mã đơn',
    dataIndex: 'id',
    key: 'id',
    width: 100
  },
  {
    title: 'Ngày thuê',
    key: 'rental_date',
    width: 150
  },
  {
    title: 'Ngày trả dự kiến',
    key: 'due_date',
    width: 150
  },
  {
    title: 'Tiền cọc',
    key: 'deposit',
    width: 150
  },
  {
    title: 'Phí thuê',
    key: 'rental_fee',
    width: 150
  },
  {
    title: 'Trạng thái',
    key: 'status',
    width: 150
  },
  {
    title: 'Hành động',
    key: 'actions',
    width: 300
  }
];

// Columns cho bảng chi tiết sách
const detailsColumns = [
  {
    title: 'Tên sách',
    key: 'book_title',
    width: 300
  },
  {
    title: 'Giá sách',
    key: 'book_price',
    width: 150
  },
  {
    title: 'Số lượng',
    key: 'quantity',
    width: 100
  }
];

// API calls
const fetchRentals = async (params = {}) => {
  loading.value = true;
  try {
    const response = await rentalApi.getUserRentals(params);
    if (response.data) {
      rentals.value = response.data.data;
      pagination.total = response.data.data.length;
    }
  } catch (error) {
    console.error('Error fetching rentals:', error);
  } finally {
    loading.value = false;
  }
};

// Status helpers
const getStatusColor = (status) => {
  const colors = {
    renting: 'blue',
    overdue: 'red',
    returned: 'green',
    cancel: 'gray'
  };
  return colors[status] || 'default';
};

const getStatusText = (status) => {
  const texts = {
    renting: 'Đang thuê',
    overdue: 'Quá hạn',
    returned: 'Đã trả',
    cancel: 'Đã hủy'
  };
  return texts[status] || 'Không xác định';
};

// Modal handlers
const showDetailsModal = (record) => {
  selectedRental.value = record;
  detailsModalVisible.value = true;
};

const cancelDetailsModal = () => {
  selectedRental.value = null;
  detailsModalVisible.value = false;
};

// Search handler
const handleSearch = () => {
  const params = {
    page: pagination.current,
    limit: pagination.pageSize
  };
  
  if (selectedStatus.value) {
    params.status = selectedStatus.value;
  }
  
  fetchRentals(params);
};

const cancelOrder = async (orderId) => {
  try {
    const response = await rentalApi.cancelRental(orderId);
    return response;
  } catch (error) {
    console.error('Error canceling order:', error);
    throw error;
  }
};

// Cancel order modal handlers
const showCancelModal = (record) => {
  selectedRental.value = record;
  cancelModalVisible.value = true;
};

const cancelCancelModal = () => {
  selectedRental.value = null;
  cancelModalVisible.value = false;
};

const confirmCancelOrder = async () => {
  // console.log(selectedRental.value);

  if (!selectedRental.value) return;

  cancelLoading.value = true;
  try {
    await cancelOrder(selectedRental.value.id);

    const index = rentals.value.findIndex(rental => rental.id === selectedRental.value.id);
    if (index !== -1) {
      rentals.value[index].status = 'cancelled';
    }

    message.success('Hủy đơn thuê thành công!');
    cancelCancelModal();

    handleSearch();
  } catch (error) {
    message.error('Có lỗi xảy ra khi hủy đơn thuê. Vui lòng thử lại!');
  } finally {
    cancelLoading.value = false;
  }
};

// Table handler
const handleTableChange = (pag) => {
  pagination.current = pag.current;
  pagination.pageSize = pag.pageSize;
  handleSearch();
};

// Setup initial data
onMounted(() => {
  handleSearch();
});
</script>