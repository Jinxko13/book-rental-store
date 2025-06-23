// composables/useCategories.ts
export const useCategories = () => {
    const { $axios } = useNuxtApp();
    
    const getCategories = async (params = {}) => {
        try {
            const response = await $axios.get('/api/categories', { params });
            return response.data;
        } catch (error) {
            console.error('Error fetching categories:', error);
            throw error;
        }
    };

    const getCategoryById = async (id) => {
        try {
            const response = await $axios.get(`/api/categories/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error fetching category ${id}:`, error);
            throw error;
        }
    };
    
    const createCategory = async (categoryData) => {
        try {
            const response = await $axios.post('/api/categories', categoryData);
            return response.data;
        } catch (error) {
            console.error('Error creating category:', error);
            throw error;
        }
    };

    const updateCategory = async (id, categoryData) => {
        try {
            const response = await $axios.put(`/api/categories/${id}`, categoryData);
            return response.data;
        } catch (error) {
            console.error(`Error updating category ${id}:`, error);
            throw error;
        }
    };

    const deleteCategory = async (id) => {
        try {
            const response = await $axios.delete(`/api/categories/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error deleting category ${id}:`, error);
            throw error;
        }
    };

    return {
        getCategories,
        getCategoryById,
        createCategory,
        updateCategory,
        deleteCategory
    };
};