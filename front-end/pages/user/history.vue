<template>
  <div>
    <Calendar :events="events"/>
    <!-- <Calendar /> -->
  </div>
</template>

<script>
export default {
  data() {
    return {
      events: [],
    }
  },
}
</script>