<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-4 sm:py-8">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Clientes</h1>
       
        <router-link to="/clients/create" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">
          Agregar nuevo cliente
        </router-link>
      </div>

      <!-- Filtros y Búsqueda -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Barra de búsqueda -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar por nombre, DNI, email, teléfono o ciudad..."
                class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Filtro por ciudad -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
            <select
              v-model="cityFilter"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
            >
              <option value="all">Todas las ciudades</option>
              <option v-for="city in uniqueCities" :key="city" :value="city">{{ city }}</option>
            </select>
          </div>

          <!-- Ordenar por -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
            <select
              v-model="sortBy"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
            >
              <option value="name-asc">Nombre (A-Z)</option>
              <option value="name-desc">Nombre (Z-A)</option>
              <option value="recent">Más recientes</option>
            </select>
          </div>
        </div>

        <!-- Resultados y botón limpiar -->
        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
          <p class="text-sm text-gray-600">
            Mostrando <span class="font-semibold">{{ filteredClients.length }}</span> de <span class="font-semibold">{{ clients.length }}</span> clientes
          </p>
          <button
            v-if="searchQuery || cityFilter !== 'all' || sortBy !== 'name-asc'"
            @click="clearFilters"
            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
          >
            Limpiar filtros
          </button>
        </div>
      </div>
      
      <div v-if="loading" class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Cargando clientes...</p>
      </div>

      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6">
        <p class="text-red-600">{{ error }}</p>
      </div>

      <div v-else-if="clients.length === 0" class="bg-white rounded-lg shadow">
        <div class="p-6">
          <p class="text-gray-600">No se encontraron clientes. ¡Comienza agregando tu primer cliente!</p>
        </div>
      </div>

      <!-- No Results State -->
      <div v-else-if="!filteredClients.length" class="bg-white rounded-lg shadow p-12 text-center">
        <div class="mx-auto h-24 w-24 text-gray-400">
          <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
        <h3 class="mt-2 text-lg font-medium text-gray-900">No se encontraron resultados</h3>
        <p class="mt-1 text-gray-600">Intenta ajustar los filtros de búsqueda</p>
        <button @click="clearFilters" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
          Limpiar filtros
        </button>
      </div>

      <!-- Clients Grid - Mobile View -->
      <div v-else-if="filteredClients.length > 0" class="lg:hidden space-y-4">
        <div v-for="client in filteredClients" :key="client.id" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
          <div class="flex justify-between items-start mb-3">
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ client.name }}</h3>
              <p class="text-sm text-gray-600">DNI: {{ client.dni }}</p>
            </div>
          </div>

          <div class="space-y-2 mb-4 text-sm">
            <div class="flex items-center gap-2 text-gray-700">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <span class="break-all">{{ client.email || 'Sin email' }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-700">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              <span>{{ client.phone || 'Sin teléfono' }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-700">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span>{{ client.city || 'Sin ciudad' }}</span>
            </div>
          </div>

          <div class="flex gap-2 pt-3 border-t border-gray-200">
            <router-link :to="`/clients/${client.id}`" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors text-center">
              Ver
            </router-link>
            <router-link :to="`/clients/${client.id}/edit`" class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors text-center">
              Editar
            </router-link>
            <button @click="deleteClient(client.id)" class="bg-red-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Clients Table - Desktop View -->
      <div v-if="filteredClients.length > 0" class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="client in filteredClients" :key="client.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ client.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ client.dni }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ client.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ client.phone }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ client.city }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-3">
                  <router-link 
                    :to="`/clients/${client.id}`" 
                    class="text-blue-600 hover:text-blue-900 transition-colors"
                  >
                    Ver
                  </router-link>
                  <router-link 
                    :to="`/clients/${client.id}/edit`" 
                    class="text-indigo-600 hover:text-indigo-900 transition-colors"
                  >
                    Editar
                  </router-link>
                  <button 
                    @click="deleteClient(client.id)" 
                    class="text-red-600 hover:text-red-900 transition-colors"
                  >
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const clients = ref([])
const loading = ref(true)
const error = ref('')

// Filtros y búsqueda
const searchQuery = ref('')
const cityFilter = ref('all')
const sortBy = ref('name-asc')

// Obtener ciudades únicas para el filtro
const uniqueCities = computed(() => {
  const cities = clients.value.map(c => c.city).filter(Boolean)
  return [...new Set(cities)].sort()
})

// Filtrado y búsqueda
const filteredClients = computed(() => {
  let filtered = clients.value

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(client => 
      client.name?.toLowerCase().includes(query) ||
      client.dni?.toLowerCase().includes(query) ||
      client.email?.toLowerCase().includes(query) ||
      client.phone?.toLowerCase().includes(query) ||
      client.city?.toLowerCase().includes(query)
    )
  }

  // Filtrar por ciudad
  if (cityFilter.value !== 'all') {
    filtered = filtered.filter(client => client.city === cityFilter.value)
  }

  // Ordenar
  if (sortBy.value === 'name-asc') {
    filtered = [...filtered].sort((a, b) => a.name?.localeCompare(b.name))
  } else if (sortBy.value === 'name-desc') {
    filtered = [...filtered].sort((a, b) => b.name?.localeCompare(a.name))
  } else if (sortBy.value === 'recent') {
    filtered = [...filtered].sort((a, b) => b.id - a.id)
  }

  return filtered
})

const clearFilters = () => {
  searchQuery.value = ''
  cityFilter.value = 'all'
  sortBy.value = 'name-asc'
}

const fetchClients = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await axios.get('/clients')
    
    if (response.data.status) {
      clients.value = response.data.clients
    } else {
      error.value = 'Error al cargar clientes'
    }
  } catch (err) {
    console.error('Error fetching clients:', err)
    error.value = err.response?.data?.message || 'Ocurrió un error al cargar los clientes'
  } finally {
    loading.value = false
  }
}

const deleteClient = async (clientId) => {
  if (!confirm('¿Está seguro de que desea eliminar este cliente?')) {
    return
  }

  try {
    const response = await axios.delete(`/clients/${clientId}`)
    
    if (response.data.status) {
      // Remove client from the list
      clients.value = clients.value.filter(c => c.id !== clientId)
    } else {
      alert('Error al eliminar el cliente')
    }
  } catch (err) {
    console.error('Error deleting client:', err)
    alert(err.response?.data?.message || 'Ocurrió un error al eliminar el cliente')
  }
}

onMounted(() => {
  fetchClients()
})
</script>