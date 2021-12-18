<template>
  <div class="main">
    <h1>ユーザーログイン画面</h1>
    <form @submit.prevent>
      <input v-model="login_id" type="text">
      <input v-model="password" type="password">
      <button type='submit' @click="userLogin">ログイン</button>
    </form>
  </div>
</template>

<script>
export default {
  middleware: 'auth',
  data(){
    return {
      login_id: '',
      password: '',
    }
  },
  methods: {
    async userLogin() {
      // api
      await this.$axios.post('/api/user/login', {
        login_id: this.login_id,
        password: this.password,
      })
      .then(res => {
        this.$store.commit('alertSuccess', 'ログインに成功しました');
      })
      .catch(e => {
        let message = e.response.data;
        this.$store.commit('alertFail', message);
      });

      // inputを空白にする
      this.login_id = '';
      this.password = '';
    },
  },
}
</script>

