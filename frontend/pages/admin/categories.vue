<!-- pages/admin/categories.vue -->
<template>
    <div>
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Quản lý danh mục sách</h2>
        <a-button type="primary" @click="showAddModal">
          <template #icon><PlusOutlined /></template>
          Thêm danh mục mới
        </a-button>
      </div>
  
      <a-card class="mb-4">
        <div class="flex gap-4">
          <a-input
            v-model:value="searchText"
            placeholder="Tìm kiếm theo tên danh mục"
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
        :dataSource="categories"
        :columns="columns"
        :pagination="pagination"
        :loading="loading"
        @change="handleTableChange"
        rowKey="id"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'status'">
            <a-tag :color="record.status === 'active' ? 'green' : 'red'">
              {{ record.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
            </a-tag>
          </template>
          <template v-if="column.key === 'actions'">
            <div class="flex gap-2">
              <a-button type="primary" size="small" @click="showEditModal(record)">
                <template #icon><EditOutlined /></template>
                Sửa
              </a-button>
              <a-popconfirm
                title="Bạn có chắc muốn xóa danh mục này?"
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
  
      <!-- Modal Thêm/Sửa Danh mục -->
      <a-modal
        :title="editingCategory ? 'Sửa danh mục' : 'Thêm danh mục mới'"
        :open="modalVisible"
        @cancel="cancelModal"
        :confirmLoading="confirmLoading"
        @ok="handleModalOk"
      >
        <a-form :model="categoryForm" layout="vertical">
          <a-form-item label="Tên danh mục" name="name" :rules="[{ required: true }]">
            <a-input v-model:value="categoryForm.name" />
          </a-form-item>
        </a-form>
      </a-modal>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted } from 'vue';
  import {
    PlusOutlined,
    SearchOutlined,
    EditOutlined,
    DeleteOutlined,
    ReloadOutlined,
  } from '@ant-design/icons-vue';
  import { message } from 'ant-design-vue';
  const categoriesApi = useCategories();
  // Định nghĩa cột cho bảng
  const columns = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
      width: 80
    },
    {
      title: 'Tên danh mục',
      dataIndex: 'name',
      key: 'name',
      // sorter: true,
      sorter: (a, b) => a.name.localeCompare(b.name),
    },
    // {
    //   title: 'Số lượng sách',
    //   dataIndex: 'bookCount',
    //   key: 'bookCount',
    //   sorter: true,
    // },
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
  const editingCategory = ref(null);
  const categories = ref([]);
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
  const categoryForm = reactive({
    name: '',
    description: '',
    icon: 'BookOutlined',
    order: 0,
    status: 'active'
  });
  const isAvailable = reactive(categoryForm.status === 'active');
  
  // Fetch data
  onMounted(() => {
    fetchCategories();
  });
  
  async function fetchCategories() {
    loading.value = true;
  
    try {
      // Sử dụng params để gửi các tham số phân trang và tìm kiếm
      const params = {
        page: pagination.current,
        limit: pagination.pageSize,
        search: searchText.value
      };
      const response = await categoriesApi.getCategories(params);
      categories.value = response.data;

      // Nếu API trả về tổng số mục, cập nhật vào pagination
      if (response.meta && response.meta.total) {
        pagination.total = response.meta.total;
      }
      
    } catch (error) {
      console.error('Lỗi khi lấy danh sách danh mục:', error);
      message.error('Có lỗi xảy ra khi tải danh sách danh mục');
    } finally {
      loading.value = false;
    }      
  }
  
  // Xử lý tìm kiếm
  function handleSearch() {
    pagination.current = 1;
    fetchCategories();
  }
  
  // Đặt lại bộ lọc
  function resetFilters() {
    searchText.value = '';
    pagination.current = 1;
    fetchCategories();
  }
  
  /// Xử lý thay đổi bảng (phân trang, sắp xếp)
  function handleTableChange(pag) {
    pagination.current = pag.current;
    pagination.pageSize = pag.pageSize;
    fetchCategories();
  }

    
  // Hiển thị modal thêm danh mục
  function showAddModal() {
    editingCategory.value = null;
    
    // Reset form
    Object.assign(categoryForm, {
      name: '',
    });
    
    modalVisible.value = true;
  }
  
  // Hiển thị modal sửa danh mục
  function showEditModal(record) {
    editingCategory.value = record;
    
    // Điền form
    Object.assign(categoryForm, {
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
        name: categoryForm.name,
      };
      
      let response;
      if (editingCategory.value) {
        // Cập nhật danh mục
        await categoriesApi.updateCategory(editingCategory.value.id, payload)
        message.success('Cập nhật danh mục thành công!');
      } else {
        // Thêm danh mục mới
        await categoriesApi.createCategory(payload)
        message.success('Thêm danh mục mới thành công!');
      }
      
      modalVisible.value = false;
      fetchCategories();
    } catch (error) {
      console.error('Lỗi khi lưu danh mục:', error);
      message.error('Có lỗi xảy ra khi lưu danh mục');
    } finally {
      confirmLoading.value = false;
    }
  }
  
  // Xử lý xóa danh mục
  async function handleDelete(id) {
    loading.value = true;
    
    try {
      await categoriesApi.deleteCategory(id);
      message.success('Xóa thành công danh mục');
      fetchCategories();
    } catch (error) {
      console.error('Lỗi khi xóa danh mục:', error);
      message.error('Có lỗi xảy ra khi xóa danh mục');
    } finally {
      loading.value = false;
    }
  }
  
  definePageMeta({
    layout: 'admin'
  });
  </script>