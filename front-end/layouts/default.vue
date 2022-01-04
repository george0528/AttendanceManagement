<template>
  <v-app id="inspire">
    <v-navigation-drawer
      v-model="drawer"
      :disable-route-watcher="true"
      app
    >
      <!--  -->
    </v-navigation-drawer>

    <v-app-bar app dark color="primary">
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

      <v-toolbar-title>Application</v-toolbar-title>
      <v-spacer></v-spacer>
        <v-btn 
          color="white"
          icon
          text
        >
          <v-icon>mdi-account-circle</v-icon>
        </v-btn>
        
      <v-toolbar-items>
      </v-toolbar-items>
    </v-app-bar>

    <v-main dark app>
      <FlashMessage />
      <Nuxt />
    </v-main>
  </v-app>
</template>

<script>
import FlashMessage from '../components/FlashMessage.vue'
export default {
  components: { FlashMessage },
  data: () => ({
    drawer: false
  }),
  methods: {
    getCsrfToken() {
      this.$axios.get('/sanctum/csrf-cookie');
    }
  },
  mounted() {
    if (!this.$cookies.get('XSRF-TOKEN') || !this.$cookies.get('backend_cookie')) {
      this.getCsrfToken();
    }
  }
}
</script>