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
    async getSchedule() {
      this.events = await this.$getSchedule(this, '/api/user/schedule');
    }
  },
  mounted() {
    this.getSchedule();
  }
}
</script>