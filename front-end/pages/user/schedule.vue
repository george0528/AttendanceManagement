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
              :value="start_time"
              :min="now"
            ></v-text-field>
            <v-text-field
              name="end_time"
              label="退勤時間"
              type="datetime-local"
              :value="end_time"
              :min="now"
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-btn color="info" @click="addSchedule" :disabled="disable">申請</v-btn>
          </v-card-actions>
        </v-card>
      </div>
    </v-dialog>
    <Calendar :events="events" :btn_flag="true"/>
  </div>
</template>


<script>
export default {
  data() {
    return {
      events: [],
      dialog: true,
      schedules: [],
      start_time: '',
      end_time: '',
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
      this.schedules.push({
        start_time: this.start_time,
        end_time: this.end_time,
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
    now() {
      var now = new Date().toLocaleString("ja");
      return now;
    },
  }
}
</script>