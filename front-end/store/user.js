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
  },
  setName(state, name) {
    state.name = name;
  }
}

// actions
export const actions = {
  login({commit}, name) {
    commit('login');
    commit('setName', name);
  }
}

// getters
export const getters = {
  isLogin(state) {
    return state.is_login;
  }
}