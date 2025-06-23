<!-- pages/admin/profile.vue -->
<template>
    <div>
      <h1 class="text-2xl font-bold mb-6">Thông tin cá nhân</h1>
      
      <a-row :gutter="24">
        <a-col :span="8">
          <a-card class="mb-4">
            <div class="flex flex-col items-center">
              <a-avatar 
                :size="100" 
                src="https://api.dicebear.com/7.x/avataaars/svg?seed=admin" 
              />
              <h2 class="text-xl font-bold mt-4">Admin User</h2>
              <p class="text-gray-500">Quản trị viên</p>
              
              <a-divider />
              
              <div class="w-full">
                <div class="flex justify-between py-2">
                  <span class="text-gray-600">Tài khoản:</span>
                  <span class="font-medium">admin</span>
                </div>
                <div class="flex justify-between py-2">
                  <span class="text-gray-600">Vai trò:</span>
                  <span class="font-medium">Quản trị viên</span>
                </div>
                <div class="flex justify-between py-2">
                  <span class="text-gray-600">Đã tham gia:</span>
                  <span class="font-medium">01/01/2025</span>
                </div>
              </div>
              
              <a-button type="primary" class="mt-4 w-full">
                Cập nhật ảnh đại diện
              </a-button>
            </div>
          </a-card>
          
          <a-card title="Thông tin bảo mật" class="mb-4">
            <a-button type="primary" block class="mb-3">
              Đổi mật khẩu
            </a-button>
            <a-button block>
              Thiết lập xác thực hai lớp
            </a-button>
          </a-card>
        </a-col>
        
        <a-col :span="16">
          <a-card title="Thông tin chi tiết" class="mb-4">
            <a-form :model="formState" :label-col="{ span: 6 }" :wrapper-col="{ span: 18 }">
              <a-form-item label="Họ và tên">
                <a-input v-model:value="formState.fullName" placeholder="Nhập họ và tên" />
              </a-form-item>
              
              <a-form-item label="Email">
                <a-input v-model:value="formState.email" placeholder="Nhập email" />
              </a-form-item>
              
              <a-form-item label="Số điện thoại">
                <a-input v-model:value="formState.phone" placeholder="Nhập số điện thoại" />
              </a-form-item>
              
              <a-form-item label="Địa chỉ">
                <a-textarea v-model:value="formState.address" placeholder="Nhập địa chỉ" :rows="3" />
              </a-form-item>
              
              <a-form-item label="Giới tính">
                <a-radio-group v-model:value="formState.gender">
                  <a-radio value="male">Nam</a-radio>
                  <a-radio value="female">Nữ</a-radio>
                  <a-radio value="other">Khác</a-radio>
                </a-radio-group>
              </a-form-item>
              
              <a-form-item label="Ngày sinh">
                <a-date-picker v-model:value="formState.birthday" class="w-full" />
              </a-form-item>
              
              <a-form-item :wrapper-col="{ offset: 6, span: 18 }">
                <a-button type="primary" @click="handleSubmit">Lưu thay đổi</a-button>
              </a-form-item>
            </a-form>
          </a-card>
          
          <a-card title="Thông báo" class="mb-4">
            <a-list item-layout="horizontal" :data-source="notifications">
              <template #renderItem="{ item }">
                <a-list-item>
                  <a-list-item-meta
                    :description="item.description"
                  >
                    <template #title>
                      <div class="flex justify-between">
                        <span>{{ item.title }}</span>
                        <span class="text-gray-400 text-sm">{{ item.time }}</span>
                      </div>
                    </template>
                    <template #avatar>
                      <a-avatar :style="{ backgroundColor: item.color }">
                        <template #icon><BellOutlined /></template>
                      </a-avatar>
                    </template>
                  </a-list-item-meta>
                </a-list-item>
              </template>
            </a-list>
          </a-card>
          
          <a-card title="Hoạt động gần đây">
            <a-timeline>
              <a-timeline-item v-for="(activity, index) in activities" :key="index" :color="activity.color">
                <template #dot>
                  <component :is="activity.icon" />
                </template>
                <div class="flex justify-between">
                  <div>
                    <p class="font-medium mb-1">{{ activity.title }}</p>
                    <p class="text-gray-500">{{ activity.description }}</p>
                  </div>
                  <span class="text-gray-400">{{ activity.time }}</span>
                </div>
              </a-timeline-item>
            </a-timeline>
          </a-card>
        </a-col>
      </a-row>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive } from 'vue';
  import { 
    BellOutlined, 
    CheckCircleOutlined, 
    EditOutlined, 
    LoginOutlined, 
    BookOutlined 
  } from '@ant-design/icons-vue';
  
  const formState = reactive({
    fullName: 'Admin User',
    email: 'admin@bookstore.com',
    phone: '0123456789',
    address: '123 Đường Sách, Quận 1, TP. Hồ Chí Minh',
    gender: 'male',
    birthday: null,
  });
  
  const notifications = [
    {
      title: 'Đơn hàng mới',
      description: 'Đơn hàng #12345 đã được tạo',
      time: '1 giờ trước',
      color: '#1890ff',
    },
    {
      title: 'Sách quá hạn',
      description: 'Có 3 đơn hàng quá hạn cần xử lý',
      time: '3 giờ trước',
      color: '#ff4d4f',
    },
    {
      title: 'Cập nhật hệ thống',
      description: 'Hệ thống vừa được cập nhật lên phiên bản mới',
      time: '1 ngày trước',
      color: '#52c41a',
    },
  ];
  
  const activities = [
    {
      icon: LoginOutlined,
      title: 'Đăng nhập hệ thống',
      description: 'Đăng nhập từ IP 192.168.1.1',
      time: 'Hôm nay, 09:30',
      color: 'blue',
    },
    {
      icon: EditOutlined,
      title: 'Cập nhật sách',
      description: 'Cập nhật thông tin sách "Ngôn ngữ lập trình"',
      time: 'Hôm nay, 08:45',
      color: 'green',
    },
    {
      icon: CheckCircleOutlined,
      title: 'Duyệt đơn hàng',
      description: 'Duyệt đơn hàng #12344 của khách hàng Nguyễn Văn A',
      time: 'Hôm qua, 15:20',
      color: 'green',
    },
    {
      icon: BookOutlined,
      title: 'Thêm sách mới',
      description: 'Thêm sách "Lập trình hướng đối tượng" vào hệ thống',
      time: 'Hôm qua, 10:15',
      color: 'blue',
    },
  ];
  
  const handleSubmit = () => {
    console.log('Form submitted', formState);
    // Gọi API để cập nhật thông tin
    alert('Đã lưu thay đổi thành công!');
  };
    
  definePageMeta({
    layout: 'admin'
  });
  </script>