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
          event.color = 'blue';
          event.timed = true;
          if(event.absence_request == null) {
            return event;
          }

          // 欠勤申請済みで未承諾の場合
          if(event.absence_request.request_check == 0) {
            event.color = 'grey';
            event.name = '欠勤申請が未承諾' + ' ' + event.user.name;
            event.is_absence_requested = true;
            return event;
          } 

          // 欠勤申請が承諾されていたら
          event.color = 'red';
          event.name = '欠勤申請承諾済み' + ' ' + event.user.name;
          event.is_absence_requested = true;
          return event;
        });
        this.events = events;
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', 'スケジュールの取得に失敗しました');
      })
    },
  },
  mounted() {
    this.getSchedule();
  }
}
</script>