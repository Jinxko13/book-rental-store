// composables/useProfile.js
export const useProfile = () => {
    const $axios = useAxios();
    
    const getProfile = async () => {
      try {
        const response = await $axios.get('/api/profile');
        return response.data;
      } catch (error) {
        console.error('Error fetching profile:', error);
        throw error;
      }
    };
    
    const updateProfile = async (profileData) => {
      try {
        const response = await $axios.put('/api/profile', profileData);
        return response.data;
      } catch (error) {
        console.error('Error updating profile:', error);
        throw error;
      }
    };
    
    const changePassword = async (passwordData) => {
      try {
        const response = await $axios.put('/api/profile/change-password', passwordData);
        return response.data;
      } catch (error) {
        console.error('Error changing password:', error);
        throw error;
      }
    };
    
    const uploadAvatar = async (formData) => {
      try {
        const response = await $axios.post('/api/profile/avatar', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        return response.data;
      } catch (error) {
        console.error('Error uploading avatar:', error);
        throw error;
      }
    };
    
    return {
      getProfile,
      updateProfile,
      changePassword,
      uploadAvatar
    };
  };