<template>
  <div>
    <v-card>
      <v-card-title primary-title>
        設定
      </v-card-title>
      <v-card-text v-show="is_load">
        <v-progress-circular
          size="60"
          width="10"
          rotate="0"
          value="40"
          color="primary"
          indeterminate
        >
        </v-progress-circular>
      </v-card-text>
      <v-card-text v-show="!is_load">
        <v-checkbox 
          label="給与明細の自動作成" 
          v-model="option_form.create_payslip" 
        ></v-checkbox>
        <v-text-field
          name="salary_closing_day"
          label="給料締め日"
          v-model="option_form.salary_closing_day"
          type="number"
          placeholder="例:25"
          :rules="[rules.required, rules.number_between]"
        ></v-text-field>
      </v-card-text>
      <v-card-actions v-show="!is_load">
        <v-btn small color="success" :disabled="is_disabled" @click="putOption">決定</v-btn>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      is_load: false,
      is_disabled: false,
      option_form: {},
      rules: {
        required: value => !!value || '入力してください',
        number_between: value => (1 <= value && value <= 28 ) || '1~28を選択してください',
      },
    }
  },
  methods: {
    async getOption() {
      this.is_load = true;
      this.is_disabled = true;

      await this.$axios.get('/api/admin/option')
      .then(res => {
        this.option_form = res.data;
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '設定の取得に失敗しました');
      })

      this.is_load = false;
      this.is_disabled = false;
    },
    async putOption() {
      this.is_load = true;
      this.is_disabled = true;

      await this.$axios.put('/api/admin/option', this.option_form)
      .then(res => {
        this.$store.dispatch('flashMessage/showSuccessMessage', '設定を変更しました');
      })
      .catch(e => {
        this.$store.dispatch('flashMessage/showErrorMessage', '設定の変更に失敗しました');
      })

      this.is_load = false;
      this.is_disabled = false;
    }
  },
  mounted() {
    this.getOption();
  }
}
</script>

