<template>
  <div>
    <v-dialog
      v-model="dialog"
    >
      <div>
        <v-card>
          <v-card-title primary-title>
            スケジュールの申請
          </v-card-title>
          <v-card-text>
            <v-text-field
              name="start_time"
              label="出勤時間"
              type="datetime-local"
              v-model="start_time"
              :min="min"
            ></v-text-field>
            <v-text-field
              name="end_time"
              label="退勤時間"
              type="datetime-local"
              v-model="end_time"
              :min="min"
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-btn color="info" @click="addSchedule" :disabled="is_disabled">申請</v-btn>
          </v-card-actions>
        </v-card>
      </div>
    </v-dialog>
    <v-dialog
      v-model="event"
      v-if="event"
    >
      <div>
        <v-card>
          <v-card-title class="red--text" primary-title>
            欠勤申請
          </v-card-title>
          <v-card-text>
            <h3>出勤時間</h3>
            <div>{{ event.start_time }}</div>
            <h3>退勤時間</h3>
            <div>{{ event.end_time }}</div>
            <v-text-field
              name="id"
              type="hidden"
            ></v-text-field>
            <v-text-field
              name="commnet"
              type="textarea"
              v-model="event.commnet"
              label="コメント"
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-btn color="info" @click="addAbsence" :disabled="is_disabled">欠勤申請</v-btn>
          </v-card-actions>
        </v-card>
      </div>
    </v-dialog>
    <Calendar :events="events" :btn="btn" @btnClickEmit="dialogOpen" @clickEvent="openAbsenceForm"/>
  </div>
</template>


<script>
import moment from "moment";
let tomorrow = moment(new Date()).add(1, 'days').format('YYYY-MM-DDTHH:MM');
export default {
  data() {
    return {
      events: [],
      dialog: false,
      schedules: [],
      start_time: tomorrow,
      end_time: tomorrow,
      event: null,
      btn: {
        flag: true,
        text: '申請を追加',
      },
      is_disabled: false,
    }
  },
  methods: {
    async getSchedule() {
      this.is_disabled = true;
      this.events = await this.$getDates(this, '/api/user/schedule', 'red','シフトの取得に失敗しました');
      this.is_disabled = false;
    },
    dialogOpen() {
      this.dialog = true;
    },
    async addSchedule() {
      this.is_disabled = true;
      this.schedules = [];
      this.schedules.push({
        start_time: moment(this.start_time).format('YYYY-MM-DD HH:MM'),
        end_time: moment(this.end_time).format('YYYY-MM-DD HH:MM'),
      })
      
      await this.$axios.post('/api/user/schedule', {
        schedules: this.schedules,
      })
      .then(res => {
        this.$store.dispatch('flashMessage/showSuccessMessage', '申請に成功しました');
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '申請に失敗しました');
      })

      this.is_disabled = false;
    },
    openAbsenceForm(event) {
      if(!event.is_absence_requested) {
        this.event = event;
      }
    },
    async addAbsence() {
      this.is_disabled = true;
      await this.$axios.post('/api/user/absence', {
        schedule_id: this.event.id,
        comment: this.event.comment
      })
      .then(res => {
        this.$store.dispatch('flashMessage/showSuccessMessage', '欠勤申請に成功しました');
        const index = this.events.indexOf(this.event);
        this.events[index].color = 'grey';
        this.events[index].name = '欠勤申請が未承諾';
        this.event = null;
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '欠勤申請に失敗しました');
      })

      this.is_disabled = false;
    }
  },
  mounted() {
    this.getSchedule();
  },
  computed: {
    min() {
      var min = tomorrow;
      return min;
    },
  }
}
</script>