// composables/useFavorites.ts
export const useFavorites = () => {
    const { $axios } = useNuxtApp();
    
    const getFavorites = async (params = {}) => {
        try {
            const response = await $axios.get('/api/favorites', { params });
            return response.data;
        } catch (error) {
            console.error('Error fetching favorites:', error);
            throw error;
        }
    };
        
    const getFavoriteById = async (id) => {
        try {
            const response = await $axios.get(`/api/favorites/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error fetching favorite ${id}:`, error);
            throw error;
        }
    };
        
    const createFavorite = async (favoriteData) => {
        try {
            const response = await $axios.post('/api/favorites', favoriteData);
            return response.data;
        } catch (error) {
            console.error('Error creating favorite:', error);
            throw error;
        }
    };
        
    const updateFavorite = async (id, favoriteData) => {
        try {
            const response = await $axios.put(`/api/favorites/${id}`, favoriteData);
            return response.data;
        } catch (error) {
            console.error(`Error updating favorite ${id}:`, error);
            throw error;
        }
    };
        
    const deleteFavorite = async (id) => {
        try {
            const response = await $axios.delete(`/api/favorites/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error deleting favorite ${id}:`, error);
            throw error;
        }
    };
    
    const getFavoritesByUser = async (userId, params = {}) => {
        try {
            const response = await $axios.get(`/api/favorites/${userId}/favorites`, { params });
            return response.data;
        } catch (error) {
            console.error(`Error fetching favorites for user ${userId}:`, error);
            throw error;
        }
    };
    
    return {
        getFavorites,
        getFavoriteById,
        createFavorite,
        updateFavorite,
        deleteFavorite,
        getFavoritesByUser
    };
};