<!-- pages/admin/users-groups.vue -->
<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Quản lý người dùng</h2>
      <a-button 
        type="primary" 
        @click="showAddModal"
        :disabled="!canCreateUser"
      >
        <template #icon><PlusOutlined /></template>
        Thêm người dùng mới
      </a-button>
    </div>

    <a-card class="mb-4">
      <div class="flex gap-4 flex-wrap">
        <a-input
          v-model:value="searchText"
          placeholder="Tìm kiếm theo tên, email, số điện thoại"
          style="width: 300px"
          @press-enter="handleSearch"
        >
          <template #prefix>
            <SearchOutlined />
          </template>
        </a-input>

        <a-select
          v-model:value="filterRole"
          placeholder="Vai trò"
          style="width: 150px"
          allowClear
        >
          <a-select-option 
            v-for="role in availableFilterRoles" 
            :key="role.value" 
            :value="role.value"
          >
            {{ role.label }}
          </a-select-option>
        </a-select>

        <div class="flex gap-2">
          <a-button type="primary" @click="handleSearch">
            <template #icon><SearchOutlined /></template>
            Tìm kiếm
          </a-button>
          <a-button @click="resetFilters">
            <template #icon><ReloadOutlined /></template>
            Đặt lại
          </a-button>
        </div>
      </div>
    </a-card>

    <a-table
      :dataSource="users"
      :columns="columns"
      :pagination="pagination"
      :loading="loading"
      @change="handleTableChange"
      rowKey="id"
    >
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'name'">
          <div class="flex items-center">
            <a-avatar v-if="record.avatar" :src="record.avatar" class="mr-2" />
            <a-avatar v-else class="mr-2">{{ getInitials(record.name) }}</a-avatar>
            {{ record.name }}
          </div>
        </template>
        
        <template v-if="column.key === 'role'">
          <a-tag :color="getRoleColor(record.role)">
            <template #icon>
              <component :is="getRoleIcon(record.role)" />
            </template>
            {{ getRoleLabel(record.role) }}
          </a-tag>
        </template>
        
        <template v-if="column.key === 'status'">
          <a-tag :color="record.status === 'active' ? 'green' : 'red'">
            {{ record.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
          </a-tag>
        </template>
        
        <template v-if="column.key === 'actions'">
          <div class="flex gap-2">
            <a-button 
              type="primary" 
              size="small" 
              @click="showEditModal(record)"
              :disabled="!canEditUser(record)"
            >
              <template #icon><EditOutlined /></template>
              Sửa
            </a-button>
            
            <a-popconfirm
              title="Bạn có chắc muốn xóa người dùng này?"
              ok-text="Có"
              cancel-text="Không"
              @confirm="handleDelete(record.id)"
              :disabled="!canDeleteUser(record)"
            >
              <a-button 
                type="primary" 
                danger 
                size="small"
                :disabled="!canDeleteUser(record)"
              >
                <template #icon><DeleteOutlined /></template>
                Xóa
              </a-button>
            </a-popconfirm>
          </div>
        </template>
      </template>
    </a-table>

    <!-- Modal Thêm/Sửa Người dùng -->
    <a-modal
      :title="editingUser ? 'Sửa thông tin người dùng' : 'Thêm người dùng mới'"
      :open="modalVisible"
      @cancel="cancelModal"
      :confirmLoading="confirmLoading"
      @ok="handleModalOk"
      width="700px"
    >
      <a-form 
      ref="formRef"
      :model="userForm" 
      :rules="formRules"
      layout="vertical">
        <div class="grid grid-cols-2 gap-4">
          <a-form-item label="Họ và tên" name="name">
            <a-input v-model:value="userForm.name" />
          </a-form-item>

          <a-form-item label="Email" name="email">
            <a-input v-model:value="userForm.email" />
          </a-form-item>

          <a-form-item label="Số điện thoại" name="phone">
            <a-input v-model:value="userForm.phone" />
          </a-form-item>

          <a-form-item label="Vai trò" name="role">
            <a-select v-model:value="userForm.role">
              <a-select-option 
                v-for="role in availableRoles" 
                :key="role.value" 
                :value="role.value"
              >
                {{ role.label }}
              </a-select-option>
            </a-select>
          </a-form-item>

          <a-form-item label="Mật khẩu" name="password">
            <a-input-password v-model:value="userForm.password" placeholder="Để trống nếu không thay đổi" />
          </a-form-item>
        </div>

        <a-form-item label="Địa chỉ" name="address">
          <a-textarea v-model:value="userForm.address" :rows="2" />
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import {
  PlusOutlined,
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
  ReloadOutlined,
  CheckOutlined,
  StopOutlined,
  CrownOutlined,
  UserOutlined,
  TeamOutlined
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

// User store hoặc authentication composable
const authStore = useAuthStore();
const currentUser = computed(() => authStore.user); 
const currentRole = computed(() => authStore.user.role);
const usersApi = useUsers();

// Định nghĩa hierarchy của vai trò
const ROLE_HIERARCHY = {
  admin: 3,
  staff: 2,
  customer: 1
};

const ROLE_CONFIG = {
  admin: {
    label: 'Quản trị viên',
    color: 'red',
    icon: 'CrownOutlined'
  },
  staff: {
    label: 'Nhân viên',
    color: 'blue', 
    icon: 'TeamOutlined'
  },
  customer: {
    label: 'Khách hàng',
    color: 'green',
    icon: 'UserOutlined'
  }
};

// Computed properties cho quyền hạn
const currentUserRoleLevel = computed(() => {
  return ROLE_HIERARCHY[currentRole.value] || 0;
});

const canCreateUser = computed(() => {
  return currentUserRoleLevel.value >= ROLE_HIERARCHY.staff;
});

const availableRoles = computed(() => {
  const roles = [];
  const currentLevel = currentUserRoleLevel.value;
  
  // Admin có thể tạo tất cả vai trò
  if (currentLevel >= ROLE_HIERARCHY.admin) {
    roles.push(
      { value: 'admin', label: 'Quản trị viên' },
      { value: 'staff', label: 'Nhân viên' },
      { value: 'customer', label: 'Khách hàng' }
    );
  }
  // Staff chỉ có thể tạo customer
  else if (currentLevel >= ROLE_HIERARCHY.staff) {
    roles.push({ value: 'customer', label: 'Khách hàng' });
  }
  
  return roles;
});

const availableFilterRoles = computed(() => {
  const roles = [];
  const currentLevel = currentUserRoleLevel.value;
  
  // Admin có thể xem tất cả
  if (currentLevel >= ROLE_HIERARCHY.admin) {
    roles.push(
      { value: 'admin', label: 'Quản trị viên' },
      { value: 'staff', label: 'Nhân viên' },
      { value: 'customer', label: 'Khách hàng' }
    );
  }
  // Staff có thể xem staff và customer
  else if (currentLevel >= ROLE_HIERARCHY.staff) {
    roles.push(
      { value: 'staff', label: 'Nhân viên' },
      { value: 'customer', label: 'Khách hàng' }
    );
  }
  
  return roles;
});

// Kiểm tra quyền chỉnh sửa user
const canEditUser = (user) => {
  const currentLevel = currentUserRoleLevel.value;
  const targetLevel = ROLE_HIERARCHY[user.role];
  
  // Không thể chỉnh sửa chính mình (tránh lock-out)
  if (user.id === currentUser.value?.id) return false;
  
  // Chỉ có thể chỉnh sửa user có level thấp hơn
  return currentLevel > targetLevel;
};

// Kiểm tra quyền toggle status
const canToggleUserStatus = (user) => {
  const currentLevel = currentUserRoleLevel.value;
  const targetLevel = ROLE_HIERARCHY[user.role];
  
  // Không thể toggle chính mình
  if (user.id === currentUser.value?.id) return false;
  
  return currentLevel > targetLevel;
};

// Kiểm tra quyền xóa user
const canDeleteUser = (user) => {
  const currentLevel = currentUserRoleLevel.value;
  const targetLevel = ROLE_HIERARCHY[user.role];
  
  // Không thể xóa chính mình
  if (user.id === currentUser.value?.id) return false;
  
  // Chỉ admin mới có thể xóa
  return currentLevel >= ROLE_HIERARCHY.admin && currentLevel > targetLevel;
};

// Helper functions cho UI
const getRoleLabel = (role) => ROLE_CONFIG[role]?.label || role;
const getRoleColor = (role) => ROLE_CONFIG[role]?.color || 'default';
const getRoleIcon = (role) => {
  const iconName = ROLE_CONFIG[role]?.icon;
  const icons = { CrownOutlined, TeamOutlined, UserOutlined };
  return icons[iconName] || UserOutlined;
};

// Định nghĩa cột cho bảng
const columns = [
  {
    title: 'ID',
    dataIndex: 'id',
    key: 'id',
    width: 80
  },
  {
    title: 'Họ và tên',
    dataIndex: 'name',
    key: 'name',
    sorter: (a, b) => a.name.localeCompare(b.name),
  },
  {
    title: 'Email',
    dataIndex: 'email',
    key: 'email',
  },
  {
    title: 'Số điện thoại',
    dataIndex: 'phone',
    key: 'phone',
  },
  {
    title: 'Vai trò',
    dataIndex: 'role',
    key: 'role',
    filters: availableFilterRoles.value.map(role => ({
      text: role.label,
      value: role.value
    })),
  },
  // {
  //   title: 'Trạng thái',
  //   dataIndex: 'status',
  //   key: 'status',
  //   filters: [
  //     { text: 'Hoạt động', value: 'active' },
  //     { text: 'Không hoạt động', value: 'inactive' },
  //   ],
  // },
  {
    title: 'Thao tác',
    key: 'actions',
    width: 280
  }
];

// State
const loading = ref(false);
const searchText = ref('');
const filterRole = ref(undefined);
const filterStatus = ref(undefined);
const modalVisible = ref(false);
const confirmLoading = ref(false);
const editingUser = ref(null);
const users = ref([]);
const sortField = ref('');
const sortOrder = ref('');

// Pagination
const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0,
  showSizeChanger: true,
  pageSizeOptions: ['10', '20', '50', '100'],
  showTotal: (total) => `Tổng ${total} mục`
});

// Form ref để truy cập validation
const formRef = ref()

// Form
const userForm = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  role: 'customer',
  status: 'active',
  address: '',
});

// Mẫu dữ liệu với hierarchy
const mockUsers = [
  {
    id: 1,
    name: 'Super Admin',
    email: 'admin@example.com',
    phone: '0901234567',
    role: 'admin',
    // status: 'active',
    address: 'Hà Nội'
  },
  {
    id: 2,
    name: 'Nguyễn Văn Staff',
    email: 'staff1@example.com',
    phone: '0912345678',
    role: 'staff',
    // status: 'active',
    address: 'Hồ Chí Minh'
  },
  {
    id: 3,
    name: 'Trần Thị Customer',
    email: 'customer1@example.com',
    phone: '0923456789',
    role: 'customer',
    // status: 'active',
    address: 'Đà Nẵng'
  },
  {
    id: 4,
    name: 'Lê Văn Customer 2',
    email: 'customer2@example.com',
    phone: '0934567890',
    role: 'customer',
    // status: 'inactive',
    address: 'Cần Thơ'
  }
];

// Form rules
const formRules = computed(() => ({
  name: [
    { required: true, message: 'Vui lòng nhập họ và tên', trigger: 'blur' }
  ],
  email: [
    { required: true, message: 'Vui lòng nhập email', trigger: 'blur' },
    { type: 'email', message: 'Email không hợp lệ', trigger: 'blur' }
  ],
  role: [
    { required: true, message: 'Vui lòng chọn vai trò', trigger: 'change' }
  ],
  password: [
    { 
      required: !editingUser.value, 
      message: 'Vui lòng nhập mật khẩu',
      trigger: 'blur' 
    },
    {
      pattern: /^[a-zA-Z\d@$!%*?&]{6,}$/,
      message: 'Mật khẩu phải có ít nhất 6 ký tự',
      trigger: 'blur'
    }
  ]
}));

// Lấy chữ cái đầu của tên
function getInitials(name) {
  if (!name) return '';
  return name.split(' ').map(word => word[0]).join('').toUpperCase();
}

// Fetch data with role-based filtering
async function fetchUsers() {
  loading.value = true;

  try {
    const params = new URLSearchParams();
    params.append('page', pagination.current);
    params.append('pageSize', pagination.pageSize);
    
    if (searchText.value) {
      params.append('search', searchText.value);
    }

    if (filterRole.value) {
      params.append('role', filterRole.value);
    }
    
    if (filterStatus.value) {
      params.append('status', filterStatus.value);
    }
    
    if (sortField.value && sortOrder.value) {
      params.append('sortField', sortField.value);
      params.append('sortOrder', sortOrder.value);
    }

    const response = await usersApi.getUsers(params);
    users.value = response.data;

    // Mock response với role-based filtering
    setTimeout(() => {
      // let filteredData = [...mockUsers];
      let filteredData = [...users.value];

      
      // Lọc theo quyền của user hiện tại
      const currentLevel = currentUserRoleLevel.value;
      filteredData = filteredData.filter(user => {
        const userLevel = ROLE_HIERARCHY[user.role];
        // Chỉ hiển thị user có level thấp hơn hoặc bằng (trừ admin có thể xem tất cả)
        return currentLevel >= ROLE_HIERARCHY.admin || currentLevel >= userLevel;
      });
      
      // Lọc theo từ khóa tìm kiếm
      if (searchText.value) {
        const searchLower = searchText.value.toLowerCase();
        filteredData = filteredData.filter(user => 
          user.name.toLowerCase().includes(searchLower) || 
          user.email.toLowerCase().includes(searchLower) || 
          (user.phone && user.phone.includes(searchText.value))
        );
      }
      
      // Lọc theo vai trò
      if (filterRole.value) {
        filteredData = filteredData.filter(user => user.role === filterRole.value);
      }
      
      // Lọc theo trạng thái
      if (filterStatus.value) {
        filteredData = filteredData.filter(user => user.status === filterStatus.value);
      }
      
      users.value = filteredData;
      pagination.total = filteredData.length;
      
      loading.value = false;
    }, 500);

  } catch (error) {
    console.error('Lỗi khi lấy danh sách người dùng:', error);
    message.error('Có lỗi xảy ra khi tải danh sách người dùng');
    loading.value = false;
  }
}

// Fetch data
onMounted(() => {
  fetchUsers();
});

// Xử lý tìm kiếm
function handleSearch() {
  pagination.current = 1;
  fetchUsers();
}

// Đặt lại bộ lọc
function resetFilters() {
  searchText.value = '';
  filterRole.value = undefined;
  filterStatus.value = undefined;
  pagination.current = 1;
  fetchUsers();
}

// Xử lý thay đổi bảng (phân trang, sắp xếp)
function handleTableChange(pag, filters, sorter) {
  pagination.current = pag.current;
  pagination.pageSize = pag.pageSize;
  
  // Xử lý sắp xếp
  if (sorter.field && sorter.order) {
    sortField.value = sorter.field;
    sortOrder.value = sorter.order === 'ascend' ? 'asc' : 'desc';
  } else {
    sortField.value = '';
    sortOrder.value = '';
  }
  
  // Xử lý lọc
  if (filters.role && filters.role.length > 0) {
    filterRole.value = filters.role[0];
  }
  
  if (filters.status && filters.status.length > 0) {
    filterStatus.value = filters.status[0];
  }
  
  fetchUsers();
}

// Hiển thị modal thêm người dùng
function showAddModal() {
  if (!canCreateUser.value) {
    message.warning('Bạn không có quyền thêm người dùng mới');
    return;
  }
  
  editingUser.value = null;
  
  // Reset form với role mặc định theo quyền
  const defaultRole = availableRoles.value.length > 0 ? availableRoles.value[availableRoles.value.length - 1].value : 'customer';
  
  Object.assign(userForm, {
    name: '',
    email: '',
    phone: '',
    password: '',
    role: defaultRole,
    status: 'active',
    address: '',
  });
  
  modalVisible.value = true;
}

// Hiển thị modal sửa người dùng
function showEditModal(record) {
  if (!canEditUser(record)) {
    message.warning('Bạn không có quyền chỉnh sửa người dùng này');
    return;
  }
  
  editingUser.value = record;
  
  // Điền form
  Object.assign(userForm, {
    name: record.name,
    email: record.email,
    phone: record.phone || '',
    password: '', // Không hiển thị mật khẩu cũ
    role: record.role,
    status: record.status,
    address: record.address || '',
  });
  
  modalVisible.value = true;
}

// Xử lý hủy modal
function cancelModal() {
  modalVisible.value = false;
}

// Xử lý submit modal với validation quyền
async function handleModalOk() {
  // Validate form trước khi submit
  await formRef.value.validateFields()

  // Kiểm tra quyền trước khi submit
  if (editingUser.value && !canEditUser(editingUser.value)) {
    message.error('Bạn không có quyền chỉnh sửa người dùng này');
    return;
  }
  
  if (!editingUser.value && !canCreateUser.value) {
    message.error('Bạn không có quyền tạo người dùng mới');
    return;
  }
  
  // Kiểm tra vai trò được chọn có hợp lệ không
  const selectedRoleValid = availableRoles.value.some(role => role.value === userForm.role);
  if (!selectedRoleValid) {
    message.error('Vai trò được chọn không hợp lệ');
    return;
  }

  confirmLoading.value = true;
  try {
    const payload = {
      name: userForm.name,
      email: userForm.email,
      phone: userForm.phone,
      role: userForm.role,
      // status: userForm.status,
      // address: userForm.address,
    };
    
    // Chỉ gửi password nếu có nhập mới
    if (userForm.password) {
      payload.password = userForm.password;
      payload.password_confirmation = userForm.password;
    }
    
    let response;
    
    if (editingUser.value) {
      // Cập nhật người dùng
      await usersApi.updateUser(editingUser.value.id, payload);
      message.success('Cập nhật thông tin người dùng thành công!');
    } else {
      // Thêm người dùng mới
      await usersApi.createUser(payload);
      message.success('Thêm người dùng mới thành công!');
    }
    
    modalVisible.value = false;
    fetchUsers();
  } catch (error) {
    console.error('Lỗi khi lưu thông tin người dùng:', error);
    message.error('Có lỗi xảy ra khi lưu thông tin người dùng');
  } finally {
    confirmLoading.value = false;
  }
}

// Xử lý đổi trạng thái người dùng
async function toggleUserStatus(record) {
  if (!canToggleUserStatus(record)) {
    message.warning('Bạn không có quyền thay đổi trạng thái người dùng này');
    return;
  }
  
  loading.value = true;
  
  try {
    const newStatus = record.status === 'active' ? 'inactive' : 'active';
    // await axios.patch(`${apiBaseUrl}/users/${record.id}/status`, { status: newStatus });
    
    
    setTimeout(() => {
      const user = users.value.find(u => u.id === record.id);
      if (user) {
        user.status = newStatus;
      }
      message.success(`Đã ${newStatus === 'active' ? 'kích hoạt' : 'vô hiệu hóa'} người dùng thành công!`);
      loading.value = false;
    }, 500);
  } catch (error) {
    console.error('Lỗi khi thay đổi trạng thái người dùng:', error);
    message.error('Có lỗi xảy ra khi thay đổi trạng thái người dùng');
    loading.value = false;
  }
}

// Xử lý xóa người dùng
async function handleDelete(id) {
  const userToDelete = users.value.find(u => u.id === id);
  if (!canDeleteUser(userToDelete)) {
    message.warning('Bạn không có quyền xóa người dùng này');
    return;
  }
  
  loading.value = true;
  
  await usersApi.deleteUser(id);
  
  setTimeout(() => {
    users.value = users.value.filter(user => user.id !== id);
    message.success('Xóa người dùng thành công!');
    loading.value = false;
  }, 500);
}

definePageMeta({
  layout: 'admin',
  // middleware: ['auth', 'role-admin-staff'] // Chỉ admin và staff mới truy cập được
});
</script>