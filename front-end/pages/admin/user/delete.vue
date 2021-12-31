<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="delete_users"
      class="elevation-1"
      show-select
      :loading="is_load"
      loading-text="データを取得中です"
    > 
      <template v-slot:[`item.restore`]="{ item }">
        <v-btn
          small
          :disabled="disabled"
          color="success"
          @click="restoreUser(item)"
        >
          復元
        </v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      api_url: '/api/admin/user/delete',
      disabled: false,
      delete_users: [],
      is_load: false,
      headers: [
        {
          text: 'id',
          value: 'id'
        },
        {
          text: '名前',
          value: 'name'
        },
        {
          text: 'ログインID',
          value: 'login_id'
        },
        {
          text: '年齢',
          value: 'age'
        },
        {
          text: '復元',
          value: 'restore'
        },
      ],
    }
  },
  methods: {
    async getDeleteUsers() {
      this.is_load = true;
      await this.$axios.get(this.api_url)
      .then(res => {
        this.delete_users = res.data; 
      }) 
      .catch(e => {
        this.$store.dispatch('flashMessage/showMessage', {
          status: true,
          message: 'ユーザーの取得に失敗しました',
          type: 'error',
        });
      })
      this.is_load = false;
    },
    async restoreUser(user) {
      this.disabled = true;
      await this.$axios.put(this.api_url, {
        user_id: user.id
      })
      .then(res => {
        const index = this.items.indexOf(item);
        this.delete_users.splice(index, 1);
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', 'ユーザーの復元に失敗しました');
      })

      this.disabled = false;
    }
  },
  mounted() {
    this.getDeleteUsers();
  }
}
</script>