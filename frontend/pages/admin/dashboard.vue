<!-- pages/admin/dashboard.vue -->
<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold">Dashboard</h2>
      <p class="text-gray-500">Xin chào, chào mừng trở lại với trang quản trị!</p>
    </div>
    <!-- Filters -->
    <a-card class="mb-6">
      <div class="flex gap-4">
        <a-select v-model:value="timeRange" style="width: 180px" @change="handleTimeRangeChange">
          <a-select-option value="week">7 ngày qua</a-select-option>
          <a-select-option value="month">30 ngày qua</a-select-option>
          <a-select-option value="quarter">Quý này</a-select-option>
          <a-select-option value="year">Năm nay</a-select-option>
        </a-select>
        <a-button type="primary" @click="refreshData" :loading="loading">
          <template #icon>
            <ReloadOutlined />
          </template>
          Làm mới dữ liệu
        </a-button>
      </div>
    </a-card>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <!-- Doanh thu -->
      <a-card>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 mb-1">Doanh thu hiện tại</p>
            <h3 class="text-2xl font-bold">{{ formatCurrency(stats.revenue.revenueNow) }}</h3>
            <p class="text-sm" :class="stats.revenue.increase >= 0 ? 'text-green-500' : 'text-red-500'">
              <span v-if="stats.revenue.increase >= 0">
                <ArrowUpOutlined /> +{{ formatPercent(stats.revenue.increase) }}%
              </span>
              <span v-else>
                <ArrowDownOutlined /> {{ formatPercent(stats.revenue.increase) }}%
              </span>
              so với kỳ trước
            </p>
          </div>
          <div class="bg-blue-50 p-3 rounded-full">
            <DollarOutlined style="font-size: 24px; color: #1890ff" />
          </div>
        </div>
      </a-card>
      <!-- Tổng số sách -->
      <a-card>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 mb-1">Tổng số sách trong kho</p>
            <h3 class="text-2xl font-bold">{{ stats.book.totalBook }}</h3>
            <p class="text-sm">
              <span class="text-blue-500">{{ stats.book.numberOfRental }}</span> đang cho thuê
            </p>
          </div>
          <div class="bg-green-50 p-3 rounded-full">
            <BookOutlined style="font-size: 24px; color: #52c41a" />
          </div>
        </div>
      </a-card>
      <!-- Khách hàng -->
      <a-card>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 mb-1">Tổng số khách hàng</p>
            <h3 class="text-2xl font-bold">{{ stats.user.total }}</h3>
            <p class="text-sm">
              <span class="text-green-500">+{{ stats.user.increase }}</span> khách hàng mới
            </p>
          </div>
          <div class="bg-purple-50 p-3 rounded-full">
            <UserOutlined style="font-size: 24px; color: #722ed1" />
          </div>
        </div>
      </a-card>
      <!-- Lượt thuê -->
      <a-card>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 mb-1">Số lượt thuê</p>
            <h3 class="text-2xl font-bold">{{ stats.rental.total }}</h3>
            <p class="text-sm" :class="stats.rental.increase >= 0 ? 'text-green-500' : 'text-red-500'">
              <span v-if="stats.rental.increase >= 0">
                <ArrowUpOutlined /> +{{ formatPercent(stats.rental.increase) }}%
              </span>
              <span v-else>
                <ArrowDownOutlined /> {{ formatPercent(stats.rental.increase) }}%
              </span>
              so với kỳ trước
            </p>
          </div>
          <div class="bg-orange-50 p-3 rounded-full">
            <ShoppingOutlined style="font-size: 24px; color: #fa8c16" />
          </div>
        </div>
      </a-card>
    </div>
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Revenue Chart -->
      <a-card title="Biểu đồ doanh thu" :loading="chartLoading">
        <div class="flex items-center justify-end mb-2">
          <a-radio-group v-model:value="revenueChartType" button-style="solid" @change="updateRevenueChart">
            <a-radio-button value="week">Tuần</a-radio-button>
            <a-radio-button value="month">Tháng</a-radio-button>
            <a-radio-button value="quarter">Quý</a-radio-button>
            <a-radio-button value="year">Năm</a-radio-button>
          </a-radio-group>
        </div>
        <div ref="revenueChartContainer" style="height: 300px; width: 100%;"></div>
      </a-card>
      <!-- Top Books Chart -->
      <a-card title="Top 5 sách được thuê nhiều nhất" :loading="chartLoading">
        <div ref="topBooksChartContainer" style="height: 300px; width: 100%;"></div>
      </a-card>
    </div>
    <!-- Recent Activity & Quick Links -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Activity -->
      <a-card title="Hoạt động gần đây" class="lg:col-span-2" :loading="loading">
        <a-list :data-source="recentActivities" :pagination="{ pageSize: 5 }">
          <template #renderItem="{ item }">
            <a-list-item>
              <a-list-item-meta>
                <template #avatar>
                  <a-avatar :style="{ backgroundColor: item.color }">
                    <template #icon>
                      <component :is="item.icon" />
                    </template>
                  </a-avatar>
                </template>
                <template #title>{{ item.title }}</template>
                <template #description>
                  <div class="flex justify-between">
                    <span>{{ item.description }}</span>
                    <span class="text-gray-400">{{ item.time }}</span>
                  </div>
                </template>
              </a-list-item-meta>
            </a-list-item>
          </template>
        </a-list>
      </a-card>
      <!-- Quick Links -->
      <a-card title="Liên kết nhanh">
        <div class="grid grid-cols-2 gap-4">
          <a-button block @click="navigateTo('/admin/books')">
            <template #icon>
              <BookOutlined />
            </template>
            Quản lý sách
          </a-button>
          <a-button block @click="navigateTo('/admin/user-groups')">
            <template #icon>
              <UserOutlined />
            </template>
            Khách hàng
          </a-button>
          <a-button block @click="navigateTo('/admin/rentals')">
            <template #icon>
              <ShoppingOutlined />
            </template>
            Đơn thuê
          </a-button>
          <a-button block @click="navigateTo('/admin/categories')">
            <template #icon>
              <AppstoreOutlined />
            </template>
            Danh mục
          </a-button>
          <a-button block @click="navigateTo('/admin/authors')">
            <template #icon>
              <TeamOutlined />
            </template>
            Tác giả
          </a-button>
          <a-button block @click="navigateTo('/admin/reports')">
            <template #icon>
              <BarChartOutlined />
            </template>
            Báo cáo
          </a-button>
        </div>
      </a-card>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick, onBeforeUnmount } from 'vue';
import { message } from 'ant-design-vue';
import * as echarts from 'echarts/core';
import {
  BarChart,
  LineChart,
  PieChart
} from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent
} from 'echarts/components';
import { CanvasRenderer } from 'echarts/renderers';
import dayjs from 'dayjs';
import {
  ReloadOutlined,
  DollarOutlined,
  BookOutlined,
  UserOutlined,
  ShoppingOutlined,
  ArrowUpOutlined,
  ArrowDownOutlined,
  TeamOutlined,
  AppstoreOutlined,
  BarChartOutlined,
  CheckCircleOutlined,
  ClockCircleOutlined,
  ExclamationCircleOutlined
} from '@ant-design/icons-vue';

const dashboardApi = useDashboard();

// Register ECharts components
echarts.use([
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent,
  BarChart,
  LineChart,
  PieChart,
  CanvasRenderer
]);

const loading = ref(false);
const chartLoading = ref(false);
const timeRange = ref('month');
const revenueChartType = ref('month');
const revenueChartContainer = ref(null);
const topBooksChartContainer = ref(null);

let revenueChart = null;
let topBooksChart = null;

// Stats data structure matching API response
const stats = reactive({
  book: {
    totalBook: 0,
    numberOfRental: 0
  },
  revenue: {
    revenueNow: 0,
    increase: 0
  },
  user: {
    total: 0,
    increase: 0
  },
  rental: {
    total: 0,
    increase: 0
  }
});

// Chart data
const revenueChartData = ref({
  labels: [],
  current: [],
  previous: []
});

const topBooksData = ref([]);

// Mock recent activities
const recentActivities = ref([
  {
    title: 'Đơn thuê mới',
    description: 'Khách hàng Nguyễn Văn A đã thuê 3 cuốn sách',
    time: '1 giờ trước',
    icon: 'ShoppingOutlined',
    color: '#1890ff'
  },
  {
    title: 'Sách đã được trả',
    description: 'Khách hàng Trần Thị B đã trả 2 cuốn sách',
    time: '3 giờ trước',
    icon: 'CheckCircleOutlined',
    color: '#52c41a'
  },
  {
    title: 'Đơn thuê sắp hết hạn',
    description: '5 đơn thuê sẽ hết hạn trong vòng 2 ngày tới',
    time: '5 giờ trước',
    icon: 'ClockCircleOutlined',
    color: '#faad14'
  },
  {
    title: 'Đơn thuê quá hạn',
    description: '3 đơn thuê đã quá hạn cần xử lý',
    time: '1 ngày trước',
    icon: 'ExclamationCircleOutlined',
    color: '#f5222d'
  },
  {
    title: 'Sách mới được thêm',
    description: '10 cuốn sách mới đã được thêm vào kho',
    time: '2 ngày trước',
    icon: 'BookOutlined',
    color: '#722ed1'
  },
  {
    title: 'Khách hàng mới đăng ký',
    description: '5 khách hàng mới đã đăng ký tài khoản',
    time: '3 ngày trước',
    icon: 'UserOutlined',
    color: '#13c2c2'
  }
]);

onMounted(async () => {
  await fetchData();
  await nextTick();
  setTimeout(() => {
    fetchRevenueChartData();
    fetchTopBooksData();
  }, 100);
});

onBeforeUnmount(() => {
  // Cleanup charts
  if (revenueChart) {
    revenueChart.dispose();
    revenueChart = null;
  }
  if (topBooksChart) {
    topBooksChart.dispose();
    topBooksChart = null;
  }
});

// API Calls
async function fetchData() {
  loading.value = true;
  try {
    const params = {
      range: timeRange.value
    }
    const response = await dashboardApi.getStatistic(params);
    if (response.data) {
      Object.assign(stats, response.data);
    } else {
      message.error('Không thể tải dữ liệu dashboard');
    }
  } catch (error) {
    console.error('Dashboard API Error:', error);
    message.error('Lỗi kết nối API dashboard');
  } finally {
    loading.value = false;
  }
}

async function fetchRevenueChartData() {
  chartLoading.value = true;
  try {
    const params = {
      range: revenueChartType.value
    };
    const response = await dashboardApi.getChart(params);
    if (response.data) {
      revenueChartData.value = response.data;

      await nextTick();
      setTimeout(() => {
        initRevenueChart();
      }, 50);
    } else {
      message.error('Không thể tải dữ liệu biểu đồ');
    }
  } catch (error) {
    console.error('Revenue Chart API Error:', error);
    message.error('Lỗi kết nối API biểu đồ');
  } finally {
    chartLoading.value = false;
  }
}

async function fetchTopBooksData() {
  try {
    const mockData = [
      { name: 'Đắc Nhân Tâm', value: 128 },
      { name: 'Nhà Giả Kim', value: 115 },
      { name: 'Hạt Giống Tâm Hồn', value: 96 },
      { name: 'Cà Phê Cùng Tony', value: 84 },
      { name: 'Tuổi Trẻ Đáng Giá Bao Nhiêu', value: 72 }
    ];

    topBooksData.value = mockData;
    await nextTick();
    setTimeout(() => {
      initTopBooksChart();
    }, 50);
  } catch (error) {
    console.error('Top Books API Error:', error);
  }
}

function handleTimeRangeChange() {
  fetchData();
  // Nếu chart range khác với time range, cập nhật chart
  if (revenueChartType.value !== timeRange.value) {
    revenueChartType.value = timeRange.value;
    fetchRevenueChartData();
  }
}

function refreshData() {
  fetchData();
  fetchRevenueChartData();
  fetchTopBooksData();
}
watch(revenueChartType.value, fetchRevenueChartData);


function updateRevenueChart() {
  fetchRevenueChartData();
}

function formatCurrency(value) {
  if (!value) return '0 ₫';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' })
    .format(value)
    .replace(/\s/g, '');
}

function formatPercent(value) {
  if (!value) return '0';
  return Math.abs(value).toFixed(1);
}

function initRevenueChart() {
  if (!revenueChartContainer.value) {
    console.log('Revenue chart container not found');
    return;
  }


  if (!revenueChartData.value.labels || !revenueChartData.value.labels.length) {
    console.log('No chart data available');
    return;
  }

  console.log('Initializing revenue chart with data:', revenueChartData.value);

  try {
    // Dispose existing chart
    if (revenueChart) {
      revenueChart.dispose();
    }

    revenueChart = echarts.init(revenueChartContainer.value);

    const option = {
      tooltip: {
        trigger: 'axis',
        formatter: function (params) {
          let result = `${params[0].name}<br/>`;
          params.forEach(param => {
            result += `${param.seriesName}: ${formatCurrency(param.value)}<br/>`;
          });
          return result;
        }
      },
      legend: {
        data: revenueChartType.value === 'year' ? ['Doanh thu'] : ['Năm nay', 'Năm trước'],
        bottom: 0
      },
      grid: {
        left: '3%',
        right: '4%',
        bottom: '60px',
        top: '3%',
        containLabel: true
      },
      xAxis: {
        type: 'category',
        data: revenueChartData.value.labels,
        axisLabel: {
          interval: 0,
          rotate: revenueChartData.value.labels.length > 7 ? 45 : 0
        }
      },
      yAxis: {
        type: 'value',
        axisLabel: {
          formatter: function (value) {
            if (value >= 1000000) {
              return (value / 1000000).toFixed(0) + 'M';
            }
            return (value / 1000).toFixed(0) + 'K';
          }
        }
      },
      series: revenueChartType.value === 'year'
        ? [
          {
            name: 'Doanh thu',
            type: 'bar',
            data: revenueChartData.value.current,
            itemStyle: {
              color: '#1890ff'
            }
          }
        ]
        : [
          {
            name: 'Năm nay',
            type: 'line',
            data: revenueChartData.value.current,
            itemStyle: {
              color: '#1890ff'
            },
            lineStyle: {
              width: 3
            },
            symbol: 'circle',
            symbolSize: 8
          },
          {
            name: 'Năm trước',
            type: 'line',
            data: revenueChartData.value.previous || [],
            itemStyle: {
              color: '#bfbfbf'
            },
            lineStyle: {
              width: 2,
              type: 'dashed'
            },
            symbol: 'circle',
            symbolSize: 6
          }
        ]
    };

    revenueChart.setOption(option);
    console.log('Revenue chart initialized successfully');

    // Handle resize
    const handleResize = () => {
      if (revenueChart) {
        revenueChart.resize();
      }
    };

    window.addEventListener('resize', handleResize);
  } catch (error) {
    console.error('Error initializing revenue chart:', error);
  }
}

function initTopBooksChart() {
  if (!topBooksChartContainer.value) {
    console.log('Top books chart container not found');
    return;
  }

  if (!topBooksData.value || !topBooksData.value.length) {
    console.log('No top books data available');
    return;
  }

  console.log('Initializing top books chart with data:', topBooksData.value);

  try {
    // Dispose existing chart
    if (topBooksChart) {
      topBooksChart.dispose();
    }

    topBooksChart = echarts.init(topBooksChartContainer.value);

    const option = {
      tooltip: {
        trigger: 'axis',
        axisPointer: {
          type: 'shadow'
        },
        formatter: function (params) {
          return `${params[0].name}: ${params[0].value} lượt thuê`;
        }
      },
      grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        top: '3%',
        containLabel: true
      },
      xAxis: {
        type: 'value',
        position: 'top'
      },
      yAxis: {
        type: 'category',
        data: topBooksData.value.map(item => item.name),
        inverse: true,
        axisLabel: {
          width: 100,
          overflow: 'truncate'
        }
      },
      series: [
        {
          name: 'Lượt thuê',
          type: 'bar',
          data: topBooksData.value.map(item => item.value),
          itemStyle: {
            color: function (params) {
              const colorList = ['#1890ff', '#36cfc9', '#52c41a', '#faad14', '#f5222d'];
              return colorList[params.dataIndex];
            }
          },
          label: {
            show: true,
            position: 'right',
            formatter: '{c} lượt'
          }
        }
      ]
    };

    topBooksChart.setOption(option);
    console.log('Top books chart initialized successfully');

    // Handle resize
    const handleResize = () => {
      if (topBooksChart) {
        topBooksChart.resize();
      }
    };

    window.addEventListener('resize', handleResize);
  } catch (error) {
    console.error('Error initializing top books chart:', error);
  }
}


definePageMeta({
  layout: 'admin'
});
</script>