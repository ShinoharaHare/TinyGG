import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        authenticated: false
    },
    mutations: {
        setAuth(state, v) {
            state.authenticated = v;
        }
    },
    actions: {
        async checkAuth({ commit, state }) {
            let { data } = await axios.post('/api/token/verify')
            commit('setAuth', data)
            return state.authenticated
        },
        async authenticate({ commit, state }, password: string) {
            let { status } = await axios.post('/api/token', { password })
            commit('setAuth', status === 200 )
            return state.authenticated
        }
    },
    modules: {
    }
})
