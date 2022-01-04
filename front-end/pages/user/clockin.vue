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
        <v-btn @click="clockIn" color="info" :disabled="disabled">出勤</v-btn>
        <nuxt-link to="/user/clockout">退勤画面へ</nuxt-link>
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
    async clockIn() {
      this.disabled = true;

      await this.$axios.post('/api/user/clockin')
      .then(res => {
        this.$store.dispatch('flashMessage/showSuccessMessage', `${res.data.start_time}に出勤しました`);
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', e.response.data);
      })

      this.disabled = false;
    }
  },
  computed: {
    name() {
      return this.$store.state.user.name
    } 
  }
}
</script>