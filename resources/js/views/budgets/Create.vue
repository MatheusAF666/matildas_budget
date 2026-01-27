<template>
  <!--
    Vista para crear un nuevo presupuesto.
    Permite seleccionar o crear un cliente, agregar artículos, etapas de pago y observaciones.
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
          <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Presupuesto</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md">
          <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
            <!-- Budget Header Section -->
            <div class="border-b pb-6">
              <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Presupuesto</h2>
              
              <!-- Client Type Selection -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Tipo de Cliente *
                </label>
                <div class="flex gap-6">
                  <label class="flex items-center cursor-pointer">
                    <input
                      type="radio"
                      v-model="clientType"
                      value="existing"
                      class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ml-2 text-gray-700">Cliente Existente</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      type="radio"
                      v-model="clientType"
                      value="new"
                      class="w-4 h-4 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ml-2 text-gray-700">Nuevo Cliente</span>
                  </label>
                </div>
              </div>

              <!-- Existing Client Selection -->
              <div v-if="clientType === 'existing'" class="mb-6">
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Seleccionar Cliente *
                </label>
                <select
                  v-model="form.client_id"
                  id="client_id"
                  name="client_id"
                  :required="clientType === 'existing'"
                  @change="onClientSelect"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                >
                  <option value="">Seleccionar un cliente</option>
                  <option v-for="client in clients" :key="client.id" :value="client.id">
                    {{ client.name }} - {{ client.email }}
                  </option>
                </select>
              </div>

              <!-- Budget Address Section -->
              <div v-if="clientType === 'existing' && form.client_id" class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Dirección del Presupuesto</h3>
                <p class="text-sm text-gray-600 mb-4">Puede modificar la dirección para este presupuesto (no afectará la dirección del cliente)</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="budget_street" class="block text-sm font-medium text-gray-700 mb-2">
                      Calle
                    </label>
                    <input
                      v-model="form.street"
                      id="budget_street"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Nombre de la calle"
                    />
                  </div>

                  <div>
                    <label for="budget_number" class="block text-sm font-medium text-gray-700 mb-2">
                      Número
                    </label>
                    <input
                      v-model="form.number"
                      id="budget_number_address"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Número"
                    />
                  </div>

                  <div>
                    <label for="budget_floor" class="block text-sm font-medium text-gray-700 mb-2">
                      Piso
                    </label>
                    <input
                      v-model="form.floor"
                      id="budget_floor"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Piso (opcional)"
                    />
                  </div>

                  <div>
                    <label for="budget_door" class="block text-sm font-medium text-gray-700 mb-2">
                      Puerta
                    </label>
                    <input
                      v-model="form.door"
                      id="budget_door"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Puerta (opcional)"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label for="budget_city" class="block text-sm font-medium text-gray-700 mb-2">
                      Ciudad
                    </label>
                    <input
                      v-model="form.city"
                      id="budget_city"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Ingresar ciudad"
                    />
                  </div>
                </div>
              </div>

              <!-- New Client Form -->
              <div v-if="clientType === 'new'" class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Nuevo Cliente</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="new_client_name" class="block text-sm font-medium text-gray-700 mb-2">
                      Nombre del Cliente *
                    </label>
                    <input
                      v-model="newClient.name"
                      id="new_client_name"
                      type="text"
                      :required="clientType === 'new'"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Ingresar nombre del cliente"
                    />
                  </div>

                  <div>
                    <label for="new_client_dni" class="block text-sm font-medium text-gray-700 mb-2">
                      DNI
                    </label>
                    <input
                      v-model="newClient.dni"
                      id="new_client_dni"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Ingresar número de DNI (opcional)"
                    />
                  </div>

                  <div>
                    <label for="new_client_email" class="block text-sm font-medium text-gray-700 mb-2">
                      Correo Electrónico
                    </label>
                    <input
                      v-model="newClient.email"
                      id="new_client_email"
                      type="email"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="cliente@ejemplo.com"
                    />
                  </div>

                  <div>
                    <label for="new_client_phone" class="block text-sm font-medium text-gray-700 mb-2">
                      Número de Teléfono
                    </label>
                    <input
                      v-model="newClient.phone"
                      id="new_client_phone"
                      type="tel"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="+34 (555) 123-4567"
                    />
                  </div>

                  <div>
                    <label for="new_client_street" class="block text-sm font-medium text-gray-700 mb-2">
                      Calle
                    </label>
                    <input
                      v-model="newClient.street"
                      id="new_client_street"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Nombre de la calle (opcional)"
                    />
                  </div>

                  <div>
                    <label for="new_client_number" class="block text-sm font-medium text-gray-700 mb-2">
                      Número
                    </label>
                    <input
                      v-model="newClient.number"
                      id="new_client_number"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Número (opcional)"
                    />
                  </div>

                  <div>
                    <label for="new_client_floor" class="block text-sm font-medium text-gray-700 mb-2">
                      Piso
                    </label>
                    <input
                      v-model="newClient.floor"
                      id="new_client_floor"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Piso (opcional)"
                    />
                  </div>

                  <div>
                    <label for="new_client_door" class="block text-sm font-medium text-gray-700 mb-2">
                      Puerta
                    </label>
                    <input
                      v-model="newClient.door"
                      id="new_client_door"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Puerta (opcional)"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label for="new_client_city" class="block text-sm font-medium text-gray-700 mb-2">
                      Ciudad *
                    </label>
                    <input
                      v-model="newClient.city"
                      id="new_client_city"
                      type="text"
                      :required="clientType === 'new'"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
                      placeholder="Ingresar ciudad"
                    />
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="budget_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Presupuesto
                  </label>
                  <input
                    :value="nextBudgetNumber"
                    id="budget_number"
                    name="budget_number"
                    type="number"
                    readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed"
                  />
                  <p class="text-xs text-gray-500 mt-1">Se asignará automáticamente</p>
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
                  <p class="text-xs text-gray-500 mt-1">Se calcula automáticamente (+{{ defaultDueDays }} días desde emisión)</p>
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
                {{ loading ? 'Creando...' : 'Crear Presupuesto' }}
              </button>
              <button
                type="button"
                @click="downloadPDF"
                :disabled="form.items.length === 0"
                class="flex-1 bg-green-600 text-white py-2 px-4 rounded-md font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Descargar PDF
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
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const clients = ref([])
const taxRate = ref(21)
const clientType = ref('existing')
const defaultDueDays = ref(31)
const nextBudgetNumber = ref(1)

const form = ref({
  client_id: '',
  issue_date: new Date().toISOString().split('T')[0],
  due_date: '',
  status: 'draft',
  street: '',
  number: '',
  floor: '',
  door: '',
  city: '',
  payment_stage_1: 0,
  payment_stage_2: 0,
  payment_stage_3: 0,
  observations: '',
  items: []
})

const newClient = ref({
  name: '',
  dni: '',
  email: '',
  phone: '',
  street: '',
  number: '',
  floor: '',
  door: '',
  city: ''
})

const loading = ref(false)
const error = ref('')
const success = ref('')

// Watch para calcular automáticamente la fecha de vencimiento
watch(() => form.value.issue_date, (newIssueDate) => {
  if (newIssueDate && defaultDueDays.value) {
    const issueDate = new Date(newIssueDate)
    issueDate.setDate(issueDate.getDate() + defaultDueDays.value)
    form.value.due_date = issueDate.toISOString().split('T')[0]
  }
})

// Computed properties for calculations
const subtotal = computed(() => {
  return form.value.items.reduce((sum, item) => {
    return sum + calculateItemTotal(item)
  }, 0)
})

const tax = computed(() => {
  return subtotal.value * (taxRate.value / 100)
})

const total = computed(() => {
  return subtotal.value + tax.value
})

const paymentStagesTotal = computed(() => {
  return (form.value.payment_stage_1 || 0) + (form.value.payment_stage_2 || 0) + (form.value.payment_stage_3 || 0)
})

// Methods
const calculateItemTotal = (item) => {
  return (item.quantity || 0) * (item.price || 0)
}

const calculatePaymentAmount = (percentage) => {
  return (total.value * (percentage || 0)) / 100
}

const onClientSelect = () => {
  // Find the selected client and pre-fill address fields
  const selectedClient = clients.value.find(c => c.id === form.value.client_id)
  if (selectedClient) {
    form.value.street = selectedClient.street || ''
    form.value.number = selectedClient.number || ''
    form.value.floor = selectedClient.floor || ''
    form.value.door = selectedClient.door || ''
    form.value.city = selectedClient.city || ''
  }
}

const addItem = () => {
  form.value.items.push({
    title: '',
    description: '',
    quantity: 1,
    price: 0
  })
}

const removeItem = (index) => {
  form.value.items.splice(index, 1)
}

const loadClients = async () => {
  try {
    const response = await axios.get('/clients')
    if (response.data.status) {
      clients.value = response.data.clients || []
    } else {
      error.value = 'No hay clientes disponibles. Por favor, cree un cliente primero.'
    }
  } catch (err) {
    console.error('Error loading clients:', err)
    if (err.response?.status === 401) {
      error.value = 'Debe iniciar sesión para crear presupuestos.'
      setTimeout(() => {
        router.push('/login')
      }, 2000)
    } else if (err.response?.status === 419) {
      error.value = 'Sesión expirada. Por favor, actualice la página e inicie sesión nuevamente.'
    } else {
      error.value = err.response?.data?.message || 'Error al cargar clientes. Por favor, actualice la página.'
    }
  }
}

const loadCompanySettings = async () => {
  try {
    const response = await axios.get('/company-settings')
    if (response.data.data?.defaultDueDays) {
      defaultDueDays.value = response.data.data.defaultDueDays
      // Calcular la fecha de vencimiento inicial si ya hay fecha de emisión
      if (form.value.issue_date) {
        const issueDate = new Date(form.value.issue_date)
        issueDate.setDate(issueDate.getDate() + defaultDueDays.value)
        form.value.due_date = issueDate.toISOString().split('T')[0]
      }
    }
  } catch (err) {
    console.error('Error loading company settings:', err)
    // Usar valor por defecto si hay error
    defaultDueDays.value = 31
  }
}

const loadNextBudgetNumber = async () => {
  try {
    const response = await axios.get('/budgets')
    if (response.data.status && response.data.budgets?.length > 0) {
      // Encontrar el número máximo de presupuesto
      const maxBudgetNumber = Math.max(...response.data.budgets.map(b => b.budget_number || 0))
      nextBudgetNumber.value = maxBudgetNumber + 1
    } else {
      nextBudgetNumber.value = 1
    }
  } catch (err) {
    console.error('Error loading next budget number:', err)
    nextBudgetNumber.value = 1
  }
}

const handleSubmit = async () => {
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
    let clientId = form.value.client_id

    // If creating a new client, create it first
    if (clientType.value === 'new') {
      try {
        const clientResponse = await axios.post('/clients-new', newClient.value)
        
        if (clientResponse.data.status && clientResponse.data.client) {
          clientId = clientResponse.data.client.id
          success.value = '¡Cliente creado exitosamente! Creando presupuesto...'
          
          // Use the new client's address for the budget
          form.value.street = newClient.value.street
          form.value.number = newClient.value.number
          form.value.floor = newClient.value.floor
          form.value.door = newClient.value.door
          form.value.city = newClient.value.city
        } else {
          error.value = clientResponse.data.message || 'Error al crear cliente'
          loading.value = false
          return
        }
      } catch (clientErr) {
        console.error('Error creating client:', clientErr)
        error.value = clientErr.response?.data?.message || 'Error al crear cliente'
        loading.value = false
        return
      }
    }

    // Create the budget with the client ID
    const budgetData = {
      ...form.value,
      client_id: clientId,
      subtotal: subtotal.value,
      tax: tax.value,
      total: total.value
    }

    const response = await axios.post('/budgets-new', budgetData)
    
    if (response.data.status) {
      success.value = clientType.value === 'new' 
        ? '¡Cliente y presupuesto creados exitosamente!' 
        : '¡Presupuesto creado exitosamente!'
      
      // Redirect to budgets list after a short delay
      setTimeout(() => {
        router.push('/budgets')
      }, 1500)
    } else {
      error.value = response.data.message || 'Error al crear presupuesto'
    }
  } catch (err) {
    console.error('Error creating budget:', err)
    error.value = err.response?.data?.message || 'Ocurrió un error al crear el presupuesto'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadClients()
  loadCompanySettings()
  loadNextBudgetNumber()
})
</script>

<style scoped>
</style>
