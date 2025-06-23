<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Quản lý đơn cho thuê</h2>
      <a-button type="primary" @click="showAddModal" v-if="canCreateRental">
        <template #icon>
          <PlusOutlined />
        </template>
        Tạo đơn mới
      </a-button>
    </div>

    <a-card class="mb-4">
      <div class="flex flex-wrap gap-4">
        <a-input v-model:value="searchText" placeholder="Tìm kiếm theo tên người thuê" style="width: 250px"
          @press-enter="handleSearch">
          <template #prefix>
            <SearchOutlined />
          </template>
        </a-input>

        <a-select v-model:value="selectedStatus" placeholder="Lọc theo trạng thái" style="width: 200px"
          @change="handleSearch">
          <a-select-option value="">Tất cả trạng thái</a-select-option>
          <a-select-option value="renting">Đang thuê</a-select-option>
          <a-select-option value="overdue">Quá hạn</a-select-option>
          <a-select-option value="returned">Đã trả</a-select-option>
          <a-select-option value="cancel">Đã hủy</a-select-option>
        </a-select>

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

    <a-table :dataSource="rentals" :columns="columns" :pagination="pagination" :loading="loading"
      @change="handleTableChange" rowKey="id" :scroll="{ x: 1200 }">
      <template #bodyCell="{ column, record }">
        <!-- Tên người thuê -->
        <template v-if="column.key === 'user'">
          {{ record.user?.name || 'Không có thông tin' }}
        </template>

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

        <!-- Hành động -->
        <template v-if="column.key === 'actions'">
          <div class="flex gap-2 flex-wrap">
            <a-button type="primary" size="small" @click="showDetailsModal(record)" v-if="record.status !== 'completed'">
              <template #icon>
                <EyeOutlined />
              </template>
              Chi tiết
            </a-button>

            <a-button type="primary" size="small" @click="updateStatus(record.id, 'approved')"
              v-if="record.status === 'pending' && canManageRental(record)">
              <template #icon>
                <CheckOutlined />
              </template>
              Xác nhận
            </a-button>

            <!-- Dropdown cho các hành động trả sách/gia hạn -->
            <a-dropdown v-if="(record.status === 'renting' || record.status === 'overdue') ">
              <template #overlay>
                <a-menu @click="handleRentalAction($event, record)">
                  <a-menu-item key="return">
                    <template #icon>
                      <RollbackOutlined />
                    </template>
                    Trả sách
                  </a-menu-item>
                  <a-menu-item key="extend">
                    <template #icon>
                      <ClockCircleOutlined />
                    </template>
                    Gia hạn thuê
                  </a-menu-item>
                  <a-menu-item key="cancel">
                    <template #icon>
                      <CloseOutlined />
                    </template>
                    Hủy đơn
                  </a-menu-item>
                </a-menu>
              </template>
              <a-button type="primary" size="small">
                <template #icon>
                  <SettingOutlined />
                </template>
                Hành động
                <DownOutlined />
              </a-button>
            </a-dropdown>

            
              <!-- <a-button type="primary" danger size="small" 
                :disabled="record.status === 'returned' || record.status === 'cancelled'">
                <template #icon>
                  <CloseOutlined />
                </template>
                Hủy bỏ
              </a-button>
            </a-popconfirm> -->
            <a-popconfirm title="Bạn có chắc muốn xóa đơn cho thuê này?" ok-text="Có" cancel-text="Không"
              @confirm="handleDelete(record.id)" 
              :disabled="record.status === 'returned' || !canDeleteRental() || record.status === 'cancelled'">
              <a-button type="primary" danger size="small" :disabled="record.status === 'returned' || !canDeleteRental() || record.status === 'cancelled'">
                <template #icon>
                  <DeleteOutlined />
                </template>
                Xóa
              </a-button>
            </a-popconfirm>
          </div>
        </template>
      </template>
    </a-table>

    <!-- Modal tạo đơn cho thuê -->
    <a-modal 
      title="Tạo đơn cho thuê" 
      :open="addModalVisible" 
      @cancel="cancelAddModal"
      :confirmLoading="confirmLoading"
      @ok="handleAdd"
      width="800px" 
      :maskClosable="true">
      
    </a-modal>

    <!-- Modal Chi tiết đơn cho thuê -->
    <a-modal 
      :title="'Chi tiết đơn cho thuê #' + selectedRental?.id" 
      :open="detailsModalVisible" 
      @cancel="cancelDetailsModal"
      :confirmLoading="confirmLoading"
      @ok="cancelDetailsModal"
      width="800px" 
      :maskClosable="true">
      <div class="mb-4">
        <h3 class="font-bold mb-2">Thông tin người thuê</h3>
        <div class="flex flex-col gap-2">
          <div><strong>Tên:</strong> {{ selectedRental?.user?.name }}</div>
          <div><strong>Email:</strong> {{ selectedRental?.user?.email }}</div>
          <div><strong>Số điện thoại:</strong> {{ selectedRental?.user?.phone }}</div>
        </div>
      </div>

      <div class="mb-4">
        <h3 class="font-bold mb-2">Thông tin đơn thuê</h3>
        <div class="flex flex-col gap-2">
          <div><strong>Ngày thuê:</strong> {{ formatDate(selectedRental?.rental_date) }}</div>
          <div><strong>Ngày trả dự kiến:</strong> {{ formatDate(selectedRental?.due_date) }}</div>
          <div><strong>Tổng tiền:</strong> {{ getTotalPrice(selectedRental?.rental_details) }} </div>
          <div><strong>Trạng thái:</strong>
            <a-tag :color="getStatusColor(selectedRental?.status)">
              {{ getStatusText(selectedRental?.status) }}
            </a-tag>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <h3 class="font-bold mb-2">Danh sách sách</h3>
        <a-table 
          :dataSource="selectedRental?.rental_details" 
          :columns="detailsColumns" 
          :pagination="false"
          rowKey="id" :scroll="{ x: 800 }">
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

    <!-- Modal Trả sách với Preview Chi phí -->
    <AdminRentalReturnModal
      :visible="returnModalVisible"
      :selectedRental="selectedRental"
      :rentalService="rentalService"
      @cancel="handleCancelReturn"
      @success="handleReturnSuccess"
    />


  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { message, Modal } from 'ant-design-vue';
import {
  PlusOutlined,
  SearchOutlined,
  ReloadOutlined,
  EyeOutlined,
  CheckOutlined,
  CloseOutlined,
  DeleteOutlined,
  SettingOutlined,
  DownOutlined,
  RollbackOutlined,
  ClockCircleOutlined
} from '@ant-design/icons-vue';
import { format, addDays } from 'date-fns';
import { vi } from 'date-fns/locale';

const rentalService = useRentalOrders();

const authStore = useAuthStore();
const currentUser = authStore.user;
const currentRole = authStore.user.role;

const ROLE_HIERARCHY = {
  admin: 3,
  staff: 2,
  customer: 1
};

const currentUserRoleLevel = computed(() => {
  return ROLE_HIERARCHY[currentRole.value] || 0;
});

const canCreateRental = computed(() => {
  return currentUserRoleLevel.value >= ROLE_HIERARCHY.staff;
});

const canManageRental = (rental) => {
  const currentLevel = currentUserRoleLevel.value;
  
  if (currentLevel >= ROLE_HIERARCHY.admin) {
    return true;
  }
  
  if (currentLevel >= ROLE_HIERARCHY.staff) {
    return rental.user?.role === 'customer';
  }
  
  return false;
};

const canDeleteRental = () => {
  const currentLevel = currentUserRoleLevel.value;
  
  if (currentLevel >= ROLE_HIERARCHY.admin) {
    return true;
  }
  
  return false;
};

// Data
const rentals = ref([]);
const loading = ref(false);
const searchText = ref('');
const selectedStatus = ref('');
const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0
});
const selectedRental = ref(null);
const addModalVisible = ref(false);
const detailsModalVisible = ref(false);
const extendModalVisible = ref(false);
const returnModalVisible = ref(false);
const confirmLoading = ref(false);
const previewLoading = ref(false);
const extendDays = ref(null);
const bookConditions = ref({});
const returnStep = ref(1);
const previewData = ref(null);

// Columns
const columns = [
  {
    title: 'Mã đơn',
    dataIndex: 'id',
    key: 'id',
    width: 100
  },
  {
    title: 'Người thuê',
    key: 'user',
    width: 200
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

const detailsColumns = [
  {
    title: 'Tên sách',
    key: 'book',
    width: 300
  },
  {
    title: 'Giá cọc',
    key: 'price',
    width: 150
  },
  {
    title: 'Số lượng',
    key: 'quantity',
    width: 100
  }
];

const fetchRentals = async (params = {}) => {
  try {
    loading.value = true;
    
    if (currentUserRoleLevel.value < ROLE_HIERARCHY.admin) {
      if (currentUserRoleLevel.value >= ROLE_HIERARCHY.staff) {
        params.user_roles = ['customer', 'staff'];
      } else {
        params.user_id = currentUser.value?.id;
      }
    }
    
    const response = await rentalService.getRentals(params);
    
    if (response.success) {
      rentals.value = response.data;
      if (response.pagination?.total !== undefined) {
        pagination.total = response.pagination.total;
      }
    } else {
      console.error('Failed to fetch rentals:', response.message);
      rentals.value = [];
    }
  } catch (error) {
    console.error('Error fetching rentals:', error);
    rentals.value = [];
  } finally {
    loading.value = false;
  }
};

const getTotalPrice = (rental_details) => {
  let totalPrice = 0;
  if (!rental_details) return 0;
  rental_details.forEach((detail) => {
    totalPrice += detail.book.price * detail.quantity;
  });
  return formatPrice(totalPrice);
};

// Status helpers
const getStatusColor = (status) => {
  const colors = {
    pending: 'orange',
    approved: 'blue',
    renting: 'yellow',
    overdue: 'red',
    returned: 'green',
    cancel: 'gray',
    completed: 'green'
  };
  return colors[status] || 'default';
};

const getStatusText = (status) => {
  const texts = {
    pending: 'Chờ xác nhận',
    approved: 'Đã xác nhận',
    renting: 'Đang thuê',
    overdue: 'Quá hạn',
    returned: 'Đã trả',
    cancel: 'Đã hủy',
    completed: 'Hoàn thành'
  };
  return texts[status] || 'Không xác định';
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

const getNewDueDate = () => {
  if (!selectedRental.value?.due_date || !extendDays.value) return '';
  const currentDueDate = new Date(selectedRental.value.due_date);
  const newDueDate = addDays(currentDueDate, extendDays.value);
  return format(newDueDate, 'dd/MM/yyyy HH:mm', { locale: vi });
};

// Modal handlers
const showAddModal = () => {
  addModalVisible.value = true;
};

const cancelAddModal = () => {
  addModalVisible.value = false;
};

const handleAdd = async () => {
  try {
    confirmLoading.value = true;
    const data = {
      "user_id": 1,
      "rental_date": "2025-03-20",
      "due_date": "2025-03-22",
      "deposit": 100000,
      "books": [
          {
              "book_id": 3,
              "quantity": 2
          }
      ],
      "paid": 0,
    };
    
    const response = await rentalService.createRentalOrder(data);
    
    if (response.success) {
      message.success('Tạo đơn thuê thành công!');
      addModalVisible.value = false;
      handleSearch();
    } else {
      message.error(response.message || 'Tạo đơn thuê thất bại!');
    }
  } catch (error) {
    console.error('Error creating rental:', error);
    message.error('Có lỗi xảy ra khi tạo đơn thuê!');
  } finally {
    confirmLoading.value = false;
  }
};

const showDetailsModal = (record) => {
  selectedRental.value = record;
  detailsModalVisible.value = true;
};

const cancelDetailsModal = () => {
  detailsModalVisible.value = false;
  selectedRental.value = null;
  confirmLoading.value = false;
};

const cancelExtendModal = () => {
  extendModalVisible.value = false;
  extendDays.value = null;
  selectedRental.value = null;
};

const cancelReturnModal = () => {
  returnModalVisible.value = false;
  selectedRental.value = null;
  bookConditions.value = {};
  returnStep.value = 1;
  previewData.value = null;
};

// Rental action handlers
const handleRentalAction = (menuInfo, record) => {
  selectedRental.value = record;
  
  if (menuInfo.key === 'return') {
    returnModalVisible.value = true;
    returnStep.value = 1;
    initializeBookConditions(record);
  } else if (menuInfo.key === 'extend') {
    extendModalVisible.value = true;
  } else if (menuInfo.key === 'cancel') {
    showCancelConfirmation(record);
    // cancelModalVisible.value = true;
  }
};

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

const handleExtendRental = async () => {
  if (!extendDays.value) {
    message.error('Vui lòng chọn thời gian gia hạn!');
    return;
  }
  
  confirmLoading.value = true;
  try {
    const response = await rentalService.extendRental(selectedRental.value.id, extendDays.value);
    
    if (response.success) {
      message.success('Gia hạn thuê thành công!');
      cancelExtendModal();
      handleSearch();
    } else {
      message.error(response.message || 'Gia hạn thuê thất bại!');
    }
  } catch (error) {
    console.error('Error extending rental:', error);
    message.error('Có lỗi xảy ra khi gia hạn thuê!');
  } finally {
    confirmLoading.value = false;
  }
};

const cancelOrder = async (orderId) => {
  try {
    const response = await rentalService.cancelRental(orderId);
    return response;
  } catch (error) {
    // console.error('Error canceling order:', error);
    throw Error('Có lỗi xảy ra khi hủy đơn thuê!');
  }
};

const showCancelConfirmation = (record) => {
    Modal.confirm({
      title: 'Xác nhận hủy đơn',
      content: 'Bạn có chắc muốn hủy đơn cho thuê này?',
      okText: 'Có',
      cancelText: 'Không',
      onOk: () => confirmCancelOrder(record),
      onCancel: () => cancelCancelModal(),
    });
}

const confirmCancelOrder = async (record) => {
  if (!record) return;

  confirmLoading.value = true;
  try {
    await cancelOrder(record.id);

    const index = rentals.value.findIndex(rental => rental.id === record.id);
    if (index !== -1) {
      rentals.value[index].status = 'cancel';
    }

    message.success('Hủy đơn thuê thành công!');
    cancelCancelModal();

    handleSearch();
  } catch (error) {
    message.error(error.message);
  } finally {
    confirmLoading.value = false;
  }
};

const cancelCancelModal = () => {
  // Modal.destroy();
  selectedRental.value = null;
  confirmLoading.value = false;
};

const handleCancelReturn = () => {
  returnModalVisible.value = false;
  returnStep.value = 1;
  previewData.value = null;
  bookConditions.value = {};
};

const handleReturnSuccess = () => {
  returnModalVisible.value = false;
  returnStep.value = 1;
  previewData.value = null;
  bookConditions.value = {};
  handleSearch();
};

// Search and filter handlers
const handleSearch = () => {
  const params = {
    page: pagination.current,
    limit: pagination.pageSize,
    search: searchText.value,
    status: selectedStatus.value
  };
  fetchRentals(params);
};

const resetFilters = () => {
  searchText.value = '';
  selectedStatus.value = '';
  pagination.current = 1;
  handleSearch();
};

// Table handlers
const handleTableChange = (pag) => {
  pagination.current = pag.current;
  pagination.pageSize = pag.pageSize;
  handleSearch();
};

// Status update
const updateStatus = async (id, status) => {
  try {
    confirmLoading.value = true;
    const response = await rentalService.updateRentalStatus(id, status);
    
    if (response.success) {
      message.success('Cập nhật trạng thái thành công!');
      handleSearch();
    } else {
      message.error(response.message || 'Cập nhật trạng thái thất bại!');
    }
  } catch (error) {
    console.error('Error updating status:', error);
    message.error('Có lỗi xảy ra khi cập nhật trạng thái!');
  } finally {
    confirmLoading.value = false;
  }
};

// Delete handler
const handleDelete = async (id) => {
  try {
    confirmLoading.value = true;
    const response = await rentalService.deleteRentalOrder(id);
    
    if (response.success) {
      message.success('Xóa đơn thuê thành công!');
      handleSearch();
    } else {
      message.error(response.message || 'Xóa đơn thuê thất bại!');
    }
  } catch (error) {
    console.error('Error deleting rental:', error);
    message.error('Có lỗi xảy ra khi xóa đơn thuê!');
  } finally {
    confirmLoading.value = false;
  }
};

// Book condition helpers
const updateBookCondition = (bookId, bookIndex, condition) => {
  const key = `${bookId}-${bookIndex}`;
  bookConditions.value[key] = condition;
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

const validateBookConditions = () => {
  if (!selectedRental.value?.rental_details) return true;
  
  for (const detail of selectedRental.value.rental_details) {
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

// Setup initial data
onMounted(() => {
  handleSearch();
});

// Meta
definePageMeta({
  layout: 'admin'
});
</script>