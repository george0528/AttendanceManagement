<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="items"
      class="elevation-1"
      show-select
      :loading="is_load"
      loading-text="データを取得中です"
    >
      <template v-slot:[`item.check_text`]="{ item }">
        <v-btn v-if="item.request_check == 0" @click="itemRequestCheckChange(item)" color="error">未確認です</v-btn>
        <v-btn v-if="item.request_check != 0" @click="itemRequestCheckChange(item)" color="success">確認済みです</v-btn>
      </template>
      <template v-slot:[`item.more`]="{ item }">
        <v-btn @click="clickMore(item)" color="primary">詳細</v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      headers: [
        {
          text: 'リクエストID',
          value: 'id',
        },
        {
          text: 'ユーザーネーム',
          value: 'user.name',
        },
        {
          text: '作成日',
          value: 'created_at'
        },
        {
          text: '確認済みか',
          value: 'check_text',
        },
        {
          text: 'スケジュールの詳細',
          value: 'more'
        }
      ],
      items: [],
      is_load: false,
    }
  },
  methods: {
    async getShiftRequest() {
      this.is_load = true;
      await this.$axios.get('/api/admin/shift')
      .then(res => {
        console.log(res.data);
        this.items = res.data;
      })
      .catch(e => {
        console.log(e);
        this.$store.dispatch('flashMessage/showMessage', {
          message: 'シフトリクエストの取得に失敗しました',
          type: 'error',
          status: true,
        })
      })

      this.is_load = false;
    },
    itemRequestCheckChange(item) {
      // api
      // this.$axios.post('/api/admin/shift/check')
      item.request_check = !item.request_check;
    },
    clickMore(item) {
      this.$router.push(`/admin/user/shift/${item.id}`);
    }
  },
  mounted() {
    this.getShiftRequest();
  }
}
</script>