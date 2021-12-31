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
        <v-btn v-if="item.request_check == 0" color="error">未承諾です</v-btn>
        <v-btn v-if="item.request_check != 0" color="success">承諾済みです</v-btn>
      </template>
      <template v-slot:[`item.approval`]="{ item }">
        <v-btn v-if="item.request_check == 0" @click="absenceApproval(item)" color="success" :disabled="disabled">承諾</v-btn>
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
          text: 'id',
          value: 'id'
        },
        {
          text: '名前',
          value: 'schedule.user.name'
        },
        {
          text: '開始時間',
          value: 'schedule.start_time'
        },
        {
          text: '終了時間',
          value: 'schedule.end_time'
        },
        {
          text: 'コメント',
          value: 'comment'
        },
        {
          text: '状態',
          value: 'check_text'
        },
        {
          text: '承諾',
          value: 'approval'
        }
      ],
      items: [],
      disabled: false,
      is_load: false,
    }
  },
  methods: {
    getAbsence() {
      this.$axios.get('/api/admin/absence')
      .then(res => {
        this.items = res.data;
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '欠勤申請の取得に失敗しました');
      })
    },
    async absenceApproval(absence) {
      this.disabled = true;
      await this.$axios.put('/api/admin/absence', {
        absence_id: absence.id
      })
      .then(res => {
        this.$store.dispatch('flashMessage/showSuccessMessage', '欠勤申請の承諾に成功しました');
        const index = this.items.indexOf(absence);
        this.items[index].request_check = 1;
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '欠勤申請の承諾に失敗しました');
      })

      this.disabled = false;
    },

  },
  mounted() {
    this.getAbsence();
  }
}
</script>