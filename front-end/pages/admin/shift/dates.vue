<template>
  <div>
    <Calendar :events="events" />
  </div>
</template>

<script>
export default {
  data() {
    return {
      events: []
    }
  },
  methods: {
    async getShiftRuest() {
      let events = await this.$getDates(this, '/api/admin/shift', 'red', 'シフトリクエストの取得に失敗をしました');
      
    }
  }
}
</script>