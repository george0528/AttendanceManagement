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
      is_load: false,
      headers: [
        {
          text: 'ID',
          value: 'id',
        },
        {
          text: '名前',
          value: 'name',
        },
        {
          text: '年齢',
          value: 'age',
        },
        {
          text: '削除',
          value: 'delete',
          sortable: false,
        }
      ],
      items: [],
    }
  },
  methods: {
    getUser() {
      this.is_load = true;
      this.$axios.get('/api/admin/user')
      .then(res => {
        this.items = res.data;
        this.is_load = false;
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', 'データを取得出来ませんでした');
        this.is_load = false;
      });
    },
    async deleteItem(item) {
      confirm('本当に削除しますか')

      await this.$axios.delete('/api/admin/user', {
        data: {
          user_id: item.id
        }
      })
      .then(res => {
        const index = this.items.indexOf(item)
        this.items.splice(index, 1);
        this.$store.dispatch('flashMessage/showSuccessMessage', '削除しました');
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '削除に失敗しました');
      })

    },
  },
  mounted() {
    this.getUser();
  }

}
</script>