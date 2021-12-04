<template>
  <div class="main">
    <form @submit.prevent>
      <button type='submit' @click="submit">axiosテスト</button>
    </form>
    <button @click="api">認証テスト</button>
    <button @click="get">ログイン済みか</button>
    <button @click="cookie">cookieテスト</button>
    <button @click="user">ユーザー情報テスト</button>
    <div v-for="item in ary" :key="item.id">
      <div>{{ item }}</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data(){
    return {
      name: 'aaaa',
      ary: [],
      url: 'http://localhost:8000/api/session',
      url_csrf: 'http://localhost:8000/sanctum/csrf-cookie',
      url_test: 'https://test-use-domain.net/api/menus',
      url_get: 'http://localhost:8000/api/login',
      url_cookie: 'http://localhost:8000/api/test',
      url_user: 'http://localhost:8000/api/user',
    }
  },
  methods: {
    submit() {
      console.log('aaa');
      axios.get(this.url_test)
          .then(response => this.ary = response.data);
    },
    api() {
      console.log('auth');
      axios.get(this.url_csrf, {withCredentials: true}).then(res => {
        axios.post(this.url, {
          email: 'wcartwright@example.org',
          password: 'password'
        }, {withCredentials: true})
        .then(response => {
          console.log(response.data);
        })
        .catch(error => {
          console.log(error);
        });
      });
    },
    get() {
      console.log('get');
      axios.get(this.url_csrf, {withCredentials: true}).then(res => {
        axios.get(this.url_get, {withCredentials: true})
          .then(response => {
            console.log(response);
          })
          .catch(error => {
            console.log(error.response);
          });
      });
    },
    cookie() {
      console.log('cookie');
      axios.get(this.url_csrf, {withCredentials: true}).then(res => {
        axios.get(this.url_cookie, {withCredentials: true})
          .then(response => {
            console.log(response);
          })
          .catch(error => {
            console.log(error.response);
          });
      });
    },
    user() {
      console.log('user');
      axios.get(this.url_csrf, {withCredentials: true}).then(res => {
        axios.get(this.url_user, {withCredentials: true})
        .then(response => {
          console.log(response);
        })
        .catch(error => {
          console.log(error.response);
        });
      });
    }
  }
}
</script>

