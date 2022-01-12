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
            :type="showPassword ? 'text' : 'password'"
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

      // api
      await this.$axios.post('/api/admin/login', {
        email: this.email,
        password: this.password,
      })
      .then(res => {
        this.$store.commit('admin/login');
        this.$store.dispatch('flashMessage/showSuccessMessage', 'ログインしました');
        this.$router.push('/admin');
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', e.response.data);
      });

      // inputを空白にする
      this.email = '';
      this.password = '';
    },
  },
}
</script>

