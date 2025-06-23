<!-- pages/admin/settings.vue -->
<template>
    <div>
      <h1 class="text-2xl font-bold mb-6">Cài đặt hệ thống</h1>
      
      <a-tabs default-active-key="1">
        <a-tab-pane key="1" tab="Cài đặt chung">
          <a-form :model="generalSettings" layout="vertical">
            <a-card class="mb-6" title="Thông tin cửa hàng">
              <a-form-item label="Tên cửa hàng">
                <a-input v-model:value="generalSettings.storeName" placeholder="Nhập tên cửa hàng" />
              </a-form-item>
              <a-form-item label="Slogan">
                <a-input v-model:value="generalSettings.slogan" placeholder="Nhập slogan cửa hàng" />
              </a-form-item>
              <a-form-item label="Địa chỉ email">
                <a-input v-model:value="generalSettings.email" placeholder="Email liên hệ" />
              </a-form-item>
              <a-form-item label="Số điện thoại">
                <a-input v-model:value="generalSettings.phone" placeholder="Số điện thoại liên hệ" />
              </a-form-item>
              <a-form-item label="Địa chỉ">
                <a-textarea v-model:value="generalSettings.address" rows="3" placeholder="Địa chỉ cửa hàng" />
              </a-form-item>
              
              <a-form-item label="Logo cửa hàng">
                <a-upload
                  v-model:file-list="fileList"
                  action="/api/admin/upload"
                  list-type="picture-card"
                  :show-upload-list="{ showPreviewIcon: false }"
                  :before-upload="beforeUpload"
                  @change="handleChange"
                >
                  <div v-if="fileList.length < 1">
                    <PlusOutlined />
                    <div class="mt-2">Tải lên</div>
                  </div>
                </a-upload>
              </a-form-item>
            </a-card>
            
            <a-card class="mb-6" title="Cài đặt hiển thị">
              <a-form-item label="Số sách hiển thị trên mỗi trang">
                <a-input-number v-model:value="generalSettings.booksPerPage" :min="5" :max="50" />
              </a-form-item>
              <a-form-item label="Định dạng ngày tháng">
                <a-select v-model:value="generalSettings.dateFormat">
                  <a-select-option value="DD/MM/YYYY">DD/MM/YYYY</a-select-option>
                  <a-select-option value="MM/DD/YYYY">MM/DD/YYYY</a-select-option>
                  <a-select-option value="YYYY-MM-DD">YYYY-MM-DD</a-select-option>
                </a-select>
              </a-form-item>
              <a-form-item label="Ngôn ngữ mặc định">
                <a-select v-model:value="generalSettings.defaultLanguage">
                  <a-select-option value="vi">Tiếng Việt</a-select-option>
                  <a-select-option value="en">Tiếng Anh</a-select-option>
                </a-select>
              </a-form-item>
            </a-card>
            
            <a-button type="primary" size="large" @click="saveGeneralSettings">
              Lưu cài đặt
            </a-button>
          </a-form>
        </a-tab-pane>
        
        <a-tab-pane key="2" tab="Cài đặt cho thuê">
          <a-card class="mb-6">
            <a-form :model="rentalSettings" layout="vertical">
              <a-form-item label="Thời gian cho thuê mặc định (ngày)">
                <a-input-number v-model:value="rentalSettings.defaultRentalDays" :min="1" :max="90" />
              </a-form-item>
              <a-form-item label="Phí trễ hạn mỗi ngày (VNĐ)">
                <a-input-number 
                  v-model:value="rentalSettings.lateFeePerDay" 
                  :min="0" 
                  :step="1000"
                  :formatter="value => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  :parser="value => value.replace(/\$\s?|(,*)/g, '')"
                />
              </a-form-item>
              <a-form-item label="Số sách tối đa cho thuê mỗi lần">
                <a-input-number v-model:value="rentalSettings.maxBooksPerRental" :min="1" :max="20" />
              </a-form-item>
              <a-form-item label="Áp dụng đặt cọc">
                <a-switch v-model:checked="rentalSettings.requireDeposit" />
              </a-form-item>
              <a-form-item label="Tỉ lệ đặt cọc (% giá sách)" v-if="rentalSettings.requireDeposit">
                <a-slider v-model:value="rentalSettings.depositPercentage" :min="10" :max="100" :step="5" />
                <span>{{ rentalSettings.depositPercentage }}%</span>
              </a-form-item>
              <a-form-item label="Cho phép gia hạn">
                <a-switch v-model:checked="rentalSettings.allowExtension" />
              </a-form-item>
              <a-form-item label="Số lần gia hạn tối đa" v-if="rentalSettings.allowExtension">
                <a-input-number v-model:value="rentalSettings.maxExtensions" :min="1" :max="5" />
              </a-form-item>
              
              <a-button type="primary" size="large" @click="saveRentalSettings">
                Lưu cài đặt cho thuê
              </a-button>
            </a-form>
          </a-card>
        </a-tab-pane>
        
        <a-tab-pane key="3" tab="Cài đặt email">
          <a-card class="mb-6">
            <a-form :model="emailSettings" layout="vertical">
              <a-form-item label="SMTP Host">
                <a-input v-model:value="emailSettings.smtpHost" placeholder="smtp.example.com" />
              </a-form-item>
              <a-form-item label="SMTP Port">
                <a-input-number v-model:value="emailSettings.smtpPort" :min="1" :max="65535" />
              </a-form-item>
              <a-form-item label="SMTP User">
                <a-input v-model:value="emailSettings.smtpUser" placeholder="user@example.com" />
              </a-form-item>
              <a-form-item label="SMTP Password">
                <a-input-password v-model:value="emailSettings.smtpPassword" placeholder="Mật khẩu" />
              </a-form-item>
              <a-form-item label="Email gửi">
                <a-input v-model:value="emailSettings.fromEmail" placeholder="noreply@example.com" />
              </a-form-item>
              <a-form-item label="Tên người gửi">
                <a-input v-model:value="emailSettings.fromName" placeholder="Thư viện sách" />
              </a-form-item>
              <a-form-item label="Mã hóa">
                <a-select v-model:value="emailSettings.encryption">
                  <a-select-option value="tls">TLS</a-select-option>
                  <a-select-option value="ssl">SSL</a-select-option>
                  <a-select-option value="none">Không mã hóa</a-select-option>
                </a-select>
              </a-form-item>
              
              <div class="flex space-x-4">
                <a-button type="primary" @click="saveEmailSettings">
                  Lưu cài đặt
                </a-button>
                <a-button @click="testEmailConnection">
                  Kiểm tra kết nối
                </a-button>
              </div>
            </a-form>
          </a-card>
          
          <a-card title="Mẫu email" class="mb-6">
            <a-tabs default-active-key="welcome">
              <a-tab-pane key="welcome" tab="Email chào mừng">
                <a-form-item label="Tiêu đề">
                  <a-input v-model:value="emailSettings.templates.welcome.subject" />
                </a-form-item>
                <a-form-item label="Nội dung">
                  <a-textarea v-model:value="emailSettings.templates.welcome.content" rows="6" />
                </a-form-item>
              </a-tab-pane>
              <a-tab-pane key="rental" tab="Email xác nhận cho thuê">
                <a-form-item label="Tiêu đề">
                  <a-input v-model:value="emailSettings.templates.rental.subject" />
                </a-form-item>
                <a-form-item label="Nội dung">
                  <a-textarea v-model:value="emailSettings.templates.rental.content" rows="6" />
                </a-form-item>
              </a-tab-pane>
              <a-tab-pane key="reminder" tab="Email nhắc nhở">
                <a-form-item label="Tiêu đề">
                  <a-input v-model:value="emailSettings.templates.reminder.subject" />
                </a-form-item>
                <a-form-item label="Nội dung">
                  <a-textarea v-model:value="emailSettings.templates.reminder.content" rows="6" />
                </a-form-item>
              </a-tab-pane>
            </a-tabs>
            <a-button type="primary" @click="saveEmailTemplates">
              Lưu mẫu email
            </a-button>
          </a-card>
        </a-tab-pane>
        
        <a-tab-pane key="4" tab="Sao lưu & Phục hồi">
          <a-card class="mb-6">
            <a-alert 
              type="warning" 
              message="Lưu ý: Việc sao lưu và phục hồi dữ liệu là thao tác nhạy cảm, hãy đảm bảo bạn hiểu rõ trước khi thực hiện." 
              banner 
              class="mb-4" 
            />
            
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-2">Sao lưu dữ liệu</h3>
              <p class="mb-4">Tạo một bản sao lưu của cơ sở dữ liệu hiện tại.</p>
              <a-button type="primary" @click="createBackup">
                <DownloadOutlined />
                Tạo bản sao lưu mới
              </a-button>
            </div>
  
            <a-divider />
            
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-2">Các bản sao lưu</h3>
              <a-table :columns="backupColumns" :data-source="backupData" :pagination="{ pageSize: 5 }">
                <template #bodyCell="{ column, record }">
                  <template v-if="column.key === 'action'">
                    <a-button-group>
                      <a-button type="primary" size="small" @click="downloadBackup(record.id)">
                        <DownloadOutlined />
                      </a-button>
                      <a-button type="primary" danger size="small" @click="confirmDeleteBackup(record.id)">
                        <DeleteOutlined />
                      </a-button>
                      <a-button type="primary" size="small" @click="confirmRestoreBackup(record.id)">
                        <ReloadOutlined />
                      </a-button>
                    </a-button-group>
                  </template>
                </template>
              </a-table>
            </div>
            
            <a-divider />
            
            <div>
              <h3 class="text-lg font-medium mb-2">Phục hồi dữ liệu</h3>
              <p class="mb-4">Tải lên một tệp sao lưu để phục hồi dữ liệu.</p>
              <a-upload
                name="file"
                :multiple="false"
                action="/api/admin/restore-backup"
                :headers="{ authorization: 'authorization-text' }"
                @change="handleBackupUpload"
              >
                <a-button>
                  <UploadOutlined />
                  Tải lên tệp sao lưu
                </a-button>
              </a-upload>
            </div>
          </a-card>
        </a-tab-pane>
      </a-tabs>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted } from 'vue';
  import {
    PlusOutlined,
    DownloadOutlined,
    UploadOutlined,
    DeleteOutlined,
    ReloadOutlined
  } from '@ant-design/icons-vue';
  import { message } from 'ant-design-vue';
  
  // Cài đặt chung
  const generalSettings = reactive({
    storeName: 'Thư viện sách XYZ',
    slogan: 'Khám phá thế giới qua từng trang sách',
    email: 'contact@bookstore.com',
    phone: '0123456789',
    address: '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh',
    booksPerPage: 20,
    dateFormat: 'DD/MM/YYYY',
    defaultLanguage: 'vi'
  });
  
  // Cài đặt cho thuê
  const rentalSettings = reactive({
    defaultRentalDays: 14,
    lateFeePerDay: 10000,
    maxBooksPerRental: 5,
    requireDeposit: true,
    depositPercentage: 50,
    allowExtension: true,
    maxExtensions: 2
  });
  
  // Cài đặt email
  const emailSettings = reactive({
    smtpHost: 'smtp.gmail.com',
    smtpPort: 587,
    smtpUser: 'example@gmail.com',
    smtpPassword: '',
    fromEmail: 'noreply@bookstore.com',
    fromName: 'Thư viện sách XYZ',
    encryption: 'tls',
    templates: {
      welcome: {
        subject: 'Chào mừng bạn đến với Thư viện sách XYZ',
        content: 'Xin chào {name},\n\nChúng tôi rất vui khi bạn đã đăng ký tài khoản tại Thư viện sách XYZ...'
      },
      rental: {
        subject: 'Xác nhận đơn cho thuê sách #{order_id}',
        content: 'Xin chào {name},\n\nChúng tôi xác nhận bạn đã thuê thành công các đầu sách sau:\n{book_list}\n\nNgày trả: {return_date}'
      },
      reminder: {
        subject: 'Nhắc nhở: Sắp đến hạn trả sách',
        content: 'Xin chào {name},\n\nChúng tôi muốn nhắc bạn rằng các sách sau sẽ đến hạn trả vào ngày {return_date}:\n{book_list}'
      }
    }
  });
  
  // Upload ảnh
  const fileList = ref([]);
  const beforeUpload = (file) => {
    const isJpgOrPng = file.type === 'image/jpeg' || file.type === 'image/png';
    if (!isJpgOrPng) {
      message.error('Chỉ có thể tải lên file JPG hoặc PNG!');
    }
    const isLt2M = file.size / 1024 / 1024 < 2;
    if (!isLt2M) {
      message.error('Hình ảnh phải nhỏ hơn 2MB!');
    }
    return isJpgOrPng && isLt2M;
  };
  
  const handleChange = (info) => {
    if (info.file.status === 'uploading') {
      return;
    }
    if (info.file.status === 'done') {
      message.success(`${info.file.name} đã được tải lên thành công`);
    } else if (info.file.status === 'error') {
      message.error(`${info.file.name} tải lên thất bại.`);
    }
  };
  
  // Xử lý lưu cài đặt
  const saveGeneralSettings = () => {
    message.loading({ content: 'Đang lưu...', key: 'saveGeneral' });
    // Gọi API lưu cài đặt
    setTimeout(() => {
      message.success({ content: 'Lưu cài đặt chung thành công!', key: 'saveGeneral' });
    }, 1000);
  };
  
  const saveRentalSettings = () => {
    message.loading({ content: 'Đang lưu...', key: 'saveRental' });
    // Gọi API lưu cài đặt
    setTimeout(() => {
      message.success({ content: 'Lưu cài đặt cho thuê thành công!', key: 'saveRental' });
    }, 1000);
  };
  
  const saveEmailSettings = () => {
    message.loading({ content: 'Đang lưu...', key: 'saveEmail' });
    // Gọi API lưu cài đặt
    setTimeout(() => {
      message.success({ content: 'Lưu cài đặt email thành công!', key: 'saveEmail' });
    }, 1000);
  };
  
  const saveEmailTemplates = () => {
    message.loading({ content: 'Đang lưu...', key: 'saveTemplates' });
    // Gọi API lưu cài đặt
    setTimeout(() => {
      message.success({ content: 'Lưu mẫu email thành công!', key: 'saveTemplates' });
    }, 1000);
  };
  
  const testEmailConnection = () => {
    message.loading({ content: 'Đang kiểm tra kết nối...', key: 'testEmail' });
    // Gọi API kiểm tra kết nối
    setTimeout(() => {
      message.success({ content: 'Kết nối email thành công!', key: 'testEmail' });
    }, 2000);
  };
  
  // Xử lý sao lưu và phục hồi
  const backupColumns = [
    {
      title: 'Tên file',
      dataIndex: 'name',
      key: 'name',
    },
    {
      title: 'Kích thước',
      dataIndex: 'size',
      key: 'size',
    },
    {
      title: 'Ngày tạo',
      dataIndex: 'created_at',
      key: 'created_at',
    },
    {
      title: 'Thao tác',
      key: 'action',
    },
  ];
  
  const backupData = ref([
    {
      id: 1,
      name: 'backup_2025_05_12_083022.sql',
      size: '5.2 MB',
      created_at: '12/05/2025 08:30:22',
    },
    {
      id: 2,
      name: 'backup_2025_05_01_140519.sql',
      size: '5.1 MB',
      created_at: '01/05/2025 14:05:19',
    },
  ]);
  
  const createBackup = () => {
    message.loading({ content: 'Đang tạo bản sao lưu...', key: 'createBackup' });
    // Gọi API tạo backup
    setTimeout(() => {
      message.success({ content: 'Bản sao lưu đã được tạo thành công!', key: 'createBackup' });
      // Thêm bản sao lưu mới vào danh sách
      const now = new Date();
      const dateStr = `${now.getDate().toString().padStart(2, '0')}/${(now.getMonth() + 1).toString().padStart(2, '0')}/${now.getFullYear()} ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
      
      backupData.value.unshift({
        id: backupData.value.length + 1,
        name: `backup_${now.getFullYear()}_${(now.getMonth() + 1).toString().padStart(2, '0')}_${now.getDate().toString().padStart(2, '0')}_${now.getHours().toString().padStart(2, '0')}${now.getMinutes().toString().padStart(2, '0')}${now.getSeconds().toString().padStart(2, '0')}.sql`,
        size: '5.3 MB',
        created_at: dateStr,
      });
    }, 2000);
  };
  
  const downloadBackup = (id) => {
    message.loading({ content: 'Đang chuẩn bị tải xuống...', key: 'downloadBackup' });
    // Gọi API download backup
    setTimeout(() => {
      message.success({ content: 'Đang tải xuống bản sao lưu!', key: 'downloadBackup' });
    }, 1000);
  };
  
  const confirmDeleteBackup = (id) => {
    // Xác nhận xóa
    window.$dialog.confirm({
      title: 'Xác nhận xóa',
      content: 'Bạn có chắc chắn muốn xóa bản sao lưu này?',
      okText: 'Xóa',
      cancelText: 'Hủy',
      onOk() {
        // Xóa backup
        message.success('Đã xóa bản sao lưu!');
        backupData.value = backupData.value.filter(item => item.id !== id);
      },
    });
  };
  
  const confirmRestoreBackup = (id) => {
    // Xác nhận phục hồi
    window.$dialog.confirm({
      title: 'Xác nhận phục hồi',
      content: 'Việc phục hồi dữ liệu sẽ ghi đè lên dữ liệu hiện tại. Bạn có chắc chắn muốn tiếp tục?',
      okText: 'Phục hồi',
      cancelText: 'Hủy',
      onOk() {
        // Gọi API phục hồi
        message.loading({ content: 'Đang phục hồi dữ liệu...', key: 'restore' });
        setTimeout(() => {
          message.success({ content: 'Phục hồi dữ liệu thành công!', key: 'restore' });
        }, 3000);
      },
    });
  };
  
  const handleBackupUpload = (info) => {
    if (info.file.status === 'uploading') {
      return;
    }
    if (info.file.status === 'done') {
      message.success(`${info.file.name} đã được tải lên thành công`);
      // Xác nhận phục hồi từ file đã tải lên
      confirmRestoreBackup();
    } else if (info.file.status === 'error') {
      message.error(`${info.file.name} tải lên thất bại.`);
    }
  };
  
  // Lấy dữ liệu khi khởi tạo
  onMounted(() => {
    // Gọi API để lấy các cài đặt hiện tại
    // fetchSettings();
  });

  definePageMeta({
    layout: 'admin'
  });
  </script>
  
  <style scoped>
  .ant-upload-picture-card-wrapper {
    width: 128px;
  }
  
  </style>