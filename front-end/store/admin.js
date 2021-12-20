// state
export const state = () => ({
  name: '',
  is_login: false,
});

// mutations
export const mutations = {
  login(state) {
    state.is_login = true;
  },
  logout(state) {
    state.is_login = false;
  }
};

// actions

// getters
export const getters = {
  isLogin(state) {
    return state.is_login;
  }
};