<template>
  <div class="profile-page">
    <a-card title="Thông tin cá nhân" class="profile-card">
      <template #extra>
        <a-button type="primary" @click="saveChanges" :loading="loading">Cập nhật thông tin</a-button>
      </template>

      <a-form :model="form" :label-col="{ span: 8 }" :wrapper-col="{ span: 16 }">
        <a-form-item label="Họ và tên">
          <a-input v-model:value="form.name" placeholder="Nhập họ và tên" />
        </a-form-item>

        <a-form-item label="Email">
          <a-input v-model:value="form.email" placeholder="Nhập email" disabled />
        </a-form-item>

        <a-form-item label="Số điện thoại">
          <a-input v-model:value="form.phone" placeholder="Nhập số điện thoại" />
        </a-form-item>

        <a-form-item label="Địa chỉ">
          <a-input v-model:value="form.address" placeholder="Nhập địa chỉ" />
        </a-form-item>

        <a-form-item label="Vai trò">
          <a-tag color="blue">{{ form.role }}</a-tag>
        </a-form-item>

        <a-form-item label="Ngày tạo">
          <a-tag color="green">{{ formattedCreatedAt }}</a-tag>
        </a-form-item>
      </a-form>
    </a-card>

     <!-- Change Password Card -->
     <a-card title="Thay đổi mật khẩu" class="profile-card password-card">
      <template #extra>
        <a-button type="primary" @click="changePassword" :loading="passwordLoading">Thay đổi mật khẩu</a-button>
      </template>
      <a-form :model="passwordForm" :label-col="{ span: 8 }" :wrapper-col="{ span: 16 }" :rules="passwordRules" ref="passwordFormRef">
        <a-form-item label="Mật khẩu hiện tại" name="currentPassword">
          <a-input-password v-model:value="passwordForm.currentPassword" placeholder="Nhập mật khẩu hiện tại" />
        </a-form-item>
        <a-form-item label="Mật khẩu mới" name="newPassword">
          <a-input-password v-model:value="passwordForm.newPassword" placeholder="Nhập mật khẩu mới" />
        </a-form-item>
        <a-form-item label="Xác nhận mật khẩu mới" name="confirmPassword">
          <a-input-password v-model:value="passwordForm.confirmPassword" placeholder="Xác nhận mật khẩu mới" />
        </a-form-item>
      </a-form>
      <a-alert 
        message="Yêu cầu mật khẩu" 
        description="Mật khẩu phải có ít nhất 8 ký tự và chứa ít nhất một chữ hoa, một chữ thường và một số."
        type="info" 
        show-icon 
        class="password-requirements"
      />
    </a-card>
  </div>
</template>

<script setup>
import { reactive, onMounted, computed, ref } from 'vue'
import { message } from 'ant-design-vue'
import { UploadOutlined } from '@ant-design/icons-vue'

const auth = useAuthStore()
const loading = ref(false)
const passwordLoading = ref(false)
const passwordFormRef = ref()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  role: '',
  createdAt: ''
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const formattedCreatedAt = computed(() => {
  if (!form.createdAt) return ''
  const date = new Date(form.createdAt)
  return date.toLocaleDateString()
})

const passwordRules = {
  currentPassword: [
    { required: true, message: 'Vui lòng nhập mật khẩu hiện tại!' }
  ],
  newPassword: [
    { required: true, message: 'Vui lòng nhập mật khẩu mới!' },
    { min: 8, message: 'Mật khẩu phải có ít nhất 8 ký tự!' },
    { 
      pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/, 
      message: 'Mật khẩu phải chứa ít nhất một chữ hoa, một chữ thường và một số!' 
    }
  ],
  confirmPassword: [
    { required: true, message: 'Vui lòng xác nhận mật khẩu mới!' },
    {
      validator: (rule, value) => {
        if (value && value !== passwordForm.newPassword) {
          return Promise.reject('Mật khẩu không khớp!')
        }
        return Promise.resolve()
      }
    }
  ]
}

onMounted(async () => {
  try {
    const userData = auth.user
    form.name = userData.name || ''
    form.email = userData.email || ''
    form.phone = userData.phone || ''
    form.role = userData.role || ''
    form.createdAt = userData.createdAt || userData.created_at || ''
  } catch (error) {
    message.error('Lỗi khi tải thông tin người dùng')
  }
})

const saveChanges = async () => {
  try {
    loading.value = true
    await auth.updateProfile(form)
    message.success('Cập nhật thông tin thành công')
  } catch (error) {
    message.error('Cập nhật thông tin thất bại')
  } finally {
    loading.value = false
  }
}

const changePassword = async () => {
  try {
    // Validate form trước khi submit
    await passwordFormRef.value.validate()
    
    passwordLoading.value = true
    
    // Gọi API đổi mật khẩu
    const response = await auth.changePassword({
      currentPassword: passwordForm.currentPassword,
      newPassword: passwordForm.newPassword
    })

    message.success('Thay đổi mật khẩu thành công')
    
    // Reset form sau khi thành công
    passwordForm.currentPassword = ''
    passwordForm.newPassword = ''
    passwordForm.confirmPassword = ''
    passwordFormRef.value.resetFields()
    
  } catch (error) {
    if (error.errorFields) {
      message.error('Hãy điền hết các trường!')
    }
  } finally {
    passwordLoading.value = false
  }
}

definePageMeta({
  middleware: 'auth'
})
</script>

<style scoped>
.profile-page {
  padding: 24px;
}

.profile-card {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
}

.avatar {
  width: 100%;
  max-width: 100px;
  height: auto;
  border-radius: 4px;
}

.upload-icon {
  font-size: 32px;
  color: #999;
}

.ant-upload-text {
  margin-top: 8px;
  color: #666;
}
</style>

