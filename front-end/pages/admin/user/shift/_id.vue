<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="events"
      class="elevation-1"
      show-select
      v-model="selected"
      item-key="id"
      :loading="is_load"
      loading-text="データを取得中です"
    >

    </v-data-table>
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
      },
      is_load: false,
      headers: [
        {
          text: 'id',
          value: 'id'
        },
        {
          text: '出勤',
          value: 'start_time'
        },
        {
          text: '退勤',
          value: 'end_time'
        },
      ]
    }
  },
  methods: {
    async getShiftRequestDate() {
      this.is_load = true;
      await this.$axios.get(`api/admin/shift/${this.$route.params.id}`)
      .then(res => {
        let data = res.data;
        let shift_dates = data.shift_request_dates.map(date => {
          date.color = 'red';
          date.timed = true;
          return date
        });
        this.events = shift_dates;
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showMessage', {
          message: 'シフトリクエストのデータの取得に失敗しました',
          type: 'error',
          status: true,
        })
      })

      this.is_load = false;
    },
  },
  mounted() {
    this.getShiftRequestDate();
  }
}
</script>

