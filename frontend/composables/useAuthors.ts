// composables/useAuthors.ts
export const useAuthors = () => {
    const { $axios } = useNuxtApp();
    
    const getAuthors = async (params = {}) => {
        try {
            const response = await $axios.get('/api/authors', { params });
            return response.data;
        } catch (error) {
            console.error('Error fetching authors:', error);
            throw error;
        }
    };
    
    const getAuthorById = async (id) => {
        try {
            const response = await $axios.get(`/api/authors/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error fetching author ${id}:`, error);
            throw error;
        }
    };
    
    const createAuthor = async (authorData) => {
        try {
            const response = await $axios.post('/api/authors', authorData);
            return response.data;
        } catch (error) {
            console.error('Error creating author:', error);
            throw error;
        }
    };
    
    const updateAuthor = async (id, authorData) => {
        try {
            const response = await $axios.put(`/api/authors/${id}`, authorData);
            return response.data;
        } catch (error) {
            console.error(`Error updating author ${id}:`, error);
            throw error;
        }
    };
    
    const deleteAuthor = async (id) => {
        try {
            const response = await $axios.delete(`/api/authors/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error deleting author ${id}:`, error);
            throw error;
        }
    };
    
    return {
        getAuthors,
        getAuthorById,
        createAuthor,
        updateAuthor,
        deleteAuthor
    };
};