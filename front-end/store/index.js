export const state = () => ({
  count: 1,
  alert: {
    status: 'none',
    message: 'test',
  }
});

export const mutations = {
  addCount(state) {
    state.count += 1;
    console.log(state.count);
  },
  updateAlertData(state, alert_data) {
    state.alert.status = alert_data.status;
    state.alert.message = alert_data.message;
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