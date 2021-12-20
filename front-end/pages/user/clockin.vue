<template>
  <div>
    <v-card>
      <v-card-title primary-title>
        出勤
      </v-card-title>
      <v-card-text>
        {{name}}さんで出勤します
      </v-card-text>
      <v-card-actions>
        <v-btn @click="clockIn" color="info">出勤</v-btn>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script>
export default {
  middleware: 'user_auth',
  methods: {
    async clockIn() {
      let message = '';
      let type = '';

      await this.$axios.post('/api/user/clockin')
      .then(res => {
        type = 'success';
        message = `${res}に出勤しました`
      })
      .catch(e => {
        type = 'error';
        message = e.response.data;
      })

      this.$store.dispatch(
        'flashMessage/showMessage',
        {
          type: type,
          message: message,
          status: true,
        }
      )
    }
  },
  computed: {
    name() {
      return this.$store.state.user.name
    } 
  }
}
</script>