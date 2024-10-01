<!-- src/App.vue -->
<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  name: 'App',
  created() {
    this.checkAuthentication(); // Llamar a la acción al crear el componente
  },
  methods: {
    ...mapActions(['checkAuthentication']),
  },
};
</script>

<style>
/* Puedes agregar estilos globales aquí si es necesario */
</style>
