<script setup lang="ts">
import Footer from '~/components/Layouts/Footer.vue';
const isAntdReady = ref(false);
onBeforeMount(() => {
    isAntdReady.value = true; 
});
</script>

<template>
  <div class="layout" v-show="isAntdReady">
    <header>
      <LayoutsHeader />
    </header>

    <main>
      <slot />
      <a-back-top :visibility-height="0" />
    </main>

    <Footer></Footer>
  </div>
</template>


<style scoped>
.logo {
  height: 64px;
  margin: 0;
  overflow: hidden;
}
</style>