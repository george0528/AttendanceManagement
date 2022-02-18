<template>
  <div>
    <Calendar :events="events" :btn="btn"/>
  </div>
</template>

<script>
export default {
  data() {
    return {
      events: [],
      btn: {
        flag: false,
        text: ''
      }
    }
  },
  middleware: 'user_auth',
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