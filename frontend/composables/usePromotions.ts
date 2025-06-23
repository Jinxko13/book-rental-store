// composables/usePromotions.js
export const usePromotions = () => {
    const { $axios } = useNuxtApp();
    
    const getPromotions = async (params = {}) => {
      try {
        const response = await $axios.get('/api/discounts', { params });
        return response.data;
      } catch (error) {
        console.error('Error fetching promotions:', error);
        throw error;
      }
    };

    const applyPromotions = async (code) => {
      try {
        const response = await $axios.get(`/api/discounts/apply/${code}`);
        return response.data;
      } catch (error) {
        console.error(`Error applying promotion ${code}:`, error);
        throw error;
      }
    };
    
    const getPromotionById = async (id) => {
      try {
        const response = await $axios.get(`/api/discounts/${id}`);
        return response.data;
      } catch (error) {
        console.error(`Error fetching promotion ${id}:`, error);
        throw error;
      }
    };
    
    const createPromotion = async (promotionData) => {
      try {
        const response = await $axios.post('/api/discounts', promotionData);
        return response.data;
      } catch (error) {
        console.error('Error creating promotion:', error);
        throw error;
      }
    };
    
    const updatePromotion = async (id, promotionData) => {
      try {
        const response = await $axios.put(`/api/discounts/${id}`, promotionData);
        return response.data;
      } catch (error) {
        console.error(`Error updating promotion ${id}:`, error);
        throw error;
      }
    };
    
    const deletePromotion = async (id) => {
      try {
        const response = await $axios.delete(`/api/discounts/${id}`);
        return response.data;
      } catch (error) {
        console.error(`Error deleting promotion ${id}:`, error);
        throw error;
      }
    };
    
    const activatePromotion = async (id) => {
      try {
        const response = await $axios.put(`/api/discounts/${id}/activate`);
        return response.data;
      } catch (error) {
        console.error(`Error activating promotion ${id}:`, error);
        throw error;
      }
    };
    
    const deactivatePromotion = async (id) => {
      try {
        const response = await $axios.put(`/api/discounts/${id}/deactivate`);
        return response.data;
      } catch (error) {
        console.error(`Error deactivating promotion ${id}:`, error);
        throw error;
      }
    };
    
    return {
      getPromotions,
      getPromotionById,
      createPromotion,
      updatePromotion,
      deletePromotion,
      activatePromotion,
      deactivatePromotion,
      applyPromotions
    };
  };