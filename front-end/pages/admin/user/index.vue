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
        this.$store.dispatch(
          'flashMessage/showMessage',
          {
            message: 'データを取得出来ませんでした',
            type: 'error',
            status: true,
          }
        );
        this.is_load = false;
      });
    },
    async deleteItem(item) {
      const index = this.items.indexOf(item)
      confirm('本当に削除しますか')

      let message = '';
      let type = '';
      await this.$axios.delete('/api/admin/user', {
        data: {
          user_id: item.id
        }
      })
      .then(res => {
        this.items.splice(index, 1);
        message = '削除しました';
        type = 'success';
      })
      .catch(e => {
        message = '削除に失敗しました';
        type = 'error';
      })

      this.$store.dispatch('flashMessage/showMessage', {
        message: message,
        type: type,
        status: true,
      })
    },
  },
  mounted() {
    this.getUser();
  }

}
</script>