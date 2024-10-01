// src/store.js
import { createStore } from 'vuex';
import { api } from './api/axiosConfig';

const store = createStore({
  state: {
    user: null, // Estado para almacenar información del usuario
    notes: [], // Estado para almacenar las notas
    isAuthenticated: false,
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setNotes(state, notes) {
      state.notes = notes;
    },
    addNote(state, note) {
      state.notes.push(note);
    },
    editNote(state, { index, updatedNote }) {
      state.notes[index] = updatedNote;
    },
    setAuthentication(state, payload) {
      state.isAuthenticated = payload.isAuthenticated;
      state.user = payload.user;
    },
  },
  
  actions: {
    login({ commit }, userData) {
      return api.post('/login', userData).then((response) => {
        const token = response.data.token;
        localStorage.setItem('token', token); // Almacenar el token
        commit('setAuthentication', { isAuthenticated: true, user: response.data.user });
      });
    },
    
    logout({ commit }) {
      localStorage.removeItem('token'); // Eliminar el token
      commit('setAuthentication', { isAuthenticated: false, user: null });
    },
    
    checkAuthentication({ commit }) {
      const token = localStorage.getItem('token');
      if (token) {
        commit('setAuthentication', { isAuthenticated: true, user: null });
      } else {
        commit('setAuthentication', { isAuthenticated: false, user: null });
      }
    },
    
    // Nueva acción de registro
    register({ commit }, userData) {
      return api.post('/register', userData).then((response) => {
        const token = response.data.token;
        localStorage.setItem('token', token); // Almacenar el token
        commit('setAuthentication', { isAuthenticated: true, user: response.data.user });
      });
    },
  },
});

export default store;
