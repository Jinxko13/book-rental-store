# pages/auth/register.vue
<template>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Đăng ký</h1>
        <p>Tạo tài khoản mới để bắt đầu</p>
      </div>
      
      <a-form
        :model="registerForm"
        :rules="registerRules"
        @finish="handleRegister"
        layout="vertical"
        class="auth-form"
      >
        <a-form-item name="name" label="Họ và tên">
          <a-input
            v-model:value="registerForm.name"
            placeholder="Nhập họ và tên"
            size="large"
          >
            <template #prefix>
              <UserOutlined />
            </template>
          </a-input>
        </a-form-item>

        <a-form-item name="email" label="Email">
          <a-input
            v-model:value="registerForm.email"
            placeholder="Nhập email của bạn"
            size="large"
            type="email"
          >
            <template #prefix>
              <MailOutlined />
            </template>
          </a-input>
        </a-form-item>

        <a-form-item name="password" label="Mật khẩu">
          <a-input-password
            v-model:value="registerForm.password"
            placeholder="Nhập mật khẩu"
            size="large"
          >
            <template #prefix>
              <LockOutlined />
            </template>
          </a-input-password>
        </a-form-item>

        <a-form-item name="password_confirmation" label="Xác nhận mật khẩu">
          <a-input-password
            v-model:value="registerForm.password_confirmation"
            placeholder="Nhập lại mật khẩu"
            size="large"
          >
            <template #prefix>
              <LockOutlined />
            </template>
          </a-input-password>
        </a-form-item>

        <a-form-item name="phone" label="Số điện thoại">
          <a-input
            v-model:value="registerForm.phone"
            placeholder="Nhập số điện thoại"
            size="large"
          >
            <template #prefix>
              <PhoneOutlined />
            </template>
          </a-input>
        </a-form-item>

        <a-form-item name="agree">
          <a-checkbox v-model:checked="registerForm.agree">
            Tôi đồng ý với 
            <a href="#" class="terms-link">Điều khoản sử dụng</a>
            và 
            <a href="#" class="terms-link">Chính sách bảo mật</a>
          </a-checkbox>
        </a-form-item>

        <a-form-item>
          <a-button
            type="primary"
            html-type="submit"
            size="large"
            block
            :loading="loading"
          >
            Đăng ký
          </a-button>
        </a-form-item>

        <div class="auth-footer">
          <span>Đã có tài khoản? </span>
          <NuxtLink to="/login" class="auth-link">
            Đăng nhập ngay
          </NuxtLink>
        </div>
      </a-form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { UserOutlined, LockOutlined, MailOutlined, PhoneOutlined } from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

const authStore = useAuthStore();

// SEO
useHead({
  title: 'Đăng ký',
  meta: [
    { name: 'description', content: 'Tạo tài khoản mới' }
  ]
});

// Reactive data
const loading = ref(false)
const registerForm = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  agree: false
});

// Custom validator for password confirmation
const validatePasswordConfirmation = async (rule, value) => {
  if (value && value !== registerForm.password) {
    return Promise.reject('Mật khẩu xác nhận không khớp!')
  }
  return Promise.resolve()
};

// Custom validator for agreement
const validateAgreement = async (rule, value) => {
  if (!value) {
    return Promise.reject('Vui lòng đồng ý với điều khoản sử dụng!')
  }
  return Promise.resolve()
};

// Form validation rules
const registerRules = {
  name: [
    { required: true, message: 'Vui lòng nhập họ và tên!' },
    { min: 2, message: 'Họ tên phải có ít nhất 2 ký tự!' }
  ],
  email: [
    { required: true, message: 'Vui lòng nhập email!' },
    { type: 'email', message: 'Email không hợp lệ!' }
  ],
  password: [
    { required: true, message: 'Vui lòng nhập mật khẩu!' },
    { min: 6, message: 'Mật khẩu phải có ít nhất 6 ký tự!' },
    { pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/, message: 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường và 1 số!' }
  ],
  password_confirmation: [
    { required: true, message: 'Vui lòng xác nhận mật khẩu!' },
    { validator: validatePasswordConfirmation }
  ],
  phone: [
    { required: true, message: 'Vui lòng nhập số điện thoại!' },
    { pattern: /^[0-9]{10,11}$/, message: 'Số điện thoại không hợp lệ!' }
  ],
  agree: [
    { validator: validateAgreement }
  ]
};

// Handle register
const handleRegister = async (values) => {
  loading.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    values.role = 'customer';
    const response = await authStore.register(values);
    
    message.success('Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');
    
    // Redirect to login page
    await navigateTo('/login');
    
  } catch (error) {
    message.error('Đăng ký thất bại. Vui lòng thử lại!');
  } finally {
    loading.value = false
  }
};

definePageMeta({
  layout: 'login'
});
</script>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg,rgb(27, 57, 189) 0%,rgb(55, 76, 105) 100%);
  padding: 20px;
}

.auth-card {
  background: white;
  border-radius: 12px;
  padding: 40px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 450px;
}

.auth-header {
  text-align: center;
  margin-bottom: 30px;
}

.auth-header h1 {
  font-size: 28px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.auth-header p {
  color: #6b7280;
  font-size: 16px;
}

.auth-form {
  margin-top: 20px;
}

.terms-link {
  color: #1890ff;
  text-decoration: none;
}

.terms-link:hover {
  text-decoration: underline;
}

.auth-footer {
  text-align: center;
  margin-top: 20px;
  color: #6b7280;
}

.auth-link {
  color: #1890ff;
  text-decoration: none;
  font-weight: 500;
}

.auth-link:hover {
  text-decoration: underline;
}
</style>