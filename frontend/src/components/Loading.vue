<template>
  <div class="fixed inset-0 flex items-center justify-center bg-gray-100 bg-opacity-90 z-50">
    <div class="flex flex-col items-center bg-white p-8 rounded-lg shadow-lg border border-gray-300">
      <div class="loader"></div>
      <p class="mt-4 text-lg font-medium text-gray-800">Cargando, por favor espere...</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadingComponent',
}
</script>

<style scoped>
.loader {
  border: 8px solid rgba(0, 0, 0, 0.1);
  border-top: 8px solid #007BFF; /* Color de la parte superior del spinner */
  border-radius: 50%;
  width: 60px;  /* Tamaño del spinner */
  height: 60px; /* Tamaño del spinner */
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.bg-gray-100 {
  background-color: #f8f9fa; /* Un gris muy claro para el fondo */
}

.bg-white {
  background-color: #ffffff; /* Blanco puro para el contenedor */
}

.text-gray-800 {
  color: #343a40; /* Un gris oscuro para el texto */
}

.border-gray-300 {
  border-color: #dee2e6; /* Un gris suave para el borde del contenedor */
}

/* Efecto de desvanecimiento al cargar */
.fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
