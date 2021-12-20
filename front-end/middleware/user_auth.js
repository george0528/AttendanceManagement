export default async function(context) {
  // ログインしていなければ
  if(!context.store.getters['user/isLogin']) {
    await context.$axios.
    get('/api/user')
    .then(res => {
      context.store.commit('user/login');
    })
    .catch(e => {
      context.store.dispatch('flashMessage/showMessage',
        {
          message: 'ユーザーログインしてください',
          type: 'error',
          status: true,
        }
      );
      context.redirect('/user/login');
    });
  }
}