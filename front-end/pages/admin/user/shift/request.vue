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
          value: 'user_name',
        },
        {
          text: 'リクエストの内容を確認したか',
          value: 'request_check',
        },
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
    }
  },
  mounted() {
    this.getShiftRequest();
  }
}
</script>