<template>
  <div>
    <Calendar :btn="btn" :events="events"/>
  </div>
</template>

<script>
export default {
  data() {
    return {
      btn: {
        flag: false,
        text: ''
      },
      events: [],
      is_load: false
    }
  },
  methods: {
    async getSchedule() {
      await this.$axios.get('/api/admin/schedule')
      .then(res => {
        var data = res.data;
        var events = data.map(event => {
          event.name = event.user.name;
          event.color = 'red';
          event.timed = true;
          return event;
        });
        this.events = events;
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', 'スケジュールの取得に失敗しました');
      })
    }
  },
  mounted() {
    this.getSchedule();
  }
}
</script>