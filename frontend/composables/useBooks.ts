// composables/useBooks.ts
export const useBooks = () => {
    const { $axios } = useNuxtApp();

    const getBooks = async (params = {}) => {
        try {
            const response = await $axios.get('/api/books', { params });
            return response.data;
        } catch (error) {
            console.error('Error fetching books:', error);
            throw error;
        }
    };

    const getBookById = async (id) => {
        try {
          const response = await $axios.get(`/api/books/${id}`);
          return response.data;
        } catch (error) {
          console.error(`Error fetching book ${id}:`, error);
          throw error;
        }
    };
    
    const createBook = async (bookData) => {
        try {
          const response = await $axios.post('/api/books', bookData, {
            headers: {
              'Content-Type': 'multipart/form-data', 
            },
          });
          return response.data;
        } catch (error) {
          console.error('Error creating book:', error);
          throw error;
        }
    };
    
    const updateBook = async (id, bookData) => {
        try {
          const response = await $axios.post(`/api/book/${id}`, bookData, {
            headers: {
              'Content-Type': 'multipart/form-data', 
            },
          });
          return response.data;
        } catch (error) {
          console.error(`Error updating book ${id}:`, error);
          throw error;
        }
    };

    const deleteBook = async (id) => {
        try {
          const response = await $axios.delete(`/api/books/${id}`);
          return response.data;
        } catch (error) {
          console.error(`Error deleting book ${id}:`, error);
          throw error;
        }
    };

    const getMonthlyBook = async (params) => {
        try {
          if(!params){
            params = {
              month: new Date().getMonth() + 1,
              year: new Date().getFullYear()
            }
          }
          const response = await $axios.get('/api/book/monthly', { params });
          return response.data;
        } catch (error) {
          console.error('Error fetching monthly books:', error);
          throw error;
        }
      };

      const getWeeklyBook = async (params) => {
        try {
          const response = await $axios.get('/api/top-book/weekly', { params });
          return response.data;
        } catch (error) {
          console.error('Error fetching weekly books:', error);
          throw error;
        }
      };

      const getNewestBook = async () => {
        try {
          const response = await $axios.get('/api/book/newest');
          return response.data;
        } catch (error) {
          console.error('Error fetching newest books:', error);
          throw error;
        }
      };

    return {
        getBooks,
        getBookById,
        createBook,
        updateBook,
        deleteBook,
        getMonthlyBook,
        getWeeklyBook,
        getNewestBook
    };
};
    