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
  // Verificar si el usuario está autenticado desde Vuex o desde localStorage
  const isAuthenticated = store.state.isAuthenticated || localStorage.getItem('authToken')

  // 1. Si está autenticado, permitir acceso a todas las rutas protegidas
  if (isAuthenticated) {
    // Si está autenticado y trata de acceder a Home o Register, redirigir a Notes
    if (to.name === 'Home' || to.name === 'Register') {
      return next({ name: 'Notes' }) // Redirige a Notes si ya está autenticado
    }
    next() // Permite la navegación a las demás rutas
  } else {
    // 2. Si no está autenticado, redirigir a Home si la ruta requiere autenticación
    if (to.meta.requiresAuth) {
      return next({ name: 'Home' }) // Redirige a Home si no está autenticado
    }
    next() // Permite acceso a rutas públicas (Home, Register)
  }
})

export default router
