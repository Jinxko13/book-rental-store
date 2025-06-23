<!-- pages/admin/books.vue -->
<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Quản lý sách</h2>
      <a-button type="primary" @click="showAddModal">
        <template #icon>
          <PlusOutlined />
        </template>
        Thêm sách mới
      </a-button>
    </div>

    <a-card class="mb-4">
      <div class="flex flex-wrap gap-4">
        <a-input v-model:value="searchText" placeholder="Tìm kiếm theo tên sách" style="width: 250px"
          @press-enter="handleSearch">
          <template #prefix>
            <SearchOutlined />
          </template>
        </a-input>

        <a-select v-model:value="selectedCategory" placeholder="Lọc theo danh mục" style="width: 200px"
          @change="handleSearch">
          <a-select-option value="">Tất cả danh mục</a-select-option>
          <a-select-option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </a-select-option>
        </a-select>

        <a-select v-model:value="selectedAuthor" placeholder="Lọc theo tác giả" style="width: 200px"
          @change="handleSearch">
          <a-select-option value="">Tất cả tác giả</a-select-option>
          <a-select-option v-for="author in authors" :key="author.id" :value="author.id">
            {{ author.name }}
          </a-select-option>
        </a-select>

        <a-button type="primary" @click="handleSearch">
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

    <a-table :dataSource="books" :columns="columns" :pagination="pagination" :loading="loading"
      @change="handleTableChange" rowKey="id" :scroll="{ x: 1200 }">
      <template #bodyCell="{ column, record }">
        <!-- Hình ảnh -->
        <template v-if="column.key === 'image'">
          <a-image :width="60"
            :src="record.image && record.image.length > 0 ? record.image[0].url : 'https://via.placeholder.com/60x80'"
            :alt="record.title" />
        </template>

        <!-- Danh mục -->
        <template v-if="column.key === 'category'">
          <a-tag v-for="cat in record.category" :key="cat.id" color="blue" class="mb-1">
            {{ cat.name }}
          </a-tag>
        </template>

        <!-- Giá -->
        <template v-if="column.key === 'price'">
          {{ formatCurrency(record.price) }}
        </template>

        <!-- Trạng thái -->
        <template v-if="column.key === 'status'">
          <a-tag :color="record.quantity > 0 ? 'green' : 'red'">
            {{ record.quantity > 0 ? 'Có sẵn' : 'Không khả dụng' }}
          </a-tag>
        </template>

        <!-- Hành động -->
        <template v-if="column.key === 'actions'">
          <div class="flex gap-2">
            <a-button type="primary" size="small" @click="showEditModal(record)">
              <template #icon>
                <EditOutlined />
              </template>
              Sửa
            </a-button>

            <a-popconfirm title="Bạn có chắc muốn xóa sách này?" ok-text="Có" cancel-text="Không"
              @confirm="handleDelete(record.id)">
              <a-button type="primary" danger size="small">
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

    <!-- Modal Thêm/Sửa Sách -->
    <a-modal :title="editingBook ? 'Sửa thông tin sách' : 'Thêm sách mới'" :visible="modalVisible" @cancel="cancelModal"
      :confirmLoading="confirmLoading" @ok="handleModalOk" width="700px" :maskClosable="false">
      <a-form :model="bookForm" layout="vertical" :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
        <a-row :gutter="16">
          <a-col :span="12">
            <a-form-item label="Tên sách" name="title" :rules="[{ required: true, message: 'Vui lòng nhập tên sách' }]">
              <a-input v-model:value="bookForm.title" placeholder="Nhập tên sách" />
            </a-form-item>
          </a-col>

          <a-col :span="12">
            <a-form-item label="Tác giả" name="author_id"
              :rules="[{ required: true, message: 'Vui lòng chọn tác giả' }]">
              <a-select v-model:value="bookForm.author_id" placeholder="Chọn tác giả" show-search
                :filter-option="filterAuthorOption">
                <a-select-option v-for="author in authors" :key="author.id" :value="author.id">
                  {{ author.name }}
                </a-select-option>
              </a-select>
            </a-form-item>
          </a-col>
        </a-row>

        <a-row :gutter="16">
          <a-col :span="12">
            <a-form-item label="Số lượng" name="quantity"
              :rules="[{ required: true, message: 'Vui lòng nhập số lượng' }]">
              <a-input-number v-model:value="bookForm.quantity" :min="0" style="width: 100%"
                placeholder="Nhập số lượng" />
            </a-form-item>
          </a-col>

          <a-col :span="12">
            <a-form-item label="Giá" name="price" :rules="[{ required: true, message: 'Vui lòng nhập giá' }]">
              <a-input-number v-model:value="bookForm.price" :min="0" style="width: 100%" placeholder="Nhập giá"
                :formatter="value => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="value => value.replace(/\$\s?|(,*)/g, '')" />
            </a-form-item>
          </a-col>
        </a-row>

        <a-form-item label="Danh mục" name="category_id"
          :rules="[{ required: true, type: 'array', message: 'Vui lòng chọn ít nhất một danh mục' }]">
          <a-select v-model:value="bookForm.category_id" placeholder="Chọn danh mục" mode="multiple" :max-tag-count="3"
            :max-tag-placeholder="tagPlaceholder">
            <a-select-option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </a-select-option>
          </a-select>
        </a-form-item>

        <a-form-item label="Mô tả" name="description" :rules="[{ required: true, message: 'Vui lòng nhập mô tả' }]">
          <a-textarea v-model:value="bookForm.description" :rows="4" placeholder="Nhập mô tả sách" :max-length="1000"
            show-count />
        </a-form-item>

        <a-row :gutter="16">
          <a-col :span="18">
            <a-form-item label="Hình ảnh" name="image"
              :rules="[{ required: true, message: 'Vui lòng tải lên ít nhất một hình ảnh' }]">
              <a-upload v-model:fileList="fileList" list-type="picture-card" :multiple="true"
                :before-upload="beforeUpload" @change="handleImageChange" @preview="handlePreview">
                <div v-if="fileList.length < 10">
                  <UploadOutlined />
                  <div class="mt-2">Tải lên</div>
                </div>
              </a-upload>
              <div class="ant-form-item-extra">Hỗ trợ: JPG, PNG, JPEG, GIF, SVG (tối đa 2MB mỗi ảnh)</div>
            </a-form-item>
          </a-col>

          <a-col :span="6">
            <a-form-item label="Trạng thái" name="status">
              <a-switch v-model:checked="bookForm.isAvailable" checked-children="Có sẵn"
                un-checked-children="Không khả dụng" />
            </a-form-item>
          </a-col>
        </a-row>
      </a-form>

      <!-- Image Preview Modal -->
      <a-modal :visible="previewVisible" :title="previewTitle" :footer="null" @cancel="handleCancelPreview">
        <img alt="preview" style="width: 100%" :src="previewImage" />
      </a-modal>
    </a-modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue';
import {
  PlusOutlined,
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
  ReloadOutlined,
  UploadOutlined
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

const authorApi = useAuthors();
const categoryApi = useCategories();
const bookApi = useBooks();

// Định nghĩa cột cho bảng
const columns = [
  {
    title: 'Hình ảnh',
    key: 'image',
    customRender: ({ record }) => record.image ? null : record.image[0].url,
    width: 80
  },
  {
    title: 'Tên sách',
    dataIndex: 'title',
    key: 'title',
    sorter: true,
  },
  {
    title: 'Tác giả',
    key: 'author',
    customRender: ({ record }) => record.author.name
  },
  {
    title: 'Danh mục',
    key: 'category',
    customRender: ({ record }) => getCategoryName(record.category)
  },
  {
    title: 'Giá thuê',
    dataIndex: 'rentalPrice',
    key: 'rentalPrice',
    sorter: true,
    customRender: ({ record }) => `${record.price.toLocaleString()} VND`
  },
  {
    title: 'Số lượng',
    dataIndex: 'quantity',
    key: 'quantity',
    sorter: true,
  },
  {
    title: 'Trạng thái',
    key: 'status',
  },
  {
    title: 'Thao tác',
    key: 'actions',
    width: 200
  }
];

// State
const loading = ref(false);
const searchText = ref('');
const selectedCategory = ref('');
const selectedAuthor = ref('');
const modalVisible = ref(false);
const confirmLoading = ref(false);
const editingBook = ref(null);
const fileList = ref([]);

// Preview image states
const previewVisible = ref(false);
const previewImage = ref('');
const previewTitle = ref('');

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
const bookForm = reactive({
  title: '',
  author_id: null,
  category_id: [],
  description: '',
  price: 0,
  quantity: 0,
  isAvailable: true
});

// Data
const books = ref([]);
const authors = ref([]);
const categories = ref([]);

// API calls
async function fetchBooks() {
  loading.value = true;
  try {
    const params = {
      limit: pagination.pageSize,
      page: pagination.current,
      search: searchText.value || undefined,
      category_id: selectedCategory.value || undefined,
      author_id: selectedAuthor.value || undefined,
    };
    
    const response = await bookApi.getBooks(params);
    
    if (response.data) {
      books.value = response.data;
    }

    if (response.numberRecord) {
      pagination.total = response.numberRecord;
    }
  } catch (error) {
    console.error("Error fetching books:", error);
    message.error("Không thể tải danh sách sách. Vui lòng thử lại sau.");
  } finally {
    loading.value = false;
  }
}

async function fetchAuthors() {
  try {
    const response = await authorApi.getAuthors();
    
    if (response.data) {
      authors.value = response.data;
    }
  } catch (err) {
    console.error(err);
    message.error("Không thể tải danh sách tác giả");
  }
}

async function fetchCategories() {
  try {
    const response = await categoryApi.getCategories();
    
    if (response.data) {
      categories.value = response.data;
    }
  } catch (err) {
    console.error(err);
    message.error("Không thể tải danh sách danh mục");
  }
}

// Setup initial data
onMounted(() => {
  fetchBooks();
  fetchAuthors();
  fetchCategories();
});

// Watch for pagination changes
watch([() => pagination.current, () => pagination.pageSize], () => {
  fetchBooks();
});

// Search and filter handlers
function handleSearch() {
  pagination.current = 1;
  fetchBooks();
}

function resetFilters() {
  searchText.value = '';
  selectedCategory.value = '';
  selectedAuthor.value = '';
  pagination.current = 1;
  fetchBooks();
}

// Table handlers
function handleTableChange(pag) {
  pagination.current = pag.current;
  pagination.pageSize = pag.pageSize;
  fetchBooks();
}

// Helper functions
function getCategoryName(categories) {
  if (!categories || !Array.isArray(categories)) return '';
  return categories.map(c => c.name).join(', ');
}

function formatCurrency(value) {
  return value ? `${value.toLocaleString()} VND` : '0 VND';
}

function filterAuthorOption(input, option) {
  return option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0;
}

function tagPlaceholder(tags) {
  return `+${tags.length} danh mục`;
}

// Form handling
function showAddModal() {
  editingBook.value = null;

  // Reset form
  Object.assign(bookForm, {
    title: '',
    author_id: null,
    category_id: [],
    description: '',
    price: 0,
    quantity: 0,
    isAvailable: true
  });

  fileList.value = [];
  modalVisible.value = true;
}

async function urlToFile(url, filename) {
  const response = await fetch(url);
  const blob = await response.blob();
  const contentType = blob.type || 'image/jpeg';
  return new File([blob], filename, { type: contentType });
}

async function showEditModal(record) {
  editingBook.value = record;
  // Fill form with book data
  Object.assign(bookForm, {
    title: record.title,
    author_id: record.author.id,
    category_id: record.category.map(c => c.id),
    description: record.description,
    price: record.price,
    quantity: record.quantity,
    isAvailable: record.quantity > 0
  });

  fileList.value = [];

  if (record.image && Array.isArray(record.image)) {
    for (const element of record.image) {
      try {
        const fileObject = await urlToFile(element.url, 'thumbnail.png');
        fileList.value.push({
          uid: element.id.toString(),
          name: 'thumbnail.png',
          status: 'done',
          url: element.url,
          originFileObj: fileObject
        });
      } catch (error) {
        console.error('Không thể tải ảnh từ URL:', element.url, error);
      }
    }
  }

  modalVisible.value = true;
}

function cancelModal() {
  modalVisible.value = false;
  fileList.value = [];
}

// File upload handlers
function beforeUpload(file) {
  const isJpgOrPng = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'].includes(file.type);
  if (!isJpgOrPng) {
    message.error('Chỉ chấp nhận định dạng JPG/PNG/GIF/SVG!');
  }

  const isLt2M = file.size / 1024 / 1024 < 2;
  if (!isLt2M) {
    message.error('Hình ảnh phải nhỏ hơn 2MB!');
  }

  // Return false to prevent auto upload
  return false;
}

function handleImageChange({ fileList: newFileList }) {
  fileList.value = newFileList;
}

function handlePreview(file) {
  previewImage.value = file.url || file.thumbUrl;
  previewVisible.value = true;
  previewTitle.value = file.name || file.url.substring(file.url.lastIndexOf('/') + 1);
}

function handleCancelPreview() {
  previewVisible.value = false;
}

// Form submission
async function handleModalOk() {
  confirmLoading.value = true;

  try {
    const formData = new FormData();

    // Append form fields
    formData.append('title', bookForm.title);
    formData.append('author_id', bookForm.author_id);
    formData.append('description', bookForm.description);
    formData.append('price', bookForm.price);
    formData.append('quantity', bookForm.quantity);
    
    // Append categories as array
    bookForm.category_id.forEach(id => {
      formData.append('category_id[]', id);
    });
    
    // Append images
    if (fileList.value.length > 0) {
      fileList.value.forEach(file => {
        if (file.originFileObj) {
          formData.append('image[]', file.originFileObj);
        }
      });
    }

    // Call the appropriate composable method
    if (editingBook.value) {
      await bookApi.updateBook(editingBook.value.id, formData);
      message.success('Cập nhật sách thành công!');
    } else {
      await bookApi.createBook(formData);
      message.success('Thêm sách mới thành công!');
    }

    modalVisible.value = false;
    fetchBooks();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      console.error("Validation errors:", error.response.data.errors);
      message.error("Lỗi xác thực dữ liệu. Vui lòng kiểm tra lại thông tin.");
    } else {
      console.error("Error submitting form:", error);
      message.error("Đã xảy ra lỗi khi lưu dữ liệu. Vui lòng thử lại sau.");
    }
  } finally {
    confirmLoading.value = false;
  }
}

// Delete handler
async function handleDelete(id) {
  loading.value = true;
  
  try {
    await bookApi.deleteBook(id);
    message.success('Xóa sách thành công!');
    fetchBooks();
  } catch (error) {
    console.error("Error deleting book:", error);
    message.error("Không thể xóa sách. Vui lòng thử lại sau.");
  } finally {
    loading.value = false;
  }
}

definePageMeta({
  layout: 'admin'
});
</script>