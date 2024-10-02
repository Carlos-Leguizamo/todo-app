<template>
  <div class="min-h-screen flex justify-center items-center bg-gray-100">
    <div class="bg-white rounded-lg shadow-md p-8 max-w-sm w-full">
      <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Registro</h1>
      <p class="text-sm text-gray-600 mb-6 text-center">
        Crea una cuenta para gestionar tus tareas y notas.
      </p>

      <form @submit.prevent="handleSubmit">
        <div class="mb-4">
          <label for="username" class="block text-gray-700 mb-1">Nombre de usuario</label>
          <input
            id="username"
            type="text"
            placeholder="Ingrese su nombre de usuario"
            v-model="username"
            required
            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
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
        <div class="mb-4">
          <label for="confirm-password" class="block text-gray-700 mb-1">Confirmar contraseña</label>
          <input
            id="confirm-password"
            type="password"
            placeholder="Confirme su contraseña"
            v-model="confirmPassword"
            required
            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <button
          type="submit"
          :disabled="!username || !email || !password || !confirmPassword || errorMessage"
          class="w-full px-4 py-2 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-600 transition duration-200 ease-in-out cursor-pointer"
        >
          Registrarse
        </button>
      </form>

      <p v-if="errorMessage" class="text-red-500 text-center">{{ errorMessage }}</p>
      <p class="text-xs text-gray-500 mt-4 text-center">
        ¿Ya tienes cuenta?
        <router-link to="/" class="text-blue-500 hover:underline">Inicia sesión aquí</router-link>.
      </p>
    </div>

    <!-- Componente de Carga -->
    <Loading v-if="isLoading" />
  </div>
</template>

<script>
import { mapState } from 'vuex';
import Loading from '@/components/Loading.vue';
import Swal from 'sweetalert2';

export default {
  name: 'RegisterView',
  components: {
    Loading
  },
  data() {
    return {
      username: '',
      email: '',
      password: '',
      confirmPassword: '',
      isLoading: false // Estado de carga
    };
  },
  computed: {
    ...mapState(['errorMessage']) // Mapea el estado del error desde Vuex
  },
  methods: {
    async handleSubmit() {
      if (this.password !== this.confirmPassword) {
        // Alerta para contraseñas no coincidentes
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Las contraseñas no coinciden.',
          customClass: {
            popup: 'bg-white rounded-lg shadow-lg p-4 max-w-xs',
            title: 'text-xl font-bold text-gray-800',
            content: 'text-sm text-gray-600',
            confirmButton: 'bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600 transition duration-200'
          }
        });
        return;
      }

      // Validación de longitud de contraseña
      if (this.password.length < 8) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'La contraseña debe tener al menos 8 caracteres.',
          customClass: {
            popup: 'bg-white rounded-lg shadow-lg p-4 max-w-xs',
            title: 'text-xl font-bold text-gray-800',
            content: 'text-sm text-gray-600',
            confirmButton: 'bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600 transition duration-200'
          }
        });
        return;
      }

      const userData = {
        name: this.username,
        email: this.email,
        password: this.password,
        password_confirmation: this.confirmPassword
      };

      this.isLoading = true; // Inicia el estado de carga

      try {
        // Llama a la acción de registro en Vuex
        await this.$store.dispatch('register', userData);

        // Espera un momento para mostrar el componente de carga
        await new Promise((resolve) => setTimeout(resolve, 1500)); // Esperar 1.5 segundos (ajusta según lo que necesites)

        // Redirige o maneja el estado después del registro
        if (!this.errorMessage) {
          this.$router.push('notes'); // Cambia 'Home' a 'notes'
        }
      } catch (error) {
        console.error("Error during registration:", error);
        // Aquí puedes manejar otros posibles errores
      } finally {
        this.isLoading = false; // Finaliza el estado de carga
      }
    }
  }
};
</script>

<style scoped>
/* No se necesitan estilos adicionales, ya que todo está manejado por Tailwind CSS */
</style>
