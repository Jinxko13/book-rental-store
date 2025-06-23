// composables/useDashboard.js
export const useDashboard = () => {
    const { $axios } = useNuxtApp();

    const getStatistic = async (params) => {
      try {
        const response = await $axios.get('/api/dashboard/statistics', {params});
        return response.data;
      } catch (error) {
        console.error('Error fetching dashboard statistics:', error);
        throw error;
      }
    }
    
    const getChart = async (params) => {
      try {
        const response = await $axios.get('/api/dashboard/chart', {params});
        return response.data;
      } catch (error) {
        console.error('Error fetching dashboard Chart:', error);
        throw error;
      }
    };
    
    const getRecentActivities = async () => {
      try {
        const response = await $axios.get('/api/dashboard/activities');
        return response.data;
      } catch (error) {
        console.error('Error fetching recent activities:', error);
        throw error;
      }
    };
    
    return {
      getStatistic,
      getChart,
      getRecentActivities
    };
  };
  