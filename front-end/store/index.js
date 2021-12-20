// state
export const state = () => ({
  count: 1,
  alert: {
    status: 'none',
    message: '',
    count: -1,
  },
  is_user_login: false,
  is_admin_login: false,
});

// mutations
export const mutations = {
  addCount(state) {
    state.count += 1;
    console.log(state.count);
  },
  alert(state, message, type, count = 0) {
    state.alert.status = type;
    state.alert.message = message;
    state.alert.count = count;
  },
  flash(state) {
    if(state.alert.count == 0 && state.alert.message !== '' && state.alert.status !== 'none') {
      state.alert.count++;
      return;
    }
    state.alert.count = -1;
    state.alert.message = '';
    state.alert.status = 'none';
  },
  userLogin(state) {
    state.is_user_login = true;
  },
  userLogout(state) {
    state.is_user_login = false;
  },
  adminLogin(state) {
    state.is_admin_login = true;
  },
  adminLogout(state) {
    state.is_admin_login = false;
  }
  
};

// actions
export const actions = {
  timerCount({ commit }) {
    setTimeout(function() {
      commit("addCount");
    }, 1000);
  }
};

// getters
export const getters = {
  auth(state) {
    return state.is_login;
  }
};