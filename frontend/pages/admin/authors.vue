<!-- pages/admin/authors.vue -->
<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Quản lý tác giả</h2>
      <a-button type="primary" @click="showAddModal">
        <template #icon><PlusOutlined /></template>
        Thêm tác giả mới
      </a-button>
    </div>

    <a-card class="mb-4">
      <div class="flex gap-4">
        <a-input
          v-model:value="searchText"
          placeholder="Tìm kiếm theo tên tác giả"
          style="width: 300px"
          @press-enter="handleSearch"
        >
          <template #prefix>
            <SearchOutlined />
          </template>
        </a-input>
        
        <a-button type="primary" @click="handleSearch">
          <template #icon><SearchOutlined /></template>
          Tìm kiếm
        </a-button>
        <a-button @click="resetFilters">
          <template #icon><ReloadOutlined /></template>
          Đặt lại
        </a-button>
      </div>
    </a-card>

    <a-table
      :dataSource="authors"
      :columns="columns"
      :pagination="pagination"
      :loading="loading"
      @change="handleTableChange"
      rowKey="id"
    >
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'actions'">
          <div class="flex gap-2">
            <a-button type="primary" size="small" @click="showEditModal(record)">
              <template #icon><EditOutlined /></template>
              Sửa
            </a-button>
            <a-popconfirm
              title="Bạn có chắc muốn xóa tác giả này?"
              ok-text="Có"
              cancel-text="Không"
              @confirm="handleDelete(record.id)"
            >
              <a-button type="primary" danger size="small">
                <template #icon><DeleteOutlined /></template>
                Xóa
              </a-button>
            </a-popconfirm>
          </div>
        </template>
      </template>
    </a-table>

    <!-- Modal Thêm/Sửa Tác giả -->
    <a-modal
      :title="editingAuthor ? 'Sửa thông tin tác giả' : 'Thêm tác giả mới'"
      :visible="modalVisible"
      @cancel="cancelModal"
      :confirmLoading="confirmLoading"
      @ok="handleModalOk"
      width="700px"
    >
      <a-form :model="authorForm" layout="vertical">
        <a-form-item label="Tên tác giả" name="name" :rules="[{ required: true }]">
          <a-input v-model:value="authorForm.name" />
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
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

// Sử dụng composable
const authorsApi = useAuthors();

// Định nghĩa cột cho bảng
const columns = [
  {
    title: 'ID',
    dataIndex: 'id',
    key: 'id',
    width: 80
  },
  {
    title: 'Tên tác giả',
    dataIndex: 'name',
    key: 'name',
    sorter: true,
  },
  {
    title: 'Số lượng sách',
    dataIndex: 'bookCount',
    key: 'bookCount',
    sorter: true,
  },
  {
    title: 'Thao tác',
    key: 'actions',
    width: 150
  }
];

// State
const loading = ref(false);
const searchText = ref('');
const modalVisible = ref(false);
const confirmLoading = ref(false);
const editingAuthor = ref(null);
const authors = ref([]);

// Pagination
const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0,
  showSizeChanger: true,
  pageSizeOptions: ['10', '20', '50', '100'],
  showTotal: (total) => `Tổng ${total} mục`
});

// Form
const authorForm = reactive({
  name: '',
});

// Fetch data
onMounted(() => {
  fetchAuthors();
});

async function fetchAuthors() {
  loading.value = true;
  
  try {
    // Sử dụng params để gửi các tham số phân trang và tìm kiếm
    const params = {
      page: pagination.current,
      limit: pagination.pageSize,
      search: searchText.value
    };
    
    const response = await authorsApi.getAuthors(params);
    authors.value = response.data;
    
    // Nếu API trả về tổng số mục, cập nhật vào pagination
    if (response.meta && response.meta.total) {
      pagination.total = response.meta.total;
    }
  } catch (error) {
    console.error('Lỗi khi lấy danh sách tác giả:', error);
    message.error('Có lỗi xảy ra khi tải danh sách tác giả');
  } finally {
    loading.value = false;
  }
}

// Xử lý tìm kiếm
function handleSearch() {
  pagination.current = 1;
  fetchAuthors();
}

// Đặt lại bộ lọc
function resetFilters() {
  searchText.value = '';
  pagination.current = 1;
  fetchAuthors();
}

// Xử lý thay đổi bảng (phân trang, sắp xếp)
function handleTableChange(pag) {
  pagination.current = pag.current;
  pagination.pageSize = pag.pageSize;
  fetchAuthors();
}

// Hiển thị modal thêm tác giả
function showAddModal() {
  editingAuthor.value = null;

  // Reset form
  Object.assign(authorForm, {
    name: '',
  });
  
  modalVisible.value = true;
}

// Hiển thị modal sửa tác giả
function showEditModal(record) {
  editingAuthor.value = record;
  
  // Điền form
  Object.assign(authorForm, {
    name: record.name,
  });
  
  modalVisible.value = true;
}

// Xử lý hủy modal
function cancelModal() {
  modalVisible.value = false;
}

// Xử lý submit modal
async function handleModalOk() {
  confirmLoading.value = true;
  try {
    const payload = {
      name: authorForm.name,
    };
    
    if (editingAuthor.value) {
      // Cập nhật tác giả
      await authorsApi.updateAuthor(editingAuthor.value.id, payload);
      message.success('Cập nhật tác giả thành công!');
    } else {
      // Thêm tác giả mới
      await authorsApi.createAuthor(payload);
      message.success('Thêm tác giả mới thành công!');
    }
    
    modalVisible.value = false;
    fetchAuthors();
  } catch (error) {
    console.error('Lỗi khi lưu tác giả:', error);
    message.error('Có lỗi xảy ra khi lưu tác giả');
  } finally {
    confirmLoading.value = false;
  }
}

// Xử lý xóa tác giả
async function handleDelete(id) {
  loading.value = true;
  
  try {
    await authorsApi.deleteAuthor(id);
    message.success('Xóa thành công tác giả');
    fetchAuthors();
  } catch (error) {
    console.error('Lỗi khi xóa tác giả:', error);
    message.error('Có lỗi xảy ra khi xóa tác giả');
  } finally {
    loading.value = false;
  }
}

definePageMeta({
  layout: 'admin'
});
</script>