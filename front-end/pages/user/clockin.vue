<template>
  <div>
    <v-card>
      <v-card-title primary-title>
        出勤
      </v-card-title>
      <v-card-text>
        {{name}}さんで出勤します
      </v-card-text>
      <v-card-actions>
        <v-btn @click="clockIn" color="info">出勤</v-btn>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script>
export default {
  middleware: 'user_auth',
  methods: {
    clockIn() {

    }
  },
  computed: {
    name() {
      return this.$store.state.user.name
    } 
  }
}
</script>