<template>
    <div>
          
    </div>
</template>

<script setup>
definePageMeta({
  layout: 'admin',
});

onMounted(async () => {
  await navigateTo('/admin/dashboard');
});

</script>