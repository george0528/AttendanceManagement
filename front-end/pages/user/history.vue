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
  methods: {
    async getScadule() {
      await this.$axios.get('/api/user/history')
      .then(res => {
        this.events = res.data;
        console.log(res);
      })
      .catch(e => {
        console.log(e.response.data);
        this.$store.dispatch('flashMessage/showMessage', {
          message: '就業履歴の取得に失敗しました',
          type: 'error',
          status: true,
        })
      })
    }
  },
  mounted() {
    this.getScadule();
  }
}
</script>