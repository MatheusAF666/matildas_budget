<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-2xl mx-auto">
        <div class="mb-6">
          <router-link to="/clients" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Clientes
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900">Agregar Nuevo Cliente</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md">
          <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nombre del Cliente *
              </label>
              <input
                v-model="form.name"
                id="name"
                name="name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                placeholder="Ingrese el nombre del cliente"
              />
            </div>

            <div>
              <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">
                DNI *
              </label>
              <input
                v-model="form.dni"
                id="dni"
                name="dni"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                placeholder="Ingrese el número de DNI"
              />
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Dirección de Email *
              </label>
              <input
                v-model="form.email"
                id="email"
                name="email"
                type="email"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                placeholder="cliente@ejemplo.com"
              />
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                Número de Teléfono *
              </label>
              <input
                v-model="form.phone"
                id="phone"
                name="phone"
                type="tel"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                placeholder="+54 (11) 1234-5678"
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="street" class="block text-sm font-medium text-gray-700 mb-2">
                  Calle *
                </label>
                <input
                  v-model="form.street"
                  id="street"
                  name="street"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                  placeholder="Nombre de la calle"
                />
              </div>

              <div>
                <label for="number" class="block text-sm font-medium text-gray-700 mb-2">
                  Número *
                </label>
                <input
                  v-model="form.number"
                  id="number"
                  name="number"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                  placeholder="Número"
                />
              </div>

              <div>
                <label for="floor" class="block text-sm font-medium text-gray-700 mb-2">
                  Piso
                </label>
                <input
                  v-model="form.floor"
                  id="floor"
                  name="floor"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                  placeholder="Piso (opcional)"
                />
              </div>

              <div>
                <label for="door" class="block text-sm font-medium text-gray-700 mb-2">
                  Puerta
                </label>
                <input
                  v-model="form.door"
                  id="door"
                  name="door"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                  placeholder="Puerta (opcional)"
                />
              </div>
            </div>

            <div>
              <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                Ciudad *
              </label>
              <input
                v-model="form.city"
                id="city"
                name="city"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                placeholder="Ingrese la ciudad"
              />
            </div>

            <div v-if="error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
              {{ error }}
            </div>

            <div v-if="success" class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md">
              {{ success }}
            </div>

            <div class="flex gap-4">
              <button
                type="submit"
                :disabled="loading"
                class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-md font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                {{ loading ? 'Creando...' : 'Crear Cliente' }}
              </button>
              <router-link
                to="/clients"
                class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-md font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-center"
              >
                Cancelar
              </router-link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const form = ref({
  name: '',
  dni: '',
  email: '',
  phone: '',
  street: '',
  number: '',
  floor: '',
  door: '',
  city: ''
})

const loading = ref(false)
const error = ref('')
const success = ref('')

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  success.value = ''

  try {
    const response = await axios.post('/clients-new', form.value)
    
    if (response.data.status) {
      success.value = '¡Cliente creado exitosamente!'
      
      // Redirect to clients list after a short delay
      setTimeout(() => {
        router.push('/clients')
      }, 1500)
    } else {
      error.value = response.data.message || 'Error al crear el cliente'
    }
  } catch (err) {
    console.error('Error creating client:', err)
    error.value = err.response?.data?.message || 'Ocurrió un error al crear el cliente'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
</style>