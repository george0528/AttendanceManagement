export default async function(context) {
  // ログインしていなければ
  if(!context.store.getters.is_login) {
    await context.$axios.
      get('/api/user/get')
      .then(res => {
        context.store.commit('userLogin');
      })
      .catch(e => {
        context.redirect('/user/login');
      })
  }
}