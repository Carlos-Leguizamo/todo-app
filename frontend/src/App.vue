<template>
  <div id="app">
    <router-view />
    <Loading v-if="isLoading" />
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import { mapState } from 'vuex'
import Loading from './components/Loading.vue'


export default {
  name: 'App',
  created() {
    this.checkAuthentication(); // Llamar a la acción al crear el componente
  },
  methods: {
    ...mapActions(['checkAuthentication']),
  },
  components: {
    Loading,
  },
  computed: {
    ...mapState(['authChecked']),
    isLoading() {
      return !this.authChecked; // Devuelve true si no se ha verificado la autenticación
    }
  }
};
</script>

<style>

</style>
