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
  data(){
    return {
      login_id: '',
      password: '',
    }
  },
  methods: {
    async userLogin() {
      // alertデータ作成
      let alert_data = {
        'status': 'none',
        'message': '',
      }

      // api
      await this.$axios.post('/api/user/login', {
        login_id: this.login_id,
        password: this.password,
      })
      .then(res => {
        console.log('true',res);
        alert_data.status = 'success';
        alert_data.message = 'ログインに成功しました';
      })
      .catch(e => {
        console.log('error',e.response);
        alert_data.status = 'danger';
        alert_data.message = 'ログインに失敗しました';
        alert_data.message = e.response.data;
      });
      
      // storeのalertデータを変更
      this.$store.commit('updateAlertData', alert_data);
      // inputを空白にする
      this.login_id = '';
      this.password = '';
    },
  },
}
</script>

