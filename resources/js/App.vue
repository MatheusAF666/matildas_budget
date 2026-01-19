<template>
  <div id="app">
    <nav class="bg-gray-800 text-white shadow-lg">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <router-link to="/" class="text-xl md:text-2xl font-bold hover:text-gray-300 transition-colors flex items-center">
            <span class="hidden sm:inline">Matilda's Budget</span>
            <span class="sm:hidden">MB</span>
          </router-link>
          
          <!-- Desktop Menu -->
          <div class="hidden md:flex items-center space-x-4">
            <template v-if="isAuthenticated">
              <router-link to="/dashboard" class="hover:text-gray-300 transition-colors">
                Dashboard
              </router-link>
              <router-link to="/budgets" class="hover:text-gray-300 transition-colors">
                Presupuestos
              </router-link>
              <router-link to="/clients" class="hover:text-gray-300 transition-colors">
                Clientes
              </router-link>
              <span class="text-gray-400 text-sm">Hola, {{ user?.name }}</span>
              <button 
                @click="handleLogout"
                class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition-colors"
              >
                Cerrar Sesión
              </button>
            </template>
            <template v-else>
              <router-link to="/login" class="hover:text-gray-300 transition-colors">
                Iniciar Sesión
              </router-link>
              <router-link 
                to="/register" 
                class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded transition-colors"
              >
                Registrarse
              </router-link>
            </template>
          </div>

          <!-- Mobile Menu Button -->
          <button 
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden p-2 rounded-lg hover:bg-gray-700 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Mobile Menu -->
        <div v-show="mobileMenuOpen" class="md:hidden py-4 border-t border-gray-700">
          <template v-if="isAuthenticated">
            <router-link 
              to="/dashboard" 
              class="block py-2 px-4 hover:bg-gray-700 rounded transition-colors"
              @click="mobileMenuOpen = false"
            >
              Dashboard
            </router-link>
            <router-link 
              to="/budgets" 
              class="block py-2 px-4 hover:bg-gray-700 rounded transition-colors"
              @click="mobileMenuOpen = false"
            >
              Presupuestos
            </router-link>
            <router-link 
              to="/clients" 
              class="block py-2 px-4 hover:bg-gray-700 rounded transition-colors"
              @click="mobileMenuOpen = false"
            >
              Clientes
            </router-link>
            <div class="px-4 py-2 text-gray-400 text-sm border-t border-gray-700 mt-2">
              Hola, {{ user?.name }}
            </div>
            <button 
              @click="handleLogout"
              class="w-full text-left py-2 px-4 hover:bg-red-600 rounded transition-colors mt-2"
            >
              Cerrar Sesión
            </button>
          </template>
          <template v-else>
            <router-link 
              to="/login" 
              class="block py-2 px-4 hover:bg-gray-700 rounded transition-colors"
              @click="mobileMenuOpen = false"
            >
              Iniciar Sesión
            </router-link>
            <router-link 
              to="/register" 
              class="block py-2 px-4 bg-indigo-600 hover:bg-indigo-700 rounded transition-colors mt-2"
              @click="mobileMenuOpen = false"
            >
              Registrarse
            </router-link>
          </template>
        </div>
      </div>
    </nav>
    
    <main class="min-h-screen">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from './composables/useAuth'

const { user, isAuthenticated, logout, checkAuth } = useAuth()
const router = useRouter()
const mobileMenuOpen = ref(false)

const handleLogout = async () => {
  await logout()
  mobileMenuOpen.value = false
  router.push('/')
}

onMounted(() => {
  checkAuth()
})
</script>