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
        <v-btn @click="clockOut" color="info" :disabled="disabled">退勤</v-btn>
        <nuxt-link to="/user/clockin">出勤画面へ</nuxt-link>
      </v-card-actions>
      <nuxt-link to="/">index</nuxt-link>
    </v-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      disabled: false,
    }
  },
  middleware: 'user_auth',
  methods: {
    async clockOut() {
      this.disabled = true;

      await this.$axios.post('/api/user/clockout')
      .then(res => {
        this.$store.dispatch('flashMessage/showSuccessMessage', `${res.data.end_time}に退勤しました`);
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', e.response.data);
      })

      this.disabled = false
    }
  },
  computed: {
    name() {
      return this.$store.state.user.name
    } 
  }
}
</script>