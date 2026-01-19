<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-4 sm:py-8">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 sm:mb-6">Dashboard</h1>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Stats Cards -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Clientes</h3>
          <p class="text-3xl font-bold text-blue-600">{{ stats.totalClients }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Presupuestos Activos</h3>
          <p class="text-3xl font-bold text-green-600">{{ stats.activeBudgets }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Último Presupuesto</h3>
          <div v-if="stats.lastBudget">
            <p class="text-2xl font-bold text-purple-600">#{{ stats.lastBudget.budget_number }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ stats.lastBudget.client_name }}</p>
            <p class="text-xs text-gray-500">{{ stats.lastBudget.created_at }} - €{{ stats.lastBudget.total }}</p>
          </div>
          <p v-else class="text-sm text-gray-500">No hay presupuestos</p>
        </div>
      </div>

      <!-- Company Settings -->
      <div class="mt-6 sm:mt-8 bg-white rounded-lg shadow p-4 sm:p-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Configuración de Empresa</h2>
        <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Personaliza la información que aparecerá en tus presupuestos</p>
        
        <form @submit.prevent="saveCompanySettings" class="space-y-6">
          <!-- Company Name -->
          <div>
            <label for="companyName" class="block text-sm font-medium text-gray-700 mb-2">
              Nombre de la Empresa
            </label>
            <input
              id="companyName"
              v-model="companySettings.name"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
              placeholder="Reformas Integrales Neto"
            />
          </div>

          <!-- Company Logo -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Logo de la Empresa
            </label>
            
            <!-- Logo Preview -->
            <div v-if="companySettings.logoPreview || companySettings.logo" class="mb-4">
              <div class="relative inline-block">
                <img 
                  :src="companySettings.logoPreview || companySettings.logo" 
                  alt="Logo preview" 
                  class="h-32 w-auto border-2 border-gray-300 rounded-lg"
                />
                <button
                  type="button"
                  @click="removeLogo"
                  class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- File Input -->
            <div class="flex items-center space-x-4">
              <label class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-600">Seleccionar imagen</span>
                <input
                  type="file"
                  accept="image/*"
                  @change="handleFileUpload"
                  class="hidden"
                />
              </label>
              <span class="text-xs text-gray-500">PNG, JPG hasta 2MB</span>
            </div>
          </div>

          <!-- Additional Company Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="companyPhone" class="block text-sm font-medium text-gray-700 mb-2">
                Teléfono
              </label>
              <input
                id="companyPhone"
                v-model="companySettings.phone"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                placeholder="+34 633 17 91 35"
              />
            </div>

            <div>
              <label for="companyEmail" class="block text-sm font-medium text-gray-700 mb-2">
                Email
              </label>
              <input
                id="companyEmail"
                v-model="companySettings.email"
                type="email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                placeholder="info@empresa.com"
              />
            </div>

            <div>
              <label for="companyAddress" class="block text-sm font-medium text-gray-700 mb-2">
                Dirección
              </label>
              <input
                id="companyAddress"
                v-model="companySettings.address"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                placeholder="AV. Principado 32, 2º D"
              />
            </div>

            <div>
              <label for="companyWebsite" class="block text-sm font-medium text-gray-700 mb-2">
                Sitio Web
              </label>
              <input
                id="companyWebsite"
                v-model="companySettings.website"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                placeholder="www.empresa.com"
              />
            </div>

            <div>
              <label for="defaultDueDays" class="block text-sm font-medium text-gray-700 mb-2">
                Días de Vencimiento (Por Defecto)
              </label>
              <input
                id="defaultDueDays"
                v-model.number="companySettings.defaultDueDays"
                type="number"
                min="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                placeholder="31"
              />
              <p class="text-xs text-gray-500 mt-1">Días después de la fecha de emisión para calcular la fecha de vencimiento</p>
            </div>
          </div>

          <!-- Error/Success Messages -->
          <div v-if="message" :class="[
            'p-4 rounded-lg',
            messageType === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'
          ]">
            {{ message }}
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
              {{ saving ? 'Guardando...' : 'Guardar Configuración' }}
            </button>
          </div>
        </form>
      </div>

      <div class="mt-6 sm:mt-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Acciones Rápidas</h2>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
          <router-link to="/clients" class="w-full sm:w-auto bg-blue-600 text-white px-4 sm:px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors text-center">
            Gestionar Clientes
          </router-link>
          <router-link to="/budgets" class="w-full sm:w-auto bg-green-600 text-white px-4 sm:px-6 py-3 rounded-lg hover:bg-green-700 transition-colors text-center">
            Ver Presupuestos
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({
  totalClients: 0,
  activeBudgets: 0,
  lastBudget: null
})

const companySettings = ref({
  name: '',
  logo: null,
  logoPreview: null,
  phone: '',
  email: '',
  address: '',
  website: '',
  defaultDueDays: 31
})

const saving = ref(false)
const message = ref('')
const messageType = ref('')

const loadStats = async () => {
  try {
    const response = await axios.get('/user-stats')
    stats.value = {
      totalClients: response.data.clients,
      activeBudgets: response.data.budgets,
      lastBudget: response.data.lastBudget
    }
  } catch (err) {
    console.error('Error loading stats:', err)
  }
}

const loadCompanySettings = async () => {
  try {
    const response = await axios.get('/company-settings')
    if (response.data.data) {
      companySettings.value = {
        ...companySettings.value,
        ...response.data.data
      }
    }
  } catch (err) {
    console.error('Error loading company settings:', err)
  }
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validar tamaño
    if (file.size > 2 * 1024 * 1024) {
      message.value = 'La imagen debe ser menor a 2MB'
      messageType.value = 'error'
      return
    }

    // Validar tipo
    if (!file.type.startsWith('image/')) {
      message.value = 'Por favor selecciona una imagen válida'
      messageType.value = 'error'
      return
    }

    // Crear preview
    const reader = new FileReader()
    reader.onload = (e) => {
      companySettings.value.logoPreview = e.target.result
    }
    reader.readAsDataURL(file)

    // Guardar archivo para enviar
    companySettings.value.logoFile = file
  }
}

const removeLogo = () => {
  companySettings.value.logo = null
  companySettings.value.logoPreview = null
  companySettings.value.logoFile = null
}

const saveCompanySettings = async () => {
  saving.value = true
  message.value = ''
  
  try {
    const formData = new FormData()
    formData.append('name', companySettings.value.name || '')
    formData.append('phone', companySettings.value.phone || '')
    formData.append('email', companySettings.value.email || '')
    formData.append('address', companySettings.value.address || '')
    formData.append('website', companySettings.value.website || '')
    formData.append('default_due_days', companySettings.value.defaultDueDays || 31)
    
    if (companySettings.value.logoFile) {
      formData.append('logo', companySettings.value.logoFile)
    }

    const response = await axios.post('/company-settings', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    message.value = 'Configuración guardada exitosamente'
    messageType.value = 'success'

    // Actualizar logo si la respuesta incluye la URL
    if (response.data.data?.logo) {
      companySettings.value.logo = response.data.data.logo
      companySettings.value.logoPreview = null
    }

    setTimeout(() => {
      message.value = ''
    }, 3000)
  } catch (err) {
    console.error('Error saving company settings:', err)
    message.value = err.response?.data?.message || 'Error al guardar la configuración'
    messageType.value = 'error'
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadStats()
  loadCompanySettings()
})
</script>