<template>
    <div>
      <a-pagination v-model:current="current1" show-quick-jumper show-less-items :total="500"/>
      <br />
      <!-- <a-pagination v-model:current="current2" show-quick-jumper :total="500" disabled show-less-items @change="onChange" /> -->
    </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue';
const current1 = ref<number>(1);
const current2 = ref<number>(2);
const onChange = (pageNumber: number) => {
    console.log('Page: ', pageNumber);
};
</script>
  