<template>
  <div>
    <h1>運営ログイン画面</h1>
    <form @submit.prevent>
      <input v-model="email" type="text">
      <input v-model="password" type="password">
      <button type='submit' @click="adminLogin">ログイン</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: ''
    }
  },
  methods: {
    async adminLogin() {
      await this.$axios.post('/api/admin/login', {
        email: this.email,
        password: this.password
      })
      .then(res => {
        this.$store.commit('alertSuccess', 'ログインに成功しました');
      })
      .catch(e => {
        let message = e.response.data;
        this.$store.commit('alertFail', message);
      })

      // inputを空白にする
      this.email = '';
      this.password = '';
    }
  }
}
</script>