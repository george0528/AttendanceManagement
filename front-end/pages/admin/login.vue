<template>
  <div class="main">
    <v-card width="400px" class="mx-auto mt-5">
      <v-card-title primary-title>
        管理者  ログイン
      </v-card-title>
      <v-card-text>
        <v-form @submit.prevent>
          <v-text-field
            name="email"
            label="email"
            v-model="email"
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
        <v-btn color="info" @click="userLogin">ログイン</v-btn>  
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
      email: '',
      password: '',
    }
  },
  methods: {
    async userLogin() {
      let message = '';
      let type = '';

      // api
      await this.$axios.post('/api/admin/login', {
        email: this.email,
        password: this.password,
      })
      .then(res => {
        this.$store.commit('adminLogin');
        message = 'ログインしました';
        type = 'success';
      })
      .catch(e => {
        message = e.response.data;
        type = 'error'; 
      });

      // flashメッセージ
      this.$store.dispatch(
        'flashMessage/showMessage',
        {
          message: message,
          type: type,
          status: true,
        }
      );

      // inputを空白にする
      this.email = '';
      this.password = '';
    },
  },
  layout: 'vutify',
}
</script>

