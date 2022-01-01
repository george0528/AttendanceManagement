<template>
  <div>
    <v-card>
      <v-card-title primary-title>
        ユーザーメニュー
      </v-card-title>
      <v-card-actions>
        <v-btn color="success" to="/user/login">ログイン</v-btn>
        <v-btn color="success" to="/user/schedule">スケジュール確認</v-btn>
        <v-btn color="success" to="/user/clockin">出勤</v-btn>
        <v-btn color="success" to="/user/clockout">退勤</v-btn>
        <v-btn color="success" to="/user/history">就業履歴</v-btn>
        <v-btn color="success" to="/user/absence">欠勤申請</v-btn>
        <v-btn color="error" @click="linkBack">戻る</v-btn>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script>
export default {
  methods: {
    linkBack() {
      this.$router.back();
    }
  }
}
</script>