<template>
  <div>
    <v-sheet tile height="6vh" color="grey lighten-3" class="d-flex align-center">
      <v-btn outlined small class="ma-4" @click="user_dialog = true" :disabled="is_disabled">追加</v-btn>
    </v-sheet>
    <!-- userテーブル -->
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
          :disabled="is_disabled"
          color="error"
          @click="deleteItem(item)"
        >
          delete
        </v-btn>
      </template>
      <template v-slot:[`item.salary`]="{ item }">
        <v-btn :disabled="is_disabled" v-if="item.salary == null" small color="error" @click="openSalaryDialog(item)">給与未設定</v-btn>
        <v-btn :disabled="is_disabled" v-if="item.salary != null" small color="info" @click="openSalaryDialog(item)">給与設定済み</v-btn>
      </template>
    </v-data-table>
    <!-- ユーザー追加のダイアログ -->
    <v-dialog
      v-model="user_dialog"
      max-width="500px"
      transition="dialog-transition"
    >
      <v-card>
        <v-card-title primary-title>
          ユーザー追加フォーム
        </v-card-title>
        <v-card-text>
          <v-form ref="user_form" v-model="user_form">
            <v-text-field
              name="name"
              label="名前"
              autocomplete="off"
              v-model="user_form_data.name"
              :rules="[rules.required]"
            ></v-text-field>
            <v-text-field
              name="age"
              label="年齢"
              type="number"
              :rules="[rules.required]"
              v-model="user_form_data.age"
            ></v-text-field>
            <v-text-field
              name="password"
              label="パスワード"
              counter=""
              v-model="user_form_data.password"
              :type="user_form_data.show_password ? 'text' : 'password'"
              autocomplete="new-password"
              append-icon="mdi-eye-off" 
              @click:append="user_form_data.show_password = !user_form_data.show_password"
              :rules="[rules.password_length]"
            ></v-text-field>
            <v-text-field
              name="password_confirm"
              type="password"
              autocomplete="new-password"
              label="パスワード確認"
              :rules="[rules.password_confirm]"
            ></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn :disabled="is_disabled" color="success" @click="addUser">追加</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- 給与設定フォームのダイアログ -->
    <v-dialog
      v-model="salary_dialog"
      max-width="500px"
      transition="dialog-transition"
    >
      <v-card>
        <v-card-title primary-title>
          {{ salary_form_data.name }}さんの給与設定
        </v-card-title>
        <v-card-text>
          <v-text-field
            name="user_id"
            type="hidden"
            v-model="salary_form_data.user_id"
          ></v-text-field>
          <v-select
            :items="select_items"
            v-model="salary_form_data.salary_type"
            item-text="text"
            item-value="value"
            label="給与のタイプ"
          ></v-select>
          <v-text-field
            name="hour_salary"
            label="時給"
            type="number"
            v-model="salary_form_data.hour_salary"
          ></v-text-field>
          <v-text-field
            name="month_salary"
            label="月給"
            type="number"
            v-model="salary_form_data.month_salary"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn color="success" :disabled="is_disabled" @click="addSalary">決定</v-btn>
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
      is_disabled: false,
      salary_dialog: false,
      salary_form_data: {},
      user_dialog: false,
      user_form: false,
      user_form_data: {
        show_password: false,
      },
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
      rules: {
        required: value => !!value || '入力してください',
        password_length: value => (!!value && 6 <= value.length && value.length <= 12) || '6文字以上12文字以下で入力してください',
        password_confirm: value => (value == this.user_form_data.password) || 'パスワードが一致しません',
      },
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
    openSalaryDialog(item) {
      this.salary_dialog = true;
      this.salary_form_data.name = item.name;
      this.salary_form_data.user_id = item.id;
      if(item.salary) {
        this.salary_form_data.salary_type = item.salary.salary_type;
        this.salary_form_data.hour_salary = item.salary.hour_salary;
        this.salary_form_data.month_salary = item.salary.month_salary;
      }
    },
    async addSalary() {
      this.is_disabled = true;

      await this.$axios.post('/api/admin/user/salary', this.salary_form_data)
      .then(res => {
        this.$store.dispatch('flashMessage/showSuccessMessage', '給料の設定に成功しました');
        this.getUser();
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '給料の設定に失敗しました');
      })

      this.is_disabled = false;
    },
    async addUser() {
      this.is_disabled = true;
      let form = this.$refs.user_form;
      
      if(!form.validate()) {
        return;
      }

      await this.$axios.post('/api/admin/user', this.user_form_data)
      .then(() => {
        this.$store.dispatch('flashMessage/showSuccessMessage', 'ユーザの追加に成功しました');
        form.reset();
        this.getUser();
      })
      .catch(() => {
        this.$store.dispatch('flashMessage/showErrorMessage', 'ユーザの追加に失敗しました');
      })

      this.is_disabled = false;
    },
  },
  mounted() {
    this.getUser();
  }

}
</script>