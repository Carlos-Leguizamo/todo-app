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
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: 'RegisterView',
  data() {
    return {
      username: '',
      email: '',
      password: '',
      confirmPassword: ''
    }
  },
  computed: {
    ...mapState(['errorMessage']) // Mapea el estado del error desde Vuex
  },
  methods: {
    async handleSubmit() {
      if (this.password !== this.confirmPassword) {
        this.$store.commit('setError', 'Las contraseñas no coinciden')
        return
      }

      const userData = {
        name: this.username, // Cambiado de username a name
        email: this.email,
        password: this.password,
        password_confirmation: this.confirmPassword
      }

      // Llama a la acción de registro en Vuex
      await this.$store.dispatch('register', userData)

      // Redirige o maneja el estado después del registro
      if (!this.errorMessage) {
        this.$router.push('/') // Cambia la ruta según sea necesario
      }
    }
  }
}
</script>

<style scoped>
/* Estilos adicionales si los necesitas */
</style>
