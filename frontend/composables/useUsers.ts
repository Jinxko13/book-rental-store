// composables/useUsers.ts
export const useUsers = () => {
    const { $axios } = useNuxtApp();
    
    const getUsers = async (params = {}) => {
        try {
            const response = await $axios.get('/api/users', { params });
            return response.data;
        } catch (error) {
            console.error('Error fetching users:', error);
            throw error;
        }
    };
        
    const getUserById = async (id) => {
        try {
            const response = await $axios.get(`/api/users/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error fetching user ${id}:`, error);
            throw error;
        }
    };
        
    const createUser = async (userData) => {
        try {
            const response = await $axios.post('/api/users', userData);
            return response.data;
        } catch (error) {
            console.error('Error creating user:', error);
            throw error;
        }
    };
        
    const updateUser = async (id, userData) => {
        try {
            const response = await $axios.put(`/api/users/${id}`, userData);
            return response.data;
        } catch (error) {
            console.error(`Error updating user ${id}:`, error);
            throw error;
        }
    };
        
    const deleteUser = async (id) => {
        try {
            const response = await $axios.delete(`/api/users/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error deleting user ${id}:`, error);
            throw error;
        }
    };
    
    const getUsersByRole = async (role, params = {}) => {
        try {
            const response = await $axios.get(`/api/users/${role}/users`, { params });
            return response.data;
        } catch (error) {
            console.error(`Error fetching users for role ${role}:`, error);
            throw error;
        }
    };
    
    return {
        getUsers,
        getUserById,
        createUser,
        updateUser,
        deleteUser,
        getUsersByRole
    };
};
        