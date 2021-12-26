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
        text: '',
      }
    }
  },
  methods: {
    async getShiftRequestDate() {
      this.events = await this.$getDates(this, `api/admin/shift/${this.$route.params._id}`, 'red', 'シフトリクエストデータの取得に失敗しました');
    },
  },
  mounted() {
    this.getShiftRequestDate();
  }
}
</script>

