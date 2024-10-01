<template>
    <div class="notes-container">
      <h1>Notas</h1>
      <div v-for="(note, index) in notes" :key="index">
        <h2>{{ note.title }}</h2>
        <p>{{ note.content }}</p>
        <button @click="editNote(index)">Editar</button>
      </div>
      <button @click="createNote">Crear Nueva Nota</button>
    </div>
  </template>
  
  <script>

  </script>
  
  <style scoped>

  </style>
  