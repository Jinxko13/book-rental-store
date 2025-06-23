<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Quản lý danh sách đơn bán chạy</h2>
    </div>

    <a-card class="mb-4">
      <div class="flex gap-4">
        <a-select v-model:value="selectedView" style="width: 240px" placeholder="Chọn chế độ xem">
          <a-select-option value="week">Top theo tuần</a-select-option>
          <a-select-option value="month">Top theo tháng</a-select-option>
        </a-select>

        <a-select v-if="selectedView === 'week'" v-model:value="selectedWeek" style="width: 120px" placeholder="Chọn tuần">
          <a-select-option v-for="w in 6" :key="w" :value="w">Tuần {{ w }}</a-select-option>
        </a-select>

        <a-select v-model:value="selectedMonth" style="width: 120px" placeholder="Chọn tháng">
          <a-select-option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</a-select-option>
        </a-select>

        <a-select v-model:value="selectedYear" style="width: 120px" placeholder="Chọn năm">
          <a-select-option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</a-select-option>
        </a-select>

        <a-button type="primary" @click="fetchTopOrders">
          <template #icon><SearchOutlined /></template>
          Lọc
        </a-button>
      </div>
    </a-card>

    <a-table :columns="columns" :dataSource="topOrders" :loading="loading" rowKey="id" bordered>
    </a-table>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { message } from 'ant-design-vue';
import { SearchOutlined } from '@ant-design/icons-vue';

const bookApi = useBooks();
const currentYear = new Date().getFullYear();
const yearOptions = computed(() => Array.from({ length: 10 }, (_, i) => currentYear - 5 + i));

const selectedView = ref('month');
const selectedWeek = ref(1);
const selectedMonth = ref(new Date().getMonth() + 1);
const selectedYear = ref(currentYear);
const topOrders = ref([]);
const loading = ref(false);

const params = {
  week: selectedWeek.value,
  month: selectedMonth.value,
  year: selectedYear.value
}

const columns = [
  { title: 'ID', dataIndex: 'id', key: 'id' },
  { title: 'Tên sách', dataIndex: 'title', key: 'title' },
  { title: 'Số lượt thuê', dataIndex: 'total_rented', key: 'total_rented' },
]

async function fetchTopOrders() {
  loading.value = true;
  try {
    const query = {
      month: selectedMonth.value,
      year: selectedYear.value
    };

    if (selectedView.value === 'week') {
      query.week = selectedWeek.value;
      const res = await axios.get('/api/top-book/weekly', { params: query });
      topOrders.value = res.data.data || [];
    } else {
      const res = await axios.get('/api/top-book/monthly', { params: query });
      topOrders.value = res.data.data || [];
    }
  } catch (err) {
    console.error(err);
    message.error('Lỗi khi tải dữ liệu top sách bán chạy');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchTopOrders();
})

definePageMeta({ layout: 'admin' });
</script>