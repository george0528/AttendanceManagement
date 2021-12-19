export default async function(context) {
  // ログインしていなければ
  if(!context.store.getters.is_login) {
    await context.$axios.
    get('/api/user')
    .then(res => {
      context.store.commit('userLogin');
    })
    .catch(e => {
      context.store.commit('alertFail', 'ユーザーログインしてください');
      context.redirect('/user/login');
    });
  }
}