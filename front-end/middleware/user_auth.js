export default async function(context) {
  context.$axios.get('/api/user/get');
  console.log(context);

  // if(!context.store.getters.is_login) {
  //   context.redirect('/');
  // }
}