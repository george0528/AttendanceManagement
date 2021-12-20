// state
export const state = () => ({
  count: 1,
});

// mutations
export const mutations = {
  addCount(state) {
    state.count += 1;
    console.log(state.count);
  },
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
  count(state) {
    return state.count;
  }
};