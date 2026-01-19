<template>
  <div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <h1 class="text-6xl font-bold text-gray-700 mb-4">404</h1>
      <h2 class="text-2xl font-semibold text-gray-600 mb-4">Página No Encontrada</h2>
      <p class="text-gray-500 mb-8">La página que buscas no existe.</p>
      <router-link 
        :to="{ name: 'home' }" 
        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors"
      >
        Volver al Inicio
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound'
};
</script>