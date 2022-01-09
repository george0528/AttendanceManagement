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
      <template v-slot:[`item.salary`]="{ item }">
        <v-btn v-if="item.salary == null" small color="error" @click="openDialog(item)">給与未設定</v-btn>
        <v-btn v-if="item.salary != null" small color="info">給与設定済み</v-btn>
      </template>
    </v-data-table>
      <!-- ダイアログ -->
      <v-dialog
        v-model="dialog"
        max-width="500px"
        transition="dialog-transition"
      >
        <v-card>
          <v-card-title primary-title>
            {{ form_data.name }}さんの給与設定
          </v-card-title>
          <v-card-text>
            <v-text-field
              name="user_id"
              type="hidden"
              v-model="form_data.user_id"
            ></v-text-field>
            <v-select
              :items="select_items"
              v-model="form_data.salary_type"
              item-text="text"
              item-value="value"
              label="給与のタイプ"
            ></v-select>
            <v-text-field
              name="hour_salary"
              label="時給"
              type="number"
              v-model="form_data.hour_salary"
            ></v-text-field>
            <v-text-field
              name="month_salary"
              label="月給"
              type="number"
              v-model="form_data.month_salary"
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-btn color="success" @click="addSalary">決定</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      is_load: false,
      dialog: false,
      form_data: {},
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
        },
        {
          text: '給与',
          value: 'salary',
          sortable: false,
        }
      ],
      items: [],
      select_items: [
        { text: '時給', value: 'hour' },
        { text: '月給', value: 'month' }
      ],
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
      let res = confirm('本当に削除しますか')

      if (res == false) {
        return;
      }

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
    openDialog(item) {
      this.dialog = true;
      this.form_data.name = item.name;
      this.form_data.user_id = item.id;
      if(item.salary) {
        this.form_data.salary_type = item.salary.salary_type;
        this.form_data.hour_salary = item.salary.hour_salary;
        this.form_data.month_salary = item.salary.month_salary;
      }
    },
    addSalary() {
      console.log(this.form_data);
    }
  },
  mounted() {
    this.getUser();
  }

}
</script>