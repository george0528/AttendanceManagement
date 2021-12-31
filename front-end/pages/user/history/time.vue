<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="items"
      class="elevation-1"
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
      is_load: false,
      items: [],
      headers: [
        {
          text: '合計就業時間',
          value: 'sum_times'
        },
        {
          text: '合計就業深夜時間',
          value: 'midnight_times'
        }
      ]
    }
  },
  methods: {
    async getHistoryTime() {
      this.is_load = true;
      await this.$axios.get('/api/user/history/time')
      .then(res => {
        this.items.push(res.data);
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', 'データの取得に失敗しました');
      })

      this.is_load = false;
    }
  },
  mounted() {
    this.getHistoryTime();
  }
}
</script>