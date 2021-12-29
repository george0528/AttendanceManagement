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
      <template v-slot:[`item.delete`]="{ item }">
        <v-btn
          small
          color="error"
          @click="deleteItem(item)"
        >
          delete
        </v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
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
      ],
    }
  },
  methods: {
    async getDeleteUsers() {
      this.is_load = true;
      await this.$axios.get('/api/admin/user/delete')
      .then(res => {
        console.log(res);
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
    }
  },
  mounted() {
    this.getDeleteUsers();
  }
}
</script>