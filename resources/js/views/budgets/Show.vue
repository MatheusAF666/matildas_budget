<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Detalles del Presupuesto</h1>
        <router-link to="/budgets" class="text-gray-600 hover:text-gray-900 transition-colors">
          ← Volver a presupuestos
        </router-link>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Cargando detalles del presupuesto...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6">
        <p class="text-red-600">{{ error }}</p>
      </div>

      <!-- Budget Details -->
      <div v-else class="space-y-6">
        <!-- Budget Info Card -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Información del Presupuesto</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Número de Presupuesto</label>
              <p class="text-lg text-gray-900">#{{ budget.budget_number }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
              <span :class="getStatusClass(budget.status)" class="inline-flex px-3 py-1 rounded-full text-sm font-semibold">
                {{ capitalizeFirst(budget.status) }}
              </span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Emisión</label>
              <p class="text-lg text-gray-900">{{ formatDate(budget.issue_date) }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
              <p class="text-lg text-gray-900">{{ formatDate(budget.due_date) }}</p>
            </div>
          </div>
        </div>

        <!-- Client Info Card -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Información del Cliente</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
              <p class="text-lg text-gray-900">{{ budget.client?.name || 'N/A' }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
              <p class="text-lg text-gray-900">{{ budget.client?.email || 'N/A' }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
              <p class="text-lg text-gray-900">{{ budget.client?.phone || 'N/A' }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
              <p class="text-lg text-gray-900">{{ budget.client?.city || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Items Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Artículos del Presupuesto</h2>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in budget.budget_item" :key="item.id">
                  <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ item.title || 'Sin título' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ item.description || '-' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ item.quantity }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">€{{ parseFloat(item.price).toFixed(2) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    €{{ (parseFloat(item.quantity) * parseFloat(item.price)).toFixed(2) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totals -->
          <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="space-y-2 max-w-xs ml-auto">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium text-gray-900">€{{ parseFloat(budget.subtotal).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Impuesto:</span>
                <span class="font-medium text-gray-900">€{{ parseFloat(budget.tax).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2">
                <span class="text-gray-900">Total:</span>
                <span class="text-indigo-600">€{{ parseFloat(budget.total).toFixed(2) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Stages Card -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Etapas de Pago</h2>
          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="border border-gray-200 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-700 mb-2">1º Pago - Firma del Presupuesto</p>
                <p class="text-2xl font-bold text-indigo-600">{{ budget.payment_stage_1 || 0 }}%</p>
                <p class="text-sm text-gray-600 mt-1">
                  €{{ ((parseFloat(budget.total) * (budget.payment_stage_1 || 0)) / 100).toFixed(2) }}
                </p>
              </div>
              
              <div class="border border-gray-200 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-700 mb-2">2º Pago - Mitad de Obra</p>
                <p class="text-2xl font-bold text-indigo-600">{{ budget.payment_stage_2 || 0 }}%</p>
                <p class="text-sm text-gray-600 mt-1">
                  €{{ ((parseFloat(budget.total) * (budget.payment_stage_2 || 0)) / 100).toFixed(2) }}
                </p>
              </div>
              
              <div class="border border-gray-200 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-700 mb-2">3º Pago - Entrega y Finalización</p>
                <p class="text-2xl font-bold text-indigo-600">{{ budget.payment_stage_3 || 0 }}%</p>
                <p class="text-sm text-gray-600 mt-1">
                  €{{ ((parseFloat(budget.total) * (budget.payment_stage_3 || 0)) / 100).toFixed(2) }}
                </p>
              </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <div class="flex justify-between items-center">
                <span class="font-medium text-gray-700">Total de Porcentajes:</span>
                <span class="text-lg font-bold text-gray-900">
                  {{ (budget.payment_stage_1 || 0) + (budget.payment_stage_2 || 0) + (budget.payment_stage_3 || 0) }}%
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
          <router-link 
            :to="`/budgets/${budget.id}/edit`" 
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
          >
            Editar Presupuesto
          </router-link>
          <button 
            @click="deleteBudget" 
            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors"
          >
            Eliminar Presupuesto
          </button>
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

const budget = ref({})
const loading = ref(true)
const error = ref('')

const fetchBudget = async () => {
  loading.value = true
  error.value = ''

  console.log('Fetching budget with ID:', route.params.id)

  try {
    const response = await axios.get(`/budgets/${route.params.id}`)
    
    console.log('Response:', response.data)
    
    if (response.data.status) {
      budget.value = response.data.budget
      console.log('Budget loaded:', budget.value)
    } else {
      error.value = 'Error al cargar los detalles del presupuesto'
    }
  } catch (err) {
    console.error('Error fetching budget:', err)
    error.value = err.response?.data?.message || 'Ocurrió un error al cargar los detalles del presupuesto'
  } finally {
    loading.value = false
  }
}

const deleteBudget = async () => {
  if (!confirm('¿Está seguro de que desea eliminar este presupuesto?')) {
    return
  }

  try {
    const response = await axios.delete(`/budgets/${route.params.id}`)
    
    if (response.data.status) {
      alert('Presupuesto eliminado exitosamente')
      router.push('/budgets')
    } else {
      alert('Error al eliminar presupuesto')
    }
  } catch (err) {
    console.error('Error deleting budget:', err)
    alert(err.response?.data?.message || 'Ocurrió un error al eliminar el presupuesto')
  }
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' })
}

const getStatusClass = (status) => {
  const statusClasses = {
    draft: 'bg-gray-100 text-gray-800',
    sent: 'bg-blue-100 text-blue-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return statusClasses[status] || 'bg-gray-100 text-gray-800'
}

const capitalizeFirst = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

onMounted(() => {
  fetchBudget()
})
</script>
