<template>
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
      <div class="px-4 py-4">
        <div class="flex justify-between items-start">
          <h3 class="text-lg font-semibold text-gray-800">{{ todo.title }}</h3>
          <div class="flex space-x-2">
            <button @click="$emit('edit')" class="text-blue-600"><EditIcon class="h-5 w-5" /></button>
            <button @click="$emit('delete')" class="text-red-600"><TrashIcon class="h-5 w-5" /></button>
          </div>
        </div>
        <p class="text-sm text-gray-600">{{ todo.description }}</p>
        <span>{{ formatDate(todo.due_date) }}</span>
        <img v-if="todo.image" :src="todo.image" :alt="todo.title" class="mt-4 rounded-lg" />
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue'
  import { EditIcon, TrashIcon } from 'lucide-vue-next'
  
  const props = defineProps({
    todo: Object
  })
  
  const formatDate = (date) => {
    // Lógica para formatear la fecha
  }
  </script>
  