<template>
  <div>
    <Nuxt />
  </div>
</template>

<script>
export default {
  mounted() {
    // csrf対策
    this.$axios.get('/sanctum/csrf-cookie');
  }
}
</script>