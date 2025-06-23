# pages/auth/login.vue
<template>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Đăng nhập</h1>
        <p>Chào mừng bạn quay lại!</p>
      </div>
      
      <a-form
        :model="loginForm"
        :rules="loginRules"
        @finish="handleLogin"
        layout="vertical"
        class="auth-form"
      >
        <a-form-item name="email" label="Email">
          <a-input
            v-model:value="loginForm.email"
            placeholder="Nhập email của bạn"
            size="large"
            type="email"
          >
            <template #prefix>
              <UserOutlined />
            </template>
          </a-input>
        </a-form-item>

        <a-form-item name="password" label="Mật khẩu">
          <a-input-password
            v-model:value="loginForm.password"
            placeholder="Nhập mật khẩu"
            size="large"
          >
            <template #prefix>
              <LockOutlined />
            </template>
          </a-input-password>
        </a-form-item>

        <a-form-item>
          <div class="form-options">
            <a-checkbox v-model:checked="loginForm.remember">
              Ghi nhớ đăng nhập
            </a-checkbox>
            <a-button type="link" class="forgot-password">
              Quên mật khẩu?
            </a-button>
          </div>
        </a-form-item>

        <a-form-item>
          <a-button
            type="primary"
            html-type="submit"
            size="large"
            block
            :loading="loading"
          >
            Đăng nhập
          </a-button>
        </a-form-item>

        <div class="auth-footer">
          <span>Chưa có tài khoản? </span>
          <NuxtLink to="/register" class="auth-link">
            Đăng ký ngay
          </NuxtLink>
        </div>
      </a-form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { UserOutlined, LockOutlined } from '@ant-design/icons-vue';

const auth = useAuthStore();
const router = useRouter();

// SEO
useHead({
  title: 'Đăng nhập',
  meta: [
    { name: 'description', content: 'Đăng nhập vào tài khoản của bạn' }
  ]
});

// Reactive data
const loading = ref(false);

const loginForm = reactive({
  email: '',
  password: '',
  remember: false
});

// Form validation rules
const loginRules = {
  email: [
    { required: true, message: 'Vui lòng nhập email!' },
    { type: 'email', message: 'Email không hợp lệ!' }
  ],
  password: [
    { required: true, message: 'Vui lòng nhập mật khẩu!' },
    { min: 6, message: 'Mật khẩu phải có ít nhất 6 ký tự!' }
  ]
};

// Handle login
const handleLogin = async (values) => {
  loading.value = true;
  
    try {
      const data = await auth.login(values);
    if (data.user.role === 'admin' || data.user.role === 'staff') {
      await navigateTo('/admin');
    } else {
      await navigateTo('/');
    }
    
  } catch (error) {
    console.error('Đăng nhập thất bại.', error);
  } finally {
    loading.value = false;
  }

}

definePageMeta({
  layout: 'login',
  middleware: 'login'
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
  max-width: 400px;
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

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot-password {
  padding: 0;
  color: #1890ff;
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