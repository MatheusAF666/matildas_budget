<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Detalles del Cliente</h1>
        <router-link to="/clients" class="text-gray-600 hover:text-gray-900 transition-colors">
          ← Volver a clientes
        </router-link>
      </div>

      <div v-if="loading" class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Cargando detalles del cliente...</p>
      </div>

      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6">
        <p class="text-red-600">{{ error }}</p>
      </div>

      <div v-else class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
              <p class="text-lg text-gray-900">{{ client.name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
              <p class="text-lg text-gray-900">{{ client.dni }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <p class="text-lg text-gray-900">{{ client.email }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
              <p class="text-lg text-gray-900">{{ client.phone }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Calle</label>
              <p class="text-lg text-gray-900">{{ client.street || 'N/A' }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
              <p class="text-lg text-gray-900">{{ client.number || 'N/A' }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Piso</label>
              <p class="text-lg text-gray-900">{{ client.floor || 'N/A' }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Puerta</label>
              <p class="text-lg text-gray-900">{{ client.door || 'N/A' }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
              <p class="text-lg text-gray-900">{{ client.city || 'N/A' }}</p>
            </div>
          </div>

          <div class="mt-8 flex gap-4">
            <router-link 
              :to="`/clients/${client.id}/edit`" 
              class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
              Editar Cliente
            </router-link>
            <button 
              @click="deleteClient" 
              class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors"
            >
              Eliminar Cliente
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const client = ref({})
const loading = ref(true)
const error = ref('')

const fetchClient = async () => {
  loading.value = true
  error.value = ''

  console.log('Fetching client with ID:', route.params.id)

  try {
    const response = await axios.get(`/clients/${route.params.id}`)
    
    console.log('Response:', response.data)
    
    if (response.data.status) {
      client.value = response.data.client
      console.log('Client loaded:', client.value)
    } else {
      error.value = 'Error al cargar los detalles del cliente'
    }
  } catch (err) {
    console.error('Error fetching client:', err)
    error.value = err.response?.data?.message || 'Ocurrió un error al cargar los detalles del cliente'
  } finally {
    loading.value = false
  }
}

const deleteClient = async () => {
  if (!confirm('¿Está seguro de que desea eliminar este cliente?')) {
    return
  }

  try {
    const response = await axios.delete(`/clients/${route.params.id}`)
    
    if (response.data.status) {
      alert('Cliente eliminado exitosamente')
      router.push('/clients')
    } else {
      alert('Error al eliminar el cliente')
    }
  } catch (err) {
    console.error('Error deleting client:', err)
    alert(err.response?.data?.message || 'Ocurrió un error al eliminar el cliente')
  }
}

onMounted(() => {
  fetchClient()
})
</script>
