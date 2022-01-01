<template>
  <div>
    <v-card>
      <v-card-title primary-title>
        メニュー
      </v-card-title>
      <v-card-actions>
        <v-btn color="success" to="/admin/login">ログイン</v-btn>
        <v-btn color="success" to="/admin/schedule">スケジュール確認</v-btn>
        <v-btn color="success" to="/admin/user">ユーザー管理</v-btn>
        <v-btn color="success" to="/admin/shift">シフト申請の管理</v-btn>
        <v-btn color="success" to="/admin/absence">欠勤申請の管理</v-btn>
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