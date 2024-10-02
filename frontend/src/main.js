// src/main.js
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store' // Importa tu store de Vuex

// Importa el archivo de Tailwind
import './assets/index.css'

const app = createApp(App)

store.dispatch('checkAuthentication')
app.use(router)
app.use(store) // Usa Vuex
app.mount('#app')
