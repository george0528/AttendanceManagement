<template>
  <div class="main">
    <v-card width="400px" class="mx-auto mt-5">
      <v-card-title primary-title>
        ユーザーログイン
      </v-card-title>
      <v-card-text>
        <v-form @submit.prevent>
          <v-text-field
            name="login_id"
            label="login_id"
            v-model="login_id"
            prepend-icon="mdi-account-circle" 
          ></v-text-field>
          <v-text-field
            name="password"
            label="password"
            v-model="password"
            v-bind:type="showPassword ? 'text' : 'password'"
            prepend-icon="mdi-lock" 
            append-icon="mdi-eye-off" 
            @click:append="showPassword = !showPassword"
          ></v-text-field>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-btn color="info" @click="userLogin" :disabled="disabled">ログイン</v-btn>  
      </v-card-actions>
    </v-card>
    <nuxt-link to="/">indexページ</nuxt-link>
  </div>
</template>

<script>
export default {
  data(){
    return {
      showPassword: false,
      login_id: '',
      password: '',
      disabled: false,
    }
  },
  methods: {
    async userLogin() {
      this.disabled = true;

      // api
      await this.$axios.post('/api/user/login', {
        login_id: this.login_id,
        password: this.password,
      })
      .then(res => {
        const name = res.data.name;
        this.$store.dispatch('user/login', name);
        this.$store.dispatch('flashMessage/showSuccessMessage', 'ログインしました');
        this.$router.push('/user');
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', 'ログインに失敗しました');
      });

      // inputを空白にする
      this.login_id = '';
      this.password = '';
      this.disabled = false;
    },
  },
}
</script>

