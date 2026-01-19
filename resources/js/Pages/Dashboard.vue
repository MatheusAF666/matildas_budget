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
            <span class="text-gray-700">Welcome back!</span>
            <form method="POST" action="/logout" class="inline">
              <button type="submit" class="text-gray-600 hover:text-gray-900">
                Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Clients Card -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Clients</dt>
                    <dd class="text-lg font-medium text-gray-900">0</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
              <div class="text-sm">
                <a href="/clients" class="font-medium text-indigo-600 hover:text-indigo-500">
                  Manage clients
                </a>
              </div>
            </div>
          </div>

          <!-- Budgets Card -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Budgets</dt>
                    <dd class="text-lg font-medium text-gray-900">0</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
              <div class="text-sm">
                <a href="/budgets" class="font-medium text-indigo-600 hover:text-indigo-500">
                  Manage budgets
                </a>
              </div>
            </div>
          </div>

          <!-- Quick Actions Card -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
              <div class="mt-4 space-y-2">
                <a href="/clients/create" class="block text-sm text-indigo-600 hover:text-indigo-500">
                  + Add new client
                </a>
                <a href="/budgets/create" class="block text-sm text-indigo-600 hover:text-indigo-500">
                  + Create budget
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
// No props needed for dashboard
</script>