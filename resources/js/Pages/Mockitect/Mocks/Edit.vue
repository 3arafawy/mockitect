<script setup>
import MockForm from './MockForm.vue';

defineProps({
  mock: Object,
});
</script>

<template>
  <MockForm :mock="mock" :is-editing="true" />
</template>
