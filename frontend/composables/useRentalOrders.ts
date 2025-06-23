// composables/useRentalOrders.ts
export const useRentalOrders = () => {
    const { $axios } = useNuxtApp();
    
    const getRentals = async (params = {}) => {
        try {
            const response = await $axios.get('/api/rentals', { params });
            return response.data;
        } catch (error) {
            console.error('Error fetching rental orders:', error);
            throw error;
        }
    };
        
    const getRentalOrderById = async (id) => {
        try {
            const response = await $axios.get(`/api/rental-orders/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error fetching rental order ${id}:`, error);
            throw error;
        }
    };
        
    const createRentalOrder = async (rentalOrderData) => {
        try {
            const response = await $axios.post('/api/rentals', rentalOrderData);
            return response.data;
        } catch (error) {
            console.error('Error creating rental order:', error);
            throw error;
        }
    };
        
    const updateRentalOrder = async (id, rentalOrderData) => {
        try {
            const response = await $axios.put(`/api/rental-orders/${id}`, rentalOrderData);
            return response.data;
        } catch (error) {
            console.error(`Error updating rental order ${id}:`, error);
            throw error;
        }
    };
        

    const approveRentalOrder = async (id) => {
        try {
          const response = await $axios.put(`/api/rental-orders/${id}/approve`);
          return response.data;
        } catch (error) {
          console.error(`Error approving rental order ${id}:`, error);
          throw error;
        }
    };
    
    const rejectRentalOrder = async (id, reason) => {
        try {
            const response = await $axios.put(`/api/rental-orders/${id}/reject`, { reason });
            return response.data;
        } catch (error) {
            console.error(`Error rejecting rental order ${id}:`, error);
            throw error;
        }
    };
    
    const returnRentalOrder = async (returnData) => {
        try {
            const response = await $axios.post(`/api/rental/return/${returnData.rental_id}`, returnData);
            return response;
        } catch (error) {
            console.error(`Error returning rental order ${returnData.rental_id}:`, error);
            throw error;
        }
    };

    const extendRental = async (id, extendData) => {
        try {
            const response = await $axios.post(`/api/rental/extend/${id}`, {
                extend_days: extendData
            });
            return response.data;
        } catch (error) {
            console.error(`Error extending due date for rental order ${id}:`, error);
            throw error;
        }
    };

    const getOverdueOrders = async (params = {}) => {
        try {
            const response = await $axios.get('/api/rentals/overdue', { params });
            return response.data;
        } catch (error) {
            console.error('Error fetching overdue orders:', error);
            throw error;
    }
    };

    const notifyRenter = async (id) => {
        try {
            const response = await $axios.post(`/api/rental/notify/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error notifying renter for rental order ${id}:`, error);
            throw error;
        }
    };

    const calculateRentalFee = async (id) => {
        try {
            const response = await $axios.get(`/api/rental/fee/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error calculating rental fee for rental order ${id}:`, error);
            throw error;
        }
    };

    const deleteRentalOrder = async (id) => {
        try {
            const response = await $axios.delete(`/api/rentals/${id}`);
            return response.data;
        } catch (error) {
            console.error(`Error deleting rental order ${id}:`, error);
            throw error;
        }
    };
    
    const getUserRentals = async (params = {}) => {
        try {
            const response = await $axios.get('/api/user/rentals', { params });
            return response.data;
        } catch (error) {
            console.error('Error fetching user rentals:', error);
            throw error;
        }
    };

    const previewReturnCost = async (returnData) => {
        try {
            const response = await $axios.post(`/api/rental/pre-return/${returnData.rental_id}`, returnData);
            return response.data;
        } catch (error) {
            console.error(`Error previewing return cost for rental order ${returnData.rental_id}:`, error);
            throw error;
        }
    };

    const cancelRental = async (id) => {
        try {
            const response = await $axios.put(`/api/rental/cancel/${id}`)
            return response.data;

        } catch (error) {
            console.error('Error fetching user rentals:', error);
            throw error;

        }
    }
    
    return {
        getRentals,
        getRentalOrderById,
        createRentalOrder,
        updateRentalOrder,
        deleteRentalOrder,
        approveRentalOrder,
        rejectRentalOrder,
        returnRentalOrder,
        extendRental,
        getOverdueOrders,
        getUserRentals,
        previewReturnCost,
        cancelRental
    };
};