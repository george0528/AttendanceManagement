<template>
  <div>
    <v-dialog
      v-model="dialog"
      max-width="500px"
      transition="dialog-transition"
    >
      <v-card v-if="event != null">
        <v-card-title primary-title>
          欠勤申請
        </v-card-title>
        <v-card-text>
          <p class="text-h6 font-weight-medium text-decoration-underline grey--text text--darken-4">名前</p>
          <p>{{ event.user.name }}</p>
          <p class="text-h6 font-weight-medium text-decoration-underline grey--text text--darken-4">出勤時間</p>
          <p>{{ event.start_time }}</p>
          <p class="text-h6 font-weight-medium text-decoration-underline grey--text text--darken-4">退勤時間</p>
          <p>{{ event.end_time }}</p>
          <p class="text-h6 font-weight-medium text-decoration-underline grey--text text--darken-4">コメント</p>
          <p v-if="event.absence_request.comment">{{ event.absence_request.comment }}</p>
          <p v-if="event.absence_request.comment == null">コメントはありませんでした</p>
        </v-card-text>
        <v-card-actions>
          <v-btn :disabled="is_disabled" @click="absenceApproval" color="success">承認</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <Calendar :btn="btn" :events="events" @clickEvent="openAbsenceForm"/>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dialog: false,
      btn: {
        flag: false,
        text: ''
      },
      events: [],
      is_load: false,
      event: null,
      is_disabled: false
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
    openAbsenceForm(event) {
      if(event.absence_request == null) {
        return;
      }
      if (event.absence_request.request_check == 1) {
        return;
      }
      this.event = event;
      this.dialog = true;
    },
    async absenceApproval() {
      this.is_disabled = true;

      await this.$axios.put('/api/admin/absence', {
        absence_id: this.event.absence_request.id
      })
      .then(res => {
        this.getSchedule();
        this.$store.dispatch('flashMessage/showSuccessMessage', '欠勤申請の承諾に成功しました');
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '欠勤申請の承諾に失敗しました');
      })

      this.dialog = false;
      this.is_disabled = false;
    },
    hello() {
      console.log('hello');
    }
  },
  mounted() {
    this.getSchedule();
  }
}
</script>