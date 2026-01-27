<template>
  <!--
    Vista para editar un presupuesto.
    Permite modificar los datos principales, los artículos, etapas de pago, observaciones y el cliente asociado.
  -->
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-5xl mx-auto">
        <div class="mb-6">
          <router-link to="/budgets" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Presupuestos
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900">Editar Presupuesto</h1>
        </div>

        <!-- Loading State -->
        <div v-if="loadingBudget" class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600">Cargando datos del presupuesto...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="loadError" class="bg-red-50 border border-red-200 rounded-lg p-6">
          <p class="text-red-600">{{ loadError }}</p>
        </div>

        <!-- Edit Form -->
        <div v-else class="bg-white rounded-lg shadow-md">
          <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
            <!-- Budget Header Section -->
            <div class="border-b pb-6">
              <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Presupuesto</h2>
              
              <!-- Client Info (Editable) -->
              <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Cliente</h3>
                <select v-model="form.client_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900">
                  <option v-for="client in clients" :key="client.id" :value="client.id">
                    {{ client.name }}
                  </option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Selecciona el cliente para este presupuesto</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="budget_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Presupuesto *
                  </label>
                  <input
                    v-model.number="form.budget_number"
                    id="budget_number"
                    name="budget_number"
                    type="number"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                  />
                </div>

                <div>
                  <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Emisión *
                  </label>
                  <input
                    v-model="form.issue_date"
                    id="issue_date"
                    name="issue_date"
                    type="date"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                  />
                </div>

                <div>
                  <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Vencimiento *
                  </label>
                  <input
                    v-model="form.due_date"
                    id="due_date"
                    name="due_date"
                    type="date"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                  />
                </div>

                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Estado *
                  </label>
                  <select
                    v-model="form.status"
                    id="status"
                    name="status"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                  >
                    <option value="draft">Borrador</option>
                    <option value="sent">Enviado</option>
                    <option value="accepted">Aceptado</option>
                    <option value="rejected">Rechazado</option>
                  </select>
                </div>

                <div>
                  <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                    Tasa de Impuesto (%) *
                  </label>
                  <input
                    v-model.number="taxRate"
                    id="tax_rate"
                    name="tax_rate"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                    placeholder="e.g., 21"
                  />
                </div>
              </div>
            </div>

            <!-- Budget Items Section -->
            <div class="border-b pb-6">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Artículos del Presupuesto</h2>
                <button
                  type="button"
                  @click="addItem"
                  class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-sm font-medium"
                >
                  + Agregar Artículo
                </button>
              </div>

              <div v-if="form.items.length === 0" class="text-center py-8 text-gray-500">
                No se han agregado artículos aún. Haga clic en "Agregar Artículo" para comenzar.
              </div>

              <div v-else class="space-y-4">
                <div
                  v-for="(item, index) in form.items"
                  :key="index"
                  class="border border-gray-200 rounded-lg p-4 bg-gray-50"
                >
                  <div class="flex justify-between items-start mb-3">
                    <h3 class="font-medium text-gray-700">Artículo #{{ index + 1 }}</h3>
                    <button
                      type="button"
                      @click="removeItem(index)"
                      class="text-red-600 hover:text-red-800 transition-colors"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-5">
                      <label :for="'title_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                        Título *
                      </label>
                      <input
                        v-model="item.title"
                        :id="'title_' + index"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                        placeholder="Título del artículo"
                      />
                    </div>

                    <div class="md:col-span-7">
                      <label :for="'description_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                      </label>
                      <input
                        v-model="item.description"
                        :id="'description_' + index"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                        placeholder="Descripción detallada (opcional)"
                      />
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mt-3">

                    <div class="md:col-span-2">
                      <label :for="'quantity_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                        Cantidad *
                      </label>
                      <input
                        v-model.number="item.quantity"
                        :id="'quantity_' + index"
                        type="number"
                        min="1"
                        step="0.01"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                        placeholder="1"
                      />
                    </div>

                    <div class="md:col-span-2">
                      <label :for="'price_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                        Precio Unitario *
                      </label>
                      <input
                        v-model.number="item.price"
                        :id="'price_' + index"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                        placeholder="0.00"
                      />
                    </div>

                    <div class="md:col-span-3">
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Total
                      </label>
                      <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-900 font-medium">
                        €{{ calculateItemTotal(item).toFixed(2) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Totals Section -->
            <div class="bg-gray-50 rounded-lg p-6">
              <div class="max-w-md ml-auto space-y-3">
                <div class="flex justify-between text-gray-700">
                  <span class="font-medium">Subtotal:</span>
                  <span class="font-semibold">€{{ subtotal.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-gray-700">
                  <span class="font-medium">Impuesto ({{ taxRate }}%):</span>
                  <span class="font-semibold">€{{ tax.toFixed(2) }}</span>
                </div>
                <div class="border-t pt-3 flex justify-between text-gray-900 text-lg">
                  <span class="font-bold">Total:</span>
                  <span class="font-bold">€{{ total.toFixed(2) }}</span>
                </div>
              </div>
            </div>

            <!-- Payment Stages Section -->
            <div class="border-b pb-6">
              <h2 class="text-xl font-semibold text-gray-800 mb-4">Etapas de Pago</h2>
              <p class="text-sm text-gray-600 mb-4">Ingrese los porcentajes de pago para cada etapa (la suma no puede superar el 100%)</p>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                  <label for="payment_stage_1" class="block text-sm font-medium text-gray-700 mb-2">
                    1º Pago - Firma del Presupuesto (%)
                  </label>
                  <input
                    v-model.number="form.payment_stage_1"
                    id="payment_stage_1"
                    type="number"
                    min="0"
                    max="100"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                    placeholder="0"
                  />
                  <p class="text-sm text-gray-600 mt-1">
                    Monto: €{{ calculatePaymentAmount(form.payment_stage_1).toFixed(2) }}
                  </p>
                </div>

                <div>
                  <label for="payment_stage_2" class="block text-sm font-medium text-gray-700 mb-2">
                    2º Pago - Mitad de Obra (%)
                  </label>
                  <input
                    v-model.number="form.payment_stage_2"
                    id="payment_stage_2"
                    type="number"
                    min="0"
                    max="100"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                    placeholder="0"
                  />
                  <p class="text-sm text-gray-600 mt-1">
                    Monto: €{{ calculatePaymentAmount(form.payment_stage_2).toFixed(2) }}
                  </p>
                </div>

                <div>
                  <label for="payment_stage_3" class="block text-sm font-medium text-gray-700 mb-2">
                    3º Pago - Entrega y Finalización (%)
                  </label>
                  <input
                    v-model.number="form.payment_stage_3"
                    id="payment_stage_3"
                    type="number"
                    min="0"
                    max="100"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                    placeholder="0"
                  />
                  <p class="text-sm text-gray-600 mt-1">
                    Monto: €{{ calculatePaymentAmount(form.payment_stage_3).toFixed(2) }}
                  </p>
                </div>
              </div>

              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                  <span class="font-medium text-gray-700">Total de Pagos:</span>
                  <span :class="paymentStagesTotal > 100 ? 'text-red-600 font-bold' : 'text-gray-900 font-bold'">
                    {{ paymentStagesTotal }}%
                  </span>
                </div>
                <p v-if="paymentStagesTotal > 100" class="text-red-600 text-sm mt-2">
                  ⚠️ La suma de los porcentajes no puede superar el 100%
                </p>
              </div>
            </div>

            <!-- Observations Section -->
            <div class="border-b pb-6">
              <h2 class="text-xl font-semibold text-gray-800 mb-4">Observaciones</h2>
              <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">
                Observaciones Adicionales (Opcional)
              </label>
              <textarea
                v-model="form.observations"
                id="observations"
                rows="5"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                placeholder="Ingrese cualquier observación adicional sobre el presupuesto..."
              ></textarea>
              <p class="text-sm text-gray-500 mt-1">
                Estas observaciones aparecerán en el PDF del presupuesto
              </p>
            </div>

            <!-- Error and Success Messages -->
            <div v-if="error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-md">
              {{ error }}
            </div>

            <div v-if="success" class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-md">
              {{ success }}
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4">
              <button
                type="submit"
                :disabled="loading || form.items.length === 0"
                class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-md font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                {{ loading ? 'Actualizando...' : 'Actualizar Presupuesto' }}
              </button>
              <router-link
                to="/budgets"
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
// Importaciones de librerías y hooks de Vue
// Se usan para manejar el estado, rutas y peticiones HTTP

import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const clients = ref([])
// Lista de clientes disponibles para seleccionar en el presupuesto

const router = useRouter()
const route = useRoute()

const taxRate = ref(21)
const clientName = ref('')

const form = ref({
  // Estado reactivo que almacena los datos del formulario de edición del presupuesto
  client_id: '',
  budget_number: '',
  issue_date: '',
  due_date: '',
  status: 'draft',
  payment_stage_1: 0,
  payment_stage_2: 0,
  payment_stage_3: 0,
  observations: '',
  items: []
})

const loading = ref(false)
const loadingBudget = ref(true)
const error = ref('')
const loadError = ref('')
const success = ref('')

// Computed properties for calculations
const subtotal = computed(() => {
  // Calcula el subtotal sumando el total de cada artículo
  return form.value.items.reduce((sum, item) => {
    return sum + calculateItemTotal(item)
  }, 0)
})

const tax = computed(() => {
  // Calcula el impuesto en base al subtotal y la tasa de impuesto
  return subtotal.value * (taxRate.value / 100)
})

const total = computed(() => {
  // Calcula el total sumando el subtotal y el impuesto
  return subtotal.value + tax.value
})

const paymentStagesTotal = computed(() => {
  // Calcula la suma de los porcentajes de las etapas de pago
  return (form.value.payment_stage_1 || 0) + (form.value.payment_stage_2 || 0) + (form.value.payment_stage_3 || 0)
})

// Methods
const calculateItemTotal = (item) => {
  // Calcula el total de un artículo multiplicando cantidad por precio
  return (item.quantity || 0) * (item.price || 0)
}

const calculatePaymentAmount = (percentage) => {
  // Calcula el monto de una etapa de pago según el porcentaje y el total
  return (total.value * (percentage || 0)) / 100
}
// ...existing code...

const addItem = () => {
  // Agrega un nuevo artículo vacío al presupuesto
  form.value.items.push({
    title: '',
    description: '',
    quantity: 1,
    price: 0
  })
}

const removeItem = (index) => {
  // Elimina un artículo del presupuesto según el índice
  form.value.items.splice(index, 1)
}

const fetchBudget = async () => {
  // Función para cargar los datos del presupuesto a editar y la lista de clientes
  loadingBudget.value = true
  loadError.value = ''

  console.log('Fetching budget for edit with ID:', route.params.id)

  try {
    const response = await axios.get(`/budgets/${route.params.id}`)
    // Fetch clients for select
    const clientsResponse = await axios.get('/clients')
    clients.value = clientsResponse.data
    // Set selected client
    form.value.client_id = response.data.client_id
    
    console.log('Response:', response.data)
    
    if (response.data.status) {
      const budget = response.data.budget
      
      form.value = {
        client_id: budget.client_id,
        budget_number: budget.budget_number,
        issue_date: budget.issue_date,
        due_date: budget.due_date,
        status: budget.status,
        payment_stage_1: budget.payment_stage_1 || 0,
        payment_stage_2: budget.payment_stage_2 || 0,
        payment_stage_3: budget.payment_stage_3 || 0,
        observations: budget.observations || '',
        items: budget.budget_item.map(item => ({
          title: item.title || '',
          description: item.description,
          quantity: parseFloat(item.quantity),
          price: parseFloat(item.price)
        }))
      }
      
      clientName.value = budget.client?.name || 'Cliente Desconocido'
      
      // Calculate tax rate from existing budget
      if (budget.subtotal > 0 && budget.tax > 0) {
        taxRate.value = ((budget.tax / budget.subtotal) * 100).toFixed(2)
      }
      
      console.log('Form populated:', form.value)
    } else {
      loadError.value = 'Failed to load budget data'
    }
  } catch (err) {
    console.error('Error fetching budget:', err)
    loadError.value = err.response?.data?.message || 'An error occurred while loading budget data'
  } finally {
    loadingBudget.value = false
  }
}

const handleSubmit = async () => {
  // Función para enviar el formulario y actualizar el presupuesto en el backend
  if (form.value.items.length === 0) {
    error.value = 'Por favor, agregue al menos un artículo al presupuesto'
    return
  }

  // Validate payment stages
  if (paymentStagesTotal.value > 100) {
    error.value = 'La suma de los porcentajes de pago no puede superar el 100%'
    return
  }

  loading.value = true
  error.value = ''
  success.value = ''

  try {
    const budgetData = {
      ...form.value,
      subtotal: subtotal.value,
      tax: tax.value,
      total: total.value
    }

    const response = await axios.put(`/budgets/${route.params.id}`, budgetData)
    
    if (response.data.status) {
      success.value = '¡Presupuesto actualizado exitosamente!'
      
      // Redirect to budgets list after a short delay
      setTimeout(() => {
        router.push('/budgets')
      }, 1500)
    } else {
      error.value = response.data.message || 'Error al actualizar presupuesto'
    }
  } catch (err) {
    console.error('Error updating budget:', err)
    
    if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = errors.join(', ')
    } else {
      error.value = err.response?.data?.message || 'Ocurrió un error al actualizar el presupuesto'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  // Hook que se ejecuta al montar el componente para cargar los datos iniciales
  fetchBudget()
})
</script>
