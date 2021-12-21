<template>
  <div>
    <v-card>
      <v-card-title primary-title>
        退勤
      </v-card-title>
      <v-card-text>
        {{name}}さんで退勤します
      </v-card-text>
      <v-card-actions>
        <v-btn @click="clockOut" color="info">退勤</v-btn>
        <nuxt-link to="/user/clockin">出勤画面へ</nuxt-link>
      </v-card-actions>
      <nuxt-link to="/">index</nuxt-link>
    </v-card>
  </div>
</template>

<script>
export default {
  middleware: 'user_auth',
  methods: {
    async clockOut() {
      let message = '';
      let type = '';

      await this.$axios.post('/api/user/clockout')
      .then(res => {
        type = 'success';
        message = `${res.data.end_time}に退勤しました`
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