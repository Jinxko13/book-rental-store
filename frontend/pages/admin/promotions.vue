<!-- pages/admin/promotions.vue -->
<template>
    <div>
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Quản lý chương trình khuyến mãi</h2>
        <a-button type="primary" @click="showAddModal">
          <template #icon><PlusOutlined /></template>
          Thêm khuyến mãi mới
        </a-button>
      </div>
  
      <a-card class="mb-4">
        <div class="flex gap-4">
          <a-input
            v-model:value="searchText"
            placeholder="Tìm kiếm theo tên khuyến mãi"
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
        :dataSource="promotions"
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
                title="Bạn có chắc muốn xóa chương trình này?"
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
  
      <!-- Modal Thêm/Sửa Khuyến mãi -->
      <a-modal
        :title="editingPromotion ? 'Sửa khuyến mãi' : 'Thêm khuyến mãi mới'"
        :open="modalVisible"
        @cancel="cancelModal"
        :confirmLoading="confirmLoading"
        @ok="handleModalOk"
      >
        <a-form :model="promotionForm" layout="vertical">
          <a-form-item label="Tên khuyến mãi" name="name" :rules="[{ required: true }]">
            <a-input v-model:value="promotionForm.name" />
          </a-form-item>
          <a-form-item label="Mô tả" name="description">
            <a-input v-model:value="promotionForm.description" />
          </a-form-item>
          <a-form-item label="Giảm giá (%)" name="discount" :rules="[{ required: true, type: 'number', min: 0, max: 100 }]">
            <a-input-number v-model:value="promotionForm.discount" style="width: 100%" />
          </a-form-item>
          <a-form-item label="Trạng thái" name="status">
            <a-radio-group v-model:value="promotionForm.status">
              <a-radio value="active">Hoạt động</a-radio>
              <a-radio value="inactive">Không hoạt động</a-radio>
            </a-radio-group>
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
  const promotionApi = usePromotions();
  
  
  // Định nghĩa cột cho bảng
  const columns = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
      width: 80,
    },
    {
      title: 'Tên khuyến mãi',
      dataIndex: 'name',
      key: 'name',
      sorter: (a, b) => a.name.localeCompare(b.name),
    },
    {
      title: 'Giảm giá (%)',
      dataIndex: 'discount_percent',
      key: 'discount',
      sorter: true,
    },
    {
      title: 'Trạng thái',
      dataIndex: 'status',
      key: 'status',
    },
    {
      title: 'Thao tác',
      key: 'actions',
      width: 150,
    },
  ];
  
  // State
  const loading = ref(false);
  const searchText = ref('');
  const modalVisible = ref(false);
  const confirmLoading = ref(false);
  const editingPromotion = ref(null);
  const promotions = ref([]);
  
  // Pagination
  const pagination = reactive({
    current: 1,
    pageSize: 10,
    total: 0,
    showSizeChanger: true,
    pageSizeOptions: ['10', '20', '50', '100'],
    showTotal: (total) => `Tổng ${total} chương trình`,
  });
  
  // Form
  const promotionForm = reactive({
    name: '',
    description: '',
    discount: 0,
    status: 'active',
  });
  
  // Fetch data
  onMounted(() => {
    fetchPromotions();
  });
  
  async function fetchPromotions() {
    loading.value = true;
  
    try {
      const data = await promotionApi.getPromotions({});
      promotions.value = data;

    } catch (error) {
      console.error('Lỗi khi lấy danh sách khuyến mãi:', error);
      message.error('Có lỗi xảy ra khi tải danh sách khuyến mãi');
    } finally {
      loading.value = false;
    }
  }
  
  // Xử lý tìm kiếm
  function handleSearch() {
    pagination.current = 1;
    fetchPromotions();
  }
  
  // Đặt lại bộ lọc
  function resetFilters() {
    searchText.value = '';
    pagination.current = 1;
    fetchPromotions();
  }
  
  // Xử lý thay đổi bảng (phân trang, sắp xếp)
  function handleTableChange(pag, filters, sorter) {
    pagination.current = pag.current;
    pagination.pageSize = pag.pageSize;
    fetchPromotions();
  }
  
  // Hiển thị modal thêm khuyến mãi
  function showAddModal() {
    editingPromotion.value = null;
  
    // Reset form
    Object.assign(promotionForm, {
      name: '',
      description: '',
      discount: 0,
      status: 'active',
    });
  
    modalVisible.value = true;
  }
  
  // Hiển thị modal sửa khuyến mãi
  function showEditModal(record) {
    editingPromotion.value = record;
  
    // Điền form
    Object.assign(promotionForm, {
      name: record.name,
      description: record.description,
      discount: record.discount_percent,
      status: record.status ? 'active' : 'inactive',
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
        name: promotionForm.name,
        description: promotionForm.description,
        discount: promotionForm.discount,
        status: promotionForm.status,
      };
  
      let response;
      if (editingPromotion.value) {
        // Cập nhật khuyến mãi
        await promotionApi.updatePromotion(editingPromotion.value.id, payload);
        message.success('Cập nhật khuyến mãi thành công!');
      } else {
        // Thêm khuyến mãi mới
        await promotionApi.createPromotion(payload);
        message.success('Thêm khuyến mãi mới thành công!');
      }
  
      modalVisible.value = false;
      fetchPromotions();
    } catch (error) {
      console.error('Lỗi khi lưu khuyến mãi:', error);
      message.error('Có lỗi xảy ra khi lưu khuyến mãi');
    } finally {
      confirmLoading.value = false;
    }
  }
  
  // Xử lý xóa khuyến mãi
  async function handleDelete(id) {
    loading.value = true;
  
    try {
      await promotionApi.deletePromotion(editingPromotion.value.id);
      message.success('Xóa thành công khuyến mãi');
      fetchPromotions();
    } catch (error) {
      console.error('Lỗi khi xóa khuyến mãi:', error);
      message.error('Có lỗi xảy ra khi xóa khuyến mãi');
    } finally {
      loading.value = false;
    }
  }
  
  definePageMeta({
    layout: 'admin',
  });
  </script>
  