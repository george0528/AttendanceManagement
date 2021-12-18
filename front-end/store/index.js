export const state = () => ({
  count: 1,
  alert: {
    status: 'none',
    message: '',
  }
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
  }
};