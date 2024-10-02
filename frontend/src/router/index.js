// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/LoginView.vue'
import Register from '../views/Register.vue'
import store from '../store'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresAuth: false } // Permite acceso a todos
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false } 
  },
  {
    path: '/notes',
    name: 'Notes',
    component: () => import('../views/NotesView.vue'),
    meta: { requiresAuth: true } 
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Middleware para proteger las rutas
router.beforeEach(async (to, from, next) => {
  
  if (!store.state.authChecked) {
    await store.dispatch('checkAuthentication')
  }

  const isAuthenticated = store.state.isAuthenticated // Obtén el estado de autenticación

  // Lógica para manejar rutas según el estado de autenticación
  if (isAuthenticated) {
    // Si está autenticado, protege las rutas de Home y Register
    if (to.name === 'Home' || to.name === 'Register') {
      next({ name: 'Notes' }) // Redirige a Notes si intenta acceder a Home o Register
    } else {
      next() // Permite la navegación
    }
  } else {
    // Si no está autenticado, protege la ruta de Notes
    if (to.meta.requiresAuth) {
      next({ name: 'Home' }) // Redirige a Home si intenta acceder a Notes
    } else {
      next() // Permite la navegación
    }
  }
})

export default router
