<template>
    <HomeIntroduction />
    <Mainpage />
</template>


<script setup>
import Mainpage from '~/components/home/MainContent.vue';
useHead({
  title: 'BINK Publishers',
  meta: [
    { name: 'description', content: 'Trang chủ của BINK Publishers' }
  ]
})

</script>
<style scoped></style>