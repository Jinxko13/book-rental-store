<template>
  <div class="w-full flex flex-col">
    <BookContent />
    <BookFavorite :BookItem="dataBook" />
  </div>
</template>

<script setup>
import BookContent from './BookContent/BookContent.vue'
import BookFavorite from './BookContent/BookFavorite.vue'
const bookApi = useBooks()
const dataBook = await bookApi.getBookById(3)
</script>
  