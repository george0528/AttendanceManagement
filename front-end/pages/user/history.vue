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
      this.events = await this.$getDates(this, '/api/user/history', 'red', '就業履歴の取得に失敗しました');
    }
  },
  mounted() {
    this.getHistory();
  }
}
</script>