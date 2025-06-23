<!-- pages/admin/overdue-orders.vue -->
<template>
  <div class="overdue-orders-page">
    <!-- Header -->
    <a-page-header
      title="Quản lý đơn quá hạn"
      sub-title="Danh sách các đơn hàng đã quá hạn sử dụng"
    >
      <template #extra>
        <a-space>
          <a-button @click="refreshData" :loading="loading">
            Làm mới
          </a-button>
          <a-button type="primary" @click="exportData">
            Xuất Excel
          </a-button>
        </a-space>
      </template>
    </a-page-header>

    <!-- Filters -->
    <a-card class="filter-card" :bordered="false">
      <div class="flex flex-wrap gap-4">

        <a-input
          v-model:value="filters.search"
          placeholder="Mã đơn, tên khách hàng, email..."
          style="width: 300px"
          allow-clear
          @press-enter="handleSearch"
        />
        <a-button type="primary" @click="handleSearch" :loading="loading">
          <template #icon>
            <SearchOutlined />
          </template>
          Tìm kiếm
        </a-button>
        <a-button @click="resetFilters">
          <template #icon>
            <ReloadOutlined />
          </template>
          Đặt lại
        </a-button>
      </div>
    </a-card>

    <!-- Statistics Cards -->
    <a-row :gutter="16" class="stats-row">
      <a-col :span="6">
        <a-card>
          <a-statistic
            title="Tổng đơn quá hạn"
            :value="statistics.total"
            :value-style="{ color: '#cf1322' }"
          />
        </a-card>
      </a-col>
      <a-col :span="6">
        <a-card>
          <a-statistic
            title="Đã xử lý"
            :value="statistics.processed"
            :value-style="{ color: '#52c41a' }"
          />
        </a-card>
      </a-col>
      <a-col :span="6">
        <a-card>
          <a-statistic
            title="Tổng tiền phạt"
            :value="statistics.totalFine"
            suffix="VNĐ"
            :value-style="{ color: '#faad14' }"
          />
        </a-card>
      </a-col>
      <a-col :span="6">
        <a-card>
          <a-statistic
            title="Quá hạn hôm nay"
            :value="statistics.todayOverdue"
            :value-style="{ color: '#ff4d4f' }"
          />
        </a-card>
      </a-col>
    </a-row>

    <!-- Data Table -->
    <a-card :bordered="false">
      <a-table
        :columns="columns"
        :data-source="orders"
        :loading="loading"
        :pagination="pagination"
        :scroll="{ x: 1200 }"
        @change="handleTableChange"
        row-key="id"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'id'">
            <a-button type="link" @click="viewOrderDetail(record)">
              {{ record.id }}
            </a-button>
          </template>

          <template v-if="column.key === 'status'">
            <a-tag :color="getStatusColor(record.status)">
              {{ getStatusText(record.status) }}
            </a-tag>
          </template>

          <template v-if="column.key === 'overdueDays'">
            <a-tag color="red">
              {{ getOverduedDays(record.due_date) }} ngày
            </a-tag>
          </template>

          <template v-if="column.key === 'due_date'">
            {{ formatDate(record.due_date) }}
          </template>

          <template v-if="column.key === 'user'">
            <div class="customer-info">
              <div>{{ record.user.name }}</div>
              <small class="text-gray">{{ record.user.phone }}</small>
            </div>
          </template>

          <template v-if="column.key === 'actions'">
            <a-space>
              <a-popconfirm title="Bạn có chắc muốn gửi nhắc nhở cho khách hàng này?" ok-text="Có" cancel-text="Không"
              @confirm="sendReminder(record.id)">
                <a-button type="primary" size="small">
                  Gửi nhắc nhở
                </a-button>
              </a-popconfirm>              
              <a-dropdown>
                <template #overlay>
                  <a-menu>
                    <a-menu-item @click="viewOrderDetail(record)">
                      Xem chi tiết
                    </a-menu-item>
                    <a-menu-item @click="showReturnModal(record)">
                      Trả sách
                    </a-menu-item>
                    <a-menu-divider />
                    <a-menu-item @click="extendDeadline(record)">
                      Gia hạn
                    </a-menu-item>
                  </a-menu>
                </template>
                <a-button size="small">
                  Thao tác
                </a-button>
              </a-dropdown>
            </a-space>
          </template>
        </template>
      </a-table>
    </a-card>

    <!-- Order Detail Modal -->
    <a-modal
      v-model:open="detailModal.visible"
      title="Chi tiết đơn hàng"
      width="800px"
      :footer="null"
    >
      <div v-if="detailModal.data" class="order-detail">
        <a-descriptions :column="2" bordered>
          <a-descriptions-item label="Mã đơn">
            {{ detailModal.data.id }}
          </a-descriptions-item>
          <a-descriptions-item label="Trạng thái">
            <a-tag :color="getStatusColor(detailModal.data.status)">
              {{ getStatusText(detailModal.data.status) }}
            </a-tag>
          </a-descriptions-item>
          <a-descriptions-item label="Khách hàng">
            {{ detailModal.data.user.name }}
          </a-descriptions-item>
          <a-descriptions-item label="Số điện thoại">
            {{ detailModal.data.user.phone }}
          </a-descriptions-item>
          <a-descriptions-item label="Ngày thuê">
            {{ formatDate(detailModal.data.rental_date) }}
          </a-descriptions-item>
          <a-descriptions-item label="Ngày hết hạn">
            {{ formatDate(detailModal.data.due_date) }}
          </a-descriptions-item>
          <!-- <a-descriptions-item label="Số ngày quá hạn">
            <a-tag color="red">{{ detailModal.data.overdueDays }} ngày</a-tag>
          </a-descriptions-item> -->
          <!-- <a-descriptions-item label="Tiền phạt">
            <span class="fine-amount">
              {{ formatPrice(detailModal.data.fineAmount) }}
            </span>
          </a-descriptions-item> -->
        </a-descriptions>

        <a-divider>Sản phẩm thuê</a-divider>
        <a-table
          :columns="productColumns"
          :data-source="detailModal.data.rental_details"
          :pagination="false"
          size="small">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'book'">
              {{ record.book?.title }}
            </template>
            <template v-if="column.key === 'price'">
              {{ record.book?.price }}
            </template>
            <template v-if="column.key === 'quantity'">
              {{ record.quantity }}
            </template>
          </template>
        </a-table>
      </div>
    </a-modal>

    <!-- Modal Gia hạn thuê -->
    <a-modal 
      title="Gia hạn thuê sách" 
      :open="extendModalVisible" 
      @cancel="cancelExtendModal"
      :confirmLoading="confirmLoading"
      @ok="handleExtendRental"
      width="500px">
      <div class="mb-4">
        <p><strong>Đơn thuê:</strong> #{{ selectedRental?.id }}</p>
        <p><strong>Người thuê:</strong> {{ selectedRental?.user?.name }}</p>
        <p><strong>Ngày trả hiện tại:</strong> {{ formatDate(selectedRental?.due_date) }}</p>
      </div>
      
      <div class="mb-4">
        <label class="block mb-2 font-medium">Chọn thời gian gia hạn:</label>
        <a-select v-model:value="extendDays" placeholder="Chọn số ngày gia hạn" style="width: 100%">
          <a-select-option :value="7">7 ngày</a-select-option>
          <a-select-option :value="14">14 ngày</a-select-option>
          <a-select-option :value="21">21 ngày</a-select-option>
          <a-select-option :value="30">30 ngày</a-select-option>
        </a-select>
      </div>
      
      <div v-if="extendDays" class="mb-4 p-3 bg-blue-50 rounded">
        <p><strong>Ngày trả mới:</strong> {{ getNewDueDate() }}</p>
      </div>
    </a-modal>

    <!-- Modal Trả sách -->
    <a-modal 
      title="Xác nhận trả sách" 
      :open="returnModalVisible" 
      @cancel="cancelReturnModal"
      :confirmLoading="confirmLoading"
      @ok="handleReturnBooks"
      width="800px">
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
                  v-model:value="bookConditions[`${detail.id}-${i}`]" 
                  placeholder="Chọn tình trạng"
                  style="width: 200px"
                  @change="updateBookCondition(detail.id, i, $event)">
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
              
              <!-- Phí bồi thường cho sách mất -->
              <div v-if="bookConditions[`${detail.id}-${i}`] === 'lost'" class="mt-2">
                <div class="flex justify-between items-center p-2 bg-red-50 rounded border border-red-200">
                  <span class="text-sm text-red-700">Phí bồi thường:</span>
                  <span class="font-medium text-red-700">{{ formatPrice(detail.book?.price * 2) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Tổng kết phí bồi thường -->
      <div v-if="getTotalCompensation() > 0" class="mb-4">
        <a-alert 
          type="warning" 
          show-icon>
          <template #message>
            <div class="flex justify-between items-center">
              <span>Tổng phí bồi thường:</span>
              <span class="font-bold text-lg">{{ formatPrice(getTotalCompensation()) }}</span>
            </div>
          </template>
        </a-alert>
      </div>
      
      <a-alert 
        message="Xác nhận trả sách" 
        description="Vui lòng kiểm tra kỹ tình trạng từng cuốn sách trước khi xác nhận trả." 
        type="info" 
        show-icon />
    </a-modal>
    
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import {
  SearchOutlined,
  ReloadOutlined
} from '@ant-design/icons-vue';
definePageMeta({
  layout: 'admin'
})

useHead({
  title: 'Quản lý đơn quá hạn - Admin'
})
import { format, addDays } from 'date-fns';
import { vi } from 'date-fns/locale';
import { message } from 'ant-design-vue';

// Data
const loading = ref(false)
const orders = ref([])
const extendModalVisible = ref(false)
const returnModalVisible = ref(false)
const extendDays = ref(null)
const confirmLoading = ref(false)
const selectedRental = ref(null)
const rentalService = useRentalOrders()
const bookConditions = ref({});
const statistics = ref({
  total: 0,
  processed: 0,
  totalFine: 0,
  todayOverdue: 0
})

const filters = reactive({
  search: '',
  status: undefined
})

const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0,
  showSizeChanger: true,
  showQuickJumper: true,
  showTotal: (total, range) => `${range[0]}-${range[1]} của ${total} mục`
})

const params = reactive({
  page: pagination.current,
  limit: pagination.pageSize,
  search: filters.search,
})

const detailModal = reactive({
  visible: false,
  data: null
})

const contactModal = reactive({
  visible: false,
  loading: false,
  data: null,
  form: {
    type: 'call',
    content: ''
  }
})

// Table columns
const columns = [
  {
    title: 'Mã đơn',
    dataIndex: 'id',
    key: 'id',
    width: 50,
    fixed: 'left'
  },
  {
    title: 'Khách hàng',
    key: 'user',
    width: 90
  },
  {
    title: 'Ngày hết hạn',
    key: 'due_date',
    width: 90,
  },
  {
    title: 'Số ngày quá hạn',
    key: 'overdueDays',
    width: 90,
    align: 'center'
  },
  // {
  //   title: 'Tiền phạt',
  //   dataIndex: 'fineAmount',
  //   key: 'fineAmount',
  //   width: 120,
  //   align: 'right'
  // },
  {
    title: 'Trạng thái',
    key: 'status',
    width: 90,
    align: 'center'
  },
  {
    title: 'Thao tác',
    key: 'actions',
    width: 90,
    fixed: 'right',
    align: 'center'
  }
]

const productColumns = [
  {
    title: 'Tên sách',
    key: 'book',
    width: 100
  },
  {
    title: 'Số lượng',
    key: 'quantity',
    width: 100,
    align: 'center'
  },
  {
    title: 'Đơn giá',
    key: 'price',
    width: 150
  }
]

// Methods
const fetchData = async (params = {}) => {
  try {
    loading.value = true
    const { getOverdueOrders } = useRentalOrders()

    const response = await getOverdueOrders(params)
    if (response.success) {
      orders.value = response.data || []
      if (response.pagination?.total !== undefined) {
        pagination.total = response.pagination.total;
      }
    }
    statistics.value = response.statistics || {
      total: 0,
      processed: 0,
      totalFine: 0,
      todayOverdue: 0
    }

  } catch (error) {
    console.error('Error fetching data:', error)
    message.error('Không thể tải dữ liệu')
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  params.search = filters.search
  params.page = pagination.current
  params.limit = pagination.pageSize
  fetchData(params)
}

const resetFilters = () => {
  filters.search = ''
  filters.status = undefined
  params.search = filters.search
  params.page = pagination.current
  params.limit = pagination.pageSize
  handleSearch()
}

const refreshData = () => {
  fetchData(params)
}

const handleTableChange = (pag) => {
  pagination.current = pag.current
  pagination.pageSize = pag.pageSize
  fetchData()
}

const viewOrderDetail = (record) => {
  detailModal.data = record
  detailModal.visible = true
}

const contactCustomer = (record) => {
  contactModal.data = record
  contactModal.form.type = 'call'
  contactModal.form.content = ''
  contactModal.visible = true
}

const showReturnModal = (record) => {
  selectedRental.value = record;
  returnModalVisible.value = true;
}

const cancelReturnModal = () => {
  returnModalVisible.value = false;
  selectedRental.value = null;
  bookConditions.value = {};
};

const sendReminder = async (record) => {
  try {
    await new Promise(resolve => setTimeout(resolve, 500))
    message.success('Đã gửi nhắc nhở đến khách hàng')
  } catch (error) {
    message.error('Có lỗi xảy ra')
  }
}

const cancelExtendModal = () => {
  extendModalVisible.value = false;
  extendDays.value = null;
  selectedRental.value = null;
};

const handleExtendRental = async () => {
  if (!extendDays.value) {
    return;
  }
  
  confirmLoading.value = true;
  try {
    const response = await rentalService.extendRental(selectedRental.value.id, extendDays.value);
    
    if (response.success) {
      cancelExtendModal();
      handleSearch();
    }
  } catch (error) {
    console.error('Error extending rental:', error);
  } finally {
    confirmLoading.value = false;
  }
};

const handleReturnBooks = async () => {
  const allBooksEvaluated = validateBookConditions();
  if (!allBooksEvaluated) {
    return;
  }
  
  confirmLoading.value = true;
  try {
    const returnData = {
      rental_id: selectedRental.value.id,
      book_conditions: getReturnData(),
      total_compensation: getTotalCompensation()
    };
    
    const response = await rentalService.returnRentalOrder(returnData);
    if (response.data.success) {
      message.success(response.data.message);
      cancelReturnModal();
      handleSearch();
    }
  } catch (error) {
    console.error('Error returning books:', error);
  } finally {
    confirmLoading.value = false;
  }
};

const getReturnData = () => {
  const returnData = [];
  
  Object.entries(bookConditions.value).forEach(([key, status]) => {
    const [bookIndex, detailId] = key.split('-');
    returnData.push({
      book_id: parseInt(bookIndex),
      status: status
    });
  });
  
  return returnData;
};


const getNewDueDate = () => {
  if (!selectedRental.value?.due_date || !extendDays.value) return '';
  const currentDueDate = new Date(selectedRental.value.due_date);
  const newDueDate = addDays(currentDueDate, extendDays.value);
  return format(newDueDate, 'dd/MM/yyyy HH:mm', { locale: vi });
};

const extendDeadline = (record) => {
  selectedRental.value = record;
  extendModalVisible.value = true;
}

const exportData = async () => {
  try {
    message.success('Đang xuất dữ liệu...')
  } catch (error) {
    message.error('Có lỗi khi xuất dữ liệu')
  }
}

// Utility functions
const getStatusColor = (status) => {
  const colors = {
    overdue: 'red',
    processing: 'orange',
    returned: 'green'
  }
  return colors[status] || 'default'
}

const getStatusText = (status) => {
  const texts = {
    overdue: 'Quá hạn',
    processing: 'Đang xử lý',
    returned: 'Đã trả'
  }
  return texts[status] || status
}

const getOverduedDays = (due_date) => {
  const today = new Date();
  const dueDate = new Date(due_date);
  const diffTime = dueDate.getTime() - today.getTime();
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
  return diffDays < 0 ? Math.abs(diffDays) : 0;
}

const getTotalCompensation = () => {
  if (!selectedRental.value?.rental_details) return 0;
  
  let total = 0;
  selectedRental.value.rental_details.forEach(detail => {
    for (let i = 1; i <= detail.quantity; i++) {
      const key = `${detail.id}-${i}`;
      if (bookConditions.value[key] === 'lost') {
        total += detail.book?.price * 2 || 0; // Phí bồi thường x2 giá sách
      }
    }
  });
  
  return total;
};

// Load data on mount
onMounted(() => {
  fetchData()
})</script>

<style scoped>
.overdue-orders-page {
  padding: 24px;
}

.filter-card {
  margin-bottom: 16px;
}

.stats-row {
  margin-bottom: 16px;
}

.customer-info {
  line-height: 1.4;
}

.text-gray {
  color: #666;
}

.fine-amount {
  color: #cf1322;
  font-weight: 600;
}

.order-detail {
  margin-top: 16px;
}

:deep(.ant-table-thead > tr > th) {
  background: #fafafa;
}

:deep(.ant-descriptions-item-label) {
  font-weight: 600;
}
</style>