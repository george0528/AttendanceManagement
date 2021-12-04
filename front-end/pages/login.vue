<template>
  <div class="main">
    <form @submit.prevent>
      <button type='submit' @click="submit">axiosテスト</button>
    </form>
    <button @click="api">認証テスト</button>
    <button @click="api_no_csrf">認証テスト No csrf</button>
    <button @click="user">ユーザー情報テスト</button>
    <div></div>
    <button @click="get">ログイン済みか</button>
    <button @click="cookie">cookieテスト</button>
    <div></div>
    <button @click="admin">adminテスト</button>
    <button @click="admin_no_csrf">admin No CSRFテスト</button>
    <button @click="admin_user">admin ユーザー情報テスト</button>
    <div></div>
    <button @click="session_delete">セッションの情報削除</button>
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
      url_admin: 'http://localhost:8000/api/admin/login',
      url_admin_user: 'http://localhost:8000/api/admin/user',
      url_delete: 'http://localhost:8000/api/session/delete',
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
    api_no_csrf() {
      console.log('auth no csrf');
      axios.post(this.url, {
        email: 'wcartwright@example.org',
        password: 'password',
      }, {withCredentials: true})
      .then(res => {
        console.log(res);
      })
      .catch(e => {
        console.log(e.response);
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
    },
    admin() {
      console.log('admin');
      axios.get(this.url_csrf, {withCredentials: true}).then(res => {
        axios.post(this.url_admin, {
          email: 'mschuster@example.org',
          password: 'password',
        },{withCredentials: true})
        .then(response => {
          console.log(response);
        })
        .catch(error => {
          console.log(error.response);
        });
      });
    },
    admin_no_csrf() {
      console.log('admin_no_csrf');
      axios.post(this.url_admin, {
        email: 'mschuster@example.org',
        password: 'password',
      }, {withCredentials: true})
      .then(response => {
        console.log(response);
      })
      .catch(error => {
        console.log(error.response);
      });
    },
    admin_user() {
      console.log('admin_user');
      axios.get(this.url_admin_user, {withCredentials: true})
      .then(res => {
        console.log(res);
      })
      .catch(e => {
        console.log(e.response);
      })
    },
    session_delete() {
      console.log('session_delete');
      axios.get(this.url_delete, {withCredentials: true})
      .then(res => {
        console.log(res);
      })
      .catch(e => {
        console.log(e.response);
      });
    }
  }
}
</script>

