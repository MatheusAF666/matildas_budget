<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold text-gray-900">Matilda's Budget</h1>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <a v-if="canRegister" href="/register" class="text-gray-600 hover:text-gray-900">Register</a>
            <a href="/login" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700">
              Login
            </a>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="text-center">
          <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Welcome to Matilda's Budget
          </h2>
          <p class="mt-4 text-lg text-gray-600">
            Manage your budgets and clients efficiently
          </p>
          <div class="mt-8">
            <a href="/login" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Get Started
            </a>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  canRegister: {
    type: Boolean,
    default: true
  }
});
</script>