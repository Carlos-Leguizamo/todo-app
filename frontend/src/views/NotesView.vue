<template>
  <div class="min-h-screen bg-gray-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
      <!-- Barra superior con título y botón de logout -->
      <div class="flex justify-between items-center mb-6 bg-white shadow-md rounded-lg p-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Gestor de Notas</h1>
        <button
          @click="logout"
          class="flex items-center space-x-2 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 rounded-md transition duration-200 ease-in-out"
        >
          <LogOutIcon class="h-5 w-5" />
          <span>Cerrar Sesión</span>
        </button>
      </div>

      <div class="flex flex-col lg:flex-row gap-6">
        <!-- Formulario de creación/edición de tareas -->
        <div class="w-full lg:w-1/3 bg-white shadow-lg rounded-lg p-6">
          <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-800">
            {{ isEditing ? 'Editar Tarea' : 'Crear Notas' }}
          </h2>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="space-y-2">
              <label for="title" class="block text-sm font-medium text-gray-700">
                Título
                <span class="text-red-500">*</span>
              </label>

              <div class="relative rounded-md shadow-sm">
                <input
                  id="title"
                  v-model="currentTodo.title"
                  type="text"
                  required
                  maxlength="30"
                  minlength="10"
                  class="block w-full pr-10 py-2 px-3 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Ingrese el título"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <clipboard-icon class="h-5 w-5 text-gray-400" />
                </div>
              </div>
            </div>

            <div class="space-y-2">
              <label for="description" class="block text-sm font-medium text-gray-700"
                >Descripción<span class="text-red-500">*</span></label
              >
              <textarea
                id="description"
                v-model="currentTodo.description"
                rows="4"
                required
                minlength="10"
                maxlength="200"
                class="block w-full py-2 px-3 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none"
                placeholder="Describa la nota"
              ></textarea>
            </div>

            <div class="space-y-2">
              <label for="dueDate" class="block text-sm font-medium text-gray-700"
                >Fecha de vencimiento</label
              >
              <div class="relative rounded-md shadow-sm">
                <input
                  id="dueDate"
                  v-model="currentTodo.dueDate"
                  type="date"
                  class="block w-full pl-10 py-2 px-3 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <calendar-icon class="h-5 w-5 text-gray-400" />
                </div>
              </div>
            </div>

            <div class="space-y-2">
              <label for="tags" class="block text-sm font-medium text-gray-700"
                >Etiquetas<span class="text-red-500">*</span></label
              >
              <div class="relative rounded-md shadow-sm">
                <input
                  id="tags"
                  v-model="currentTodo.tags"
                  required
                  type="text"
                  class="block w-full pl-10 py-2 px-3 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Separadas por comas"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <tag-icon class="h-5 w-5 text-gray-400" />
                </div>
              </div>
            </div>

            <div class="space-y-2">
              <label for="image" class="block text-sm font-medium text-gray-700"
                >Cargar imagen</label
              >
              <input
                id="image"
                type="file"
                @change="onImageChange"
                class="block w-full py-2 px-3 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                accept="image/*"
              />
            </div>

            <button
              type="submit"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <save-icon class="mr-2 h-5 w-5" />
              {{ isEditing ? 'Actualizar Tarea' : 'Crear Tarea' }}
            </button>
          </form>
        </div>

        <!-- Lista de tareas -->
        <div class="w-full lg:w-2/3 bg-white shadow-lg rounded-lg p-6">
          <div
            class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
          >
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Listados de Notas</h2>
            <div class="relative rounded-md shadow-sm w-full sm:w-64">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar notas..."
                class="block w-full pl-10 pr-4 py-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <search-icon class="h-5 w-5 text-gray-400" />
              </div>
            </div>
          </div>
          <transition-group name="list" tag="div" class="space-y-4">
            <div
              v-for="todo in filteredTodos"
              :key="todo.id"
              class="bg-white shadow-md rounded-lg overflow-hidden transition duration-300 ease-in-out hover:shadow-lg border border-gray-200"
            >
              <div class="px-4 py-4 sm:px-6">
                <div class="flex justify-between items-start">
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ todo.title }}</h3>
                  <div class="flex space-x-2">
                    <button
                      @click="editTodo(todo)"
                      class="text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out"
                    >
                      <edit-icon class="h-5 w-5" />
                    </button>
                    <button
                      @click="deleteTodo(todo.id)"
                      class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out"
                    >
                      <trash-icon class="h-5 w-5" />
                    </button>
                  </div>
                </div>
                <p class="text-sm text-gray-600 mb-4">{{ todo.description }}</p>
                <div class="flex items-center text-xs text-gray-500 mb-2">
                  <calendar-icon class="h-4 w-4 mr-1" />
                  <span>{{ formatDate(todo.due_date) }}</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-2">
                  <span
                    v-for="(tag, index) in todo.tags"
                    :key="index"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {{ tag }}
                  </span>
                </div>

                <img
                  v-if="todo.image"
                  :src="todo.image"
                  :alt="todo.title"
                  class="mt-4 rounded-lg max-w-full h-auto"
                />
              </div>
            </div>
          </transition-group>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue'
import {
  CalendarIcon,
  TagIcon,
  ImageIcon,
  SaveIcon,
  SearchIcon,
  EditIcon,
  TrashIcon,
  ClipboardIcon,
  LogOutIcon
} from 'lucide-vue-next'
import { api } from '@/api/axiosConfig'
import { useStore } from 'vuex'

const store = useStore() // Acceder al store
const user = computed(() => store.state.user)

const todos = ref([])
const currentTodo = ref({
  id: null,
  title: '',
  description: '',
  dueDate: '',
  tags: '',
  image: null
})
const isEditing = ref(false)
const searchQuery = ref('')

// Función para reiniciar el formulario
const resetForm = () => {
  currentTodo.value = {
    id: null,
    title: '',
    description: '',
    dueDate: '',
    tags: '',
    image: null
  }
  isEditing.value = false
}
// Función para cerrar sesión
const logout = () => {
  localStorage.clear()
  window.location.href = '/'
}

// Obtener la lista de notas al cargar el componente
api.get(`/notes/${user.value?.id}`).then((response) => {
  console.log('response', response.data)
  todos.value = response.data
})

// Filtrar las notas según la búsqueda
const filteredTodos = computed(() => {
  return todos.value.filter((todo) => {
    return (
      todo.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      todo.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  })
})

// Función para obtener la lista de notas
const fetchTodos = async () => {
  try {
    const response = await api.get(`/notes/${user.value?.id}`)

    // Asegúrate de que la imagen se mantenga como Base64
    todos.value = response.data.map((todo) => ({
      ...todo,
      image: todo.image ? todo.image : null // Si la imagen está en Base64, se utiliza directamente
    }))
  } catch (error) {
    console.error('Error al obtener las notas:', error)
  }
}

// Modifica tu función handleSubmit para que llame a fetchTodos
const handleSubmit = async () => {
  const formData = new FormData()
  formData.append('title', currentTodo.value.title)
  formData.append('description', currentTodo.value.description)

  // Formatear dueDate solo si se proporciona
  if (currentTodo.value.dueDate) {
    const dueDate = new Date(currentTodo.value.dueDate)
    if (!isNaN(dueDate.getTime())) {
      const formattedDueDate = dueDate.toISOString().split('T')[0] // Formato YYYY-MM-DD
      formData.append('due_date', formattedDueDate)
    } else {
      console.error('Fecha de vencimiento no válida')
      return
    }
  }

  formData.append('user_id', user.value?.id)

  // Manejo de tags
  if (currentTodo.value.tags) {
    // Asegurarse de que currentTodo.value.tags sea una cadena
    const tagsString = typeof currentTodo.value.tags === 'string' ? currentTodo.value.tags : ''
    const tags = tagsString
      .split(',')
      .map((tag) => tag.trim())
      .filter((tag) => tag) // Filtrar tags vacíos

    tags.forEach((tag) => {
      // Agregar cada tag al FormData 
      formData.append('tags[]', tag)
    })

    // Asignar los tags al currentTodo como un array
    currentTodo.value.tags = tags
  }

  // Si hay una imagen, agregarla al FormData
  if (currentTodo.value.image) {
    formData.append('image', currentTodo.value.image)
  }

  try {
    if (isEditing.value) {
      console.log('currentTodo', currentTodo.value)
      await api.put(`/notes/${currentTodo.value.id}`, currentTodo.value)
      // Actualizar la nota editada en la lista local de todos
      const index = todos.value.findIndex((todo) => todo.id === currentTodo.value.id)
      if (index !== -1) {
        todos.value[index] = { ...currentTodo.value }
      }
    } else {
      const response = await api.post('/notes-store', formData)
      todos.value.push(response.data)
    }

    // Actualizar la lista de tareas después de agregar o editar
    await fetchTodos()
    resetForm()
  } catch (error) {
    console.error('Error al guardar la nota:', error)
  }
}

// Función para editar una nota
const editTodo = (todo) => {
  currentTodo.value = { ...todo }
  isEditing.value = true
}

// Función para eliminar una nota
const deleteTodo = async (todoId) => {
  await api.delete(`/notes/${todoId}`)
  todos.value = todos.value.filter((todo) => todo.id !== todoId)
}

// Función para formatear la fecha
const formatDate = (dateString) => {
  if (!dateString) return 'Sin fecha'
  const date = new Date(dateString)
  return isNaN(date.getTime())
    ? 'Fecha no válida'
    : date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' })
}

// Función para manejar cambios en la imagen
const onImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    currentTodo.value.image = file
  }
}
</script>
