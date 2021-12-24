<template>
  <div>
    <Calendar :events="events"/>
  </div>
</template>

<script>
export default {
  data() {
    return {
      events: [],
    }
  },
  methods: {
    async getHistory() {
      this.events = await this.$getDates(this, '/api/user/history');
    }
  },
  mounted() {
    this.getHistory();
  }
}
</script>