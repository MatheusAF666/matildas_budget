<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-16">
      
      <!-- Not Authenticated View -->
      <div v-if="!isAuthenticated" class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          Bienvenido a Matilda's Budget
        </h1>
        <p class="text-xl text-gray-600 mb-8">
          Tu solución personal de gestión de presupuestos
        </p>
        
        <div class="max-w-md mx-auto space-y-4">
          <router-link 
            to="/login" 
            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-colors block"
          >
            Iniciar Sesión
          </router-link>
          
          <router-link 
            to="/register" 
            class="w-full bg-white text-indigo-600 py-3 px-6 rounded-lg text-lg font-semibold border-2 border-indigo-600 hover:bg-indigo-50 transition-colors block"
          >
            Crear Cuenta
          </router-link>
        </div>
        
        <div class="mt-12 text-gray-600">
          <h3 class="text-lg font-semibold mb-4">¿Por qué elegir Matilda's Budget?</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-white/50 rounded-lg p-4">
              <div class="text-indigo-600 text-2xl mb-2">👥</div>
              <h4 class="font-semibold">Gestión de Clientes</h4>
              <p class="text-sm">Organiza y rastrea todos tus clientes</p>
            </div>
            <div class="bg-white/50 rounded-lg p-4">
              <div class="text-indigo-600 text-2xl mb-2">💼</div>
              <h4 class="font-semibold">Planificación de Presupuestos</h4>
              <p class="text-sm">Crea presupuestos detallados de proyectos</p>
            </div>
            <div class="bg-white/50 rounded-lg p-4">
              <div class="text-indigo-600 text-2xl mb-2">📊</div>
              <h4 class="font-semibold">Seguimiento de Progreso</h4>
              <p class="text-sm">Monitorea tus objetivos financieros</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Authenticated View -->
      <div v-else class="text-center">
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 mb-2">
            ¡Bienvenido de vuelta, {{ user?.name }}! 👋
          </h1>
          <p class="text-xl text-gray-600">
            ¿Listo para gestionar tus presupuestos hoy?
          </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
          <!-- Clients Card -->
          <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
              <div class="bg-blue-100 rounded-full p-3 w-16 h-16 mx-auto mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Gestionar Clientes</h3>
              <p class="text-gray-600 mb-4">Mantén un registro de todos tus clientes y su información</p>
              <router-link to="/clients" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                Ver Clientes
              </router-link>
            </div>
          </div>

          <!-- Budgets Card -->
          <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
              <div class="bg-green-100 rounded-full p-3 w-16 h-16 mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Crear Presupuestos</h3>
              <p class="text-gray-600 mb-4">Diseña y gestiona presupuestos para tus proyectos</p>
              <router-link to="/budgets" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
                Ver Presupuestos
              </router-link>
            </div>
          </div>

          <!-- Dashboard Card -->
          <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="text-center">
              <div class="bg-purple-100 rounded-full p-3 w-16 h-16 mx-auto mb-4">
                <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Panel de Control</h3>
              <p class="text-gray-600 mb-4">Obtén una visión general de tus actividades de presupuesto</p>
              <router-link to="/dashboard" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition-colors">
                Ir al Panel de Control
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuth } from '../composables/useAuth'

const { user, isAuthenticated, checkAuth } = useAuth()

onMounted(() => {
  checkAuth()
})
</script>