// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/LoginView.vue';
import Register from '../views/Register.vue';
import store from '../store'; 

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
  },
  {
    path: '/notes',
    name: 'Notes',
    component: () => import('../views/HomeView.vue'),
    meta: { requiresAuth: true }, // Indica que esta ruta requiere autenticación
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Guardia de navegación
router.beforeEach((to, from, next) => {
  const isAuthenticated = store.state.isAuthenticated; // Obtén el estado de autenticación

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'Home' }); // Redirige al inicio de sesión si no está autenticado
  } else {
    next(); // Permite la navegación
  }
});

export default router;
