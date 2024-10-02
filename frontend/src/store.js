// src/store.js
import { createStore } from 'vuex'
import { api } from './api/axiosConfig'

const store = createStore({
  state: {
    user: null, // Estado para almacenar información del usuario
    notes: [], // Estado para almacenar las notas
    isAuthenticated: false,
    authChecked: false
  },
  mutations: {
    setUser(state, user) {
      state.user = user
    },
    setNotes(state, notes) {
      state.notes = notes
    },
    addNote(state, note) {
      state.notes.push(note)
    },
    editNote(state, { index, updatedNote }) {
      state.notes[index] = updatedNote
    },
    setAuthentication(state, payload) {
      state.isAuthenticated = payload.isAuthenticated
      state.user = payload.user
      state.authChecked = true
    }
  },

  actions: {
    login({ commit }, userData) {
      return api.post('/login', userData).then((response) => {
        const token = response.data.token
        localStorage.setItem('token', token) // Almacenar el token
        commit('setAuthentication', { isAuthenticated: true, user: response.data.user })
      })
    },

    logout({ commit }) {
      localStorage.removeItem('token') // Eliminar el token
      commit('setAuthentication', { isAuthenticated: false, user: null })
    },

    async checkAuthentication({ commit }) {
      const token = localStorage.getItem('token')

      // Si no hay token almacenado, no se realiza la validación
      if (!token) {
        commit('setAuthentication', { isAuthenticated: false, user: null })
        return
      }

      try {
        // Realizar la solicitud al servidor para validar el token
        const response = await api.post(
          '/validate-token',
          {},
          { headers: { Authorization: `Bearer ${token}` } }
        )

        // Verificar si el token es válido según la respuesta del servidor
        if (response.data.valid) {
          console.log('user', response.data.user)
          commit('setAuthentication', { isAuthenticated: true, user: response.data.user })
        } else {
          commit('setAuthentication', { isAuthenticated: false, user: null })
          // Opcional: limpiar el token inválido de localStorage
          localStorage.removeItem('token')
        }
      } catch (error) {
        console.error('Error al validar el token:', error)
        commit('setAuthentication', { isAuthenticated: false, user: null })
        // Opcional: limpiar el token en caso de error
        localStorage.removeItem('token')
      }
    },

    // Nueva acción de registro
    register({ commit }, userData) {
      return api.post('/register', userData).then((response) => {
        const token = response.data.token
        localStorage.setItem('token', token)
        commit('setAuthentication', { isAuthenticated: true, user: response.data.user })
      })
    }
  }
})

export default store
