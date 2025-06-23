import { notification } from 'ant-design-vue';

interface User {
  name: string
  email: string
  avatar?: string
  role: string
  created_at: string
  updated_at: string
}

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(
    useCookie('auth_user').value || null
  );
  const token = ref(
    useCookie('auth_token').value || null
  );
  const loginError = ref(null);
  const sessionExpiry = ref(null);
  const rememberMe = ref(false);

  // Permissions & Roles
  const isLoading = ref(false);
  const isAuthenticated = computed(() => !!user.value && !!token.value);
  const isSessionExpired = computed(() => sessionExpiry.value && sessionExpiry.value < new Date());

  const { $axios } = useNuxtApp();

  watch(
    () => useCookie('auth_user').value,
    (newValue) => {
      user.value = newValue || null;
    }
  );

  watch(
    () => useCookie('auth_token').value,
    (newValue) => {
      token.value = newValue || null;
    }
  );
  
  // Actions
  const login = async (credentials) => {
    try {
      isLoading.value = true;
      loginError.value = null;
      
      const response = await $axios.post('/api/auth/login', {
        ...credentials,
        remember_me: rememberMe.value
      });
      
      const { user: userData, token: userToken, message: message} = response.data;
      
      // Set state
      user.value = userData;
      token.value = userToken;
      
      // Calculate session expiry
      const expiryHours = rememberMe.value ? 24 * 7 : 24; // 7 days or 1 day
      sessionExpiry.value = new Date(Date.now() + expiryHours * 60 * 60 * 1000);
      
      // Store in cookie with appropriate expiry
      const tokenCookie = useCookie('auth_token', {
        default: () => '',
        maxAge: rememberMe.value ? 60 * 60 * 24 * 7 : 60 * 60 * 24,
        httpOnly: false,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'strict'
      });
      tokenCookie.value = userToken;
      
      // Store user data
      const userCookie = useCookie('auth_user', {
        default: () => null,
        maxAge: rememberMe.value ? 60 * 60 * 24 * 7 : 60 * 60 * 24,
        httpOnly: false,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'strict'
      });
      userCookie.value = userData;
      
      notification.success({
        message: message,
      });
      return response.data;
    } catch (error) {
      loginError.value = error.response?.data?.message || 'Login failed';
      notification.error({
        message: error.response?.data?.message || 'Login failed',
      });
      console.log(error)
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async (force = false) => {
    try {
      if (!force && token.value) {
        await $axios.post('/api/auth/logout');
      }
    } catch (error) {
      notification.error({
        message: error.response?.data?.message || 'Logout failed',
      });
    } finally {
      user.value = null;
      token.value = null;
      sessionExpiry.value = null;
      loginError.value = null;
      
      useCookie('auth_token').value = '';
      useCookie('auth_user').value = null;
      
      await navigateTo('/');
    }
  };

  const register = async (credentials) => {
    try {
      isLoading.value = true;
      loginError.value = null;
      
      const response = await $axios.post('/api/auth/register', credentials);
      
      const { user: userData, token: userToken, message: message} = response.data;
      
      // Set state
      user.value = userData;
      token.value = userToken;
      
      // Calculate session expiry
      const expiryHours = rememberMe.value ? 24 * 7 : 24; // 7 days or 1 day
      sessionExpiry.value = new Date(Date.now() + expiryHours * 60 * 60 * 1000);
      
      // Store in cookie with appropriate expiry
      const tokenCookie = useCookie('auth_token', {
        default: () => '',
        maxAge: rememberMe.value ? 60 * 60 * 24 * 7 : 60 * 60 * 24,
        httpOnly: false,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'strict'
      });
      tokenCookie.value = userToken;
      
      // Store user data
      const userCookie = useCookie('auth_user', {
        default: () => null,
        maxAge: rememberMe.value ? 60 * 60 * 24 * 7 : 60 * 60 * 24,
        httpOnly: false,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'strict'
      });
      userCookie.value = userData;
      
      notification.success({
        message: message,
      });
      return response.data;
    } catch (error) {
      loginError.value = error.response?.data?.message || 'Register failed';
      notification.error({
        message: error.response?.data?.message || 'Register failed',
      });
      console.log(error)
      throw error;
    } finally {
      isLoading.value = false;
    }
  };
  
  const checkAuth = async () => {
    const tokenCookie = useCookie('auth_token');
    const userCookie = useCookie('auth_user');
    if (tokenCookie.value && userCookie.value) {
      try {
        const response = await $axios.get('/api/auth/me');
      
        const { user: userData, token: userToken, message: message} = response.data;
        
        user.value = userData;
        token.value = userToken;
        
        const userCookie = useCookie('auth_user');
        userCookie.value = userData;

        return true;
      } catch (error) {
        return false;
      }
    }
    await logout(true);
    return false;
  };
  
  const updateProfile = async (profileData) => {
    try {
      const response = await $axios.put('/api/auth/me', profileData);
      user.value = response.data.user;
      return response.data;
    } catch (error) {
      notification.error({
        message: error.response.data.message,
      });
      throw error;
    }
  };

  const changePassword = async (passwordData) => {
    try {
      const response = await $axios.put('/api/auth/change-password', passwordData);
      return response.data;
    } catch (error) {
      notification.error({
        message: error.response.data.message,
      });
      throw error;
    }
  };

  return {
    // State
    user: readonly(user),
    token: readonly(token),
    isLoading: readonly(isLoading),
    loginError: readonly(loginError),
    sessionExpiry: readonly(sessionExpiry),
    isAuthenticated,
    isSessionExpired,

    // Actions
    login,
    logout,
    checkAuth,
    updateProfile,
    changePassword,
    register,
    // setRememberMe,
  };
})
