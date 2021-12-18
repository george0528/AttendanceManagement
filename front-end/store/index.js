export const state = () => ({
  count: 1,
  alert: {
    status: 'none',
    message: '',
  },
  is_user_login: false,
  is_admin_login: false,
});

export const mutations = {
  addCount(state) {
    state.count += 1;
    console.log(state.count);
  },
  alertSuccess(state, message) {
    state.alert.status = 'success';
    state.alert.message = message;
  },
  alertFail(state, message) {
    state.alert.status = 'danger';
    state.alert.message = message;
  },
  toggleUserLogin(state) {
    state.is_user_login = !state.is_user_login;
  },
  toggleAdminLogin(state) {
    state.is_admin_login = !state.is_admin_login;
  }
  
};

export const actions = {
  timerCount({ commit }) {
    setTimeout(function() {
      commit("addCount");
    }, 1000);
  }
};

export const getters = {
  message(state) {
    return state.alert.message;
  },
  auth(state) {
    return state.is_login;
  }
};