export default {
  // 追記
  vue: {
    devtools: true
  },

  ssr: true,

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    title: 'front-end',
    htmlAttrs: {
      lang: 'ja'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' },
      { name: 'format-detection', content: 'telephone=no' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  // router
  router: {
    middleware: 'csrf',
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
  ],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
    {src: '@/plugins/axios.js', mode: 'server'},
    {src: '@/plugins/utils.js', mode: 'client'},
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    '@nuxtjs/vuetify'
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    '@nuxtjs/axios',
    ['cookie-universal-nuxt', { parseJSON: false }],
  ],

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {
  },
  
  axios: {
    credentials: true
  },

  // 追記
  publicRuntimeConfig: {
    baseURL: process.env.BASE_URL,
  },
}
