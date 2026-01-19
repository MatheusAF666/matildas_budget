<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-md w-full space-y-8 bg-white p-6 sm:p-10 rounded-2xl shadow-xl">
      <div>
        <h2 class="text-center text-2xl sm:text-3xl font-extrabold text-gray-900">
          Crea tu cuenta
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Comienza a gestionar tus presupuestos hoy
        </p>
      </div>
      <form class="mt-8 space-y-5" @submit.prevent="handleRegister">
        <div class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
            <input
              v-model="form.name"
              id="name"
              name="name"
              type="text"
              required
              class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
              placeholder="Juan Pérez"
            />
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input
              v-model="form.email"
              id="email"
              name="email"
              type="email"
              required
              class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
              placeholder="tu@email.com"
            />
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input
              v-model="form.password"
              id="password"
              name="password"
              type="password"
              required
              class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
              placeholder="••••••••"
            />
          </div>
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
            <input
              v-model="form.password_confirmation"
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
              placeholder="••••••••"
            />
          </div>
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-3">
          <p class="text-red-600 text-sm">{{ error }}</p>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-[1.02]"
          >
            <span v-if="loading" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Creando cuenta...
            </span>
            <span v-else>Registrarse</span>
          </button>
        </div>

        <div class="text-center pt-4 border-t border-gray-200">
          <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500 text-sm sm:text-base">
            ¿Ya tienes una cuenta? Inicia sesión
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../../composables/useAuth'

const { register, loading } = useAuth()
const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const error = ref('')

const handleRegister = async () => {
  error.value = ''
  
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Las contraseñas no coinciden'
    return
  }
  
  const result = await register(form.value)
  
  if (result.success) {
    router.push('/dashboard')
  } else {
    error.value = result.message
  }
}
</script>