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
    meta: { requiresAuth: true } // Solo autenticados pueden acceder
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Middleware para proteger las rutas
router.beforeEach(async (to, from, next) => {
  // 1. Si ya se ha chequeado la autenticación en Vuex
  if (!store.state.authChecked) {
    await store.dispatch('checkAuthentication') // Chequear autenticación desde Vuex
  }

  // 2. Verificar autenticación desde localStorage (opcional)
  const isAuthenticated = store.state.isAuthenticated || localStorage.getItem('authToken') // Ejemplo con token

  // Si el usuario está autenticado
  if (isAuthenticated) {
    // Evitar que los usuarios autenticados accedan a Home o Register
    if (to.name === 'Home' || to.name === 'Register') {
      return next({ name: 'Notes' }) // Redirige a Notes si está autenticado
    }
    // Permitir acceso a todas las demás rutas
    next()
  } else {
    // Si no está autenticado, redirige a Home si la ruta requiere autenticación
    if (to.meta.requiresAuth) {
      return next({ name: 'Home' }) // Redirige a Home
    }
    // Permitir acceso a rutas públicas
    next()
  }
})

export default router
