<template>
  <div class="min-h-screen flex justify-center items-center bg-gray-100">
    <div class="bg-white rounded-lg shadow-md p-8 max-w-sm w-full">
      <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Iniciar Sesión</h1>
      <p class="text-sm text-gray-600 mb-6 text-center">Accede a tu cuenta para gestionar tus tareas y notas.</p>

      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="email" class="block text-gray-700 mb-1">Correo electrónico</label>
          <input
            id="email"
            type="email"
            placeholder="Ingrese su correo electrónico"
            v-model="email"
            required
            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-gray-700 mb-1">Contraseña</label>
          <input
            id="password"
            type="password"
            placeholder="Ingrese su contraseña"
            v-model="password"
            required
            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <button
          type="submit"
          class="w-full px-4 py-2 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-600 transition duration-200 ease-in-out"
        >
          Iniciar Sesión
        </button>
      </form>

      <p class="text-xs text-gray-500 mt-4 text-center">
        ¿No tienes cuenta? <router-link to="/register" class="text-blue-500 hover:underline">Regístrate aquí</router-link>.
      </p>
    </div>

    <!-- Componente de carga -->
    <Loading v-if="isLoading" />
  </div>
</template>

<script setup>
import { useStore } from 'vuex'; // Importa useStore
import { useRouter } from 'vue-router'; // Importa useRouter
import { ref } from 'vue';
import Loading from '@/components/Loading.vue'; // Asegúrate de importar tu componente de carga
import Swal from 'sweetalert2'; // Importa SweetAlert2

const store = useStore(); // Usa el store de Vuex
const router = useRouter(); // Usa el router
const email = ref('');
const password = ref('');
const isLoading = ref(false); // Estado de carga

const handleSubmit = () => {
  isLoading.value = true; // Activar el estado de carga
  console.log('Email:', email.value);
  console.log('Password:', password.value);

  // Llama a la acción de login del store
  store.dispatch('login', { email: email.value, password: password.value })
    .then(() => {
      // Redirige al usuario a otra página después de un inicio de sesión exitoso
      router.push({ name: 'Notes' }); // Cambia aquí
    })
    .catch((error) => {
      console.error(error);
      // Mostrar la alerta de error
      Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Email o contraseña incorrectos',
          customClass: {
            popup: 'bg-white rounded-lg shadow-lg p-4 max-w-xs',
            title: 'text-xl font-bold text-gray-800',
            content: 'text-sm text-gray-600',
            confirmButton: 'bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600 transition duration-200'
          }
        });
    })
    .finally(() => {
      isLoading.value = false; // Desactivar el estado de carga después de que finalice la solicitud
    });
};
</script>
