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
            <v-btn color="info" @click="addSchedule" :disabled="disable">申請</v-btn>
          </v-card-actions>
        </v-card>
      </div>
    </v-dialog>
    <Calendar :events="events" :btn="btn" @btnClickEmit="dialogOpen"/>
  </div>
</template>


<script>
import moment from "moment";
let tomorrow = moment(new Date()).add(1, 'days').format('YYYY-MM-DDTHH:MM');
export default {
  data() {
    return {
      events: [],
      dialog: true,
      schedules: [],
      start_time: tomorrow,
      end_time: tomorrow,
      btn: {
        flag: true,
        text: '申請を追加',
      },
      disable: false,
    }
  },
  methods: {
    async getSchedule() {
      this.disable = true;
      this.events = await this.$getDates(this, '/api/user/schedule');
      this.disable = false;
    },
    dialogOpen() {
      this.dialog = true;
    },
    async addSchedule() {
      this.disable = true;
      this.schedules = [];
      this.schedules.push({
        start_time: moment(this.start_time).format('YYYY-MM-DD HH:MM'),
        end_time: moment(this.end_time).format('YYYY-MM-DD HH:MM'),
      })
      
      let message = '';
      let type = '';
      await this.$axios.post('/api/user/schedule', {
        schedules: this.schedules,
      })
      .then(res => {
        message = '申請出来ました';
        type = 'success';
      })
      .catch(e => {
        message = '申請に失敗しました';
        type = 'error';
      })

      this.$store.dispatch('flashMessage/showMessage', {
        message: message,
        type: type,
        status: true,
      });
      this.disable = false;
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