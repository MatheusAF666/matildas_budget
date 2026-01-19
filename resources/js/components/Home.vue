<template>
  <div class="home">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Welcome to Budget App</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
        <div class="space-y-2">
          <button 
            @click="loadBudgets"
            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded"
          >
            Load Budgets
          </button>
          <button 
            @click="loadClients"
            class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded"
          >
            Load Clients
          </button>
        </div>
      </div>
      
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Data</h2>
        <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-48">{{ data }}</pre>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Home',
  data() {
    return {
      data: 'No data loaded yet...'
    }
  },
  methods: {
    async loadBudgets() {
      try {
        const response = await this.$http.get('/budgets');
        this.data = response.data;
      } catch (error) {
        this.data = 'Error loading budgets: ' + error.message;
      }
    },
    async loadClients() {
      try {
        const response = await this.$http.get('/clients');
        this.data = response.data;
      } catch (error) {
        this.data = 'Error loading clients: ' + error.message;
      }
    }
  }
}
</script>