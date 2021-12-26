<template>
  <div>
    <v-data-table
      :headers="headers"
      items="items"
      hide-actions
      class="elevation-1"
      select-all
      pagination.sync="pagination"
      item-key="id"
      :loading="true"
      search="search"
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
      ]
    }
  },
}
</script>