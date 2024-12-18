import axios from 'axios'

export default {
  namespaced: true,

  state: {
    authenticated: false,
    user: {}
  },

  getters: {
    authenticated (state) {
      return state.authenticated
    },

    user (state) {
      return state.user
    },
  },

  mutations: {
    SET_AUTHENTICATED (state, value) {
      state.authenticated = value
    },

    SET_USER (state, value) {
      state.user = value
    }
  },

  actions: {
    async signIn ({ dispatch }, credentials) {
      await axios.get('/sanctum/csrf-cookie')
      await axios.post('/api/login', credentials)
      return dispatch('me')
    },

    async signOut ({ dispatch }) {
      await axios.post('/api/logout')
    },

    me ({ commit }) {
      return axios.get('/api/me').then((response) => {
        commit('SET_AUTHENTICATED', true)
        commit('SET_USER', response.data)
      }).catch(() => {
        commit('SET_AUTHENTICATED', false)
        commit('SET_USER', {})
      })
    }
  }
}
