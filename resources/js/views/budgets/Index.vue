<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import jsPDF from 'jspdf'

const router = useRouter()
const budgets = ref([])
const loading = ref(true)
const error = ref(null)
const showDeleteModal = ref(false)
const budgetToDelete = ref(null)
const deleting = ref(false)
const sendingEmail = ref({}) // Para trackear qué presupuesto está enviando email

// Filtros y búsqueda
const searchQuery = ref('')
const statusFilter = ref('all')
const sortBy = ref('date-desc')

const loadBudgets = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await axios.get('/budgets')
    budgets.value = response.data.budgets || []
  } catch (err) {
    console.error('Error loading budgets:', err)
    error.value = err.response?.data?.message || 'Error al cargar presupuestos. Por favor, intente de nuevo.'
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' })
}

const getStatusClass = (status) => {
  const statusClasses = {
    draft: 'bg-gray-100 text-gray-800',
    sent: 'bg-blue-100 text-blue-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return statusClasses[status] || 'bg-gray-100 text-gray-800'
}

const capitalizeFirst = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

// Filtrado y búsqueda
const filteredBudgets = computed(() => {
  let filtered = budgets.value

  // Filtrar por búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(budget => 
      budget.budget_number?.toString().toLowerCase().includes(query) ||
      budget.client?.name?.toLowerCase().includes(query) ||
      budget.client?.email?.toLowerCase().includes(query)
    )
  }

  // Filtrar por estado
  if (statusFilter.value !== 'all') {
    filtered = filtered.filter(budget => budget.status === statusFilter.value)
  }

  // Ordenar
  if (sortBy.value === 'date-desc') {
    filtered = [...filtered].sort((a, b) => new Date(b.issue_date) - new Date(a.issue_date))
  } else if (sortBy.value === 'date-asc') {
    filtered = [...filtered].sort((a, b) => new Date(a.issue_date) - new Date(b.issue_date))
  } else if (sortBy.value === 'total-desc') {
    filtered = [...filtered].sort((a, b) => parseFloat(b.total) - parseFloat(a.total))
  } else if (sortBy.value === 'total-asc') {
    filtered = [...filtered].sort((a, b) => parseFloat(a.total) - parseFloat(b.total))
  }

  return filtered
})

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = 'all'
  sortBy.value = 'date-desc'
}

const viewBudget = (id) => {
  router.push(`/budgets/${id}`)
}

const editBudget = (id) => {
  router.push(`/budgets/${id}/edit`)
}

const sendEmail = async (budget) => {
  if (!budget.client?.email) {
    error.value = 'El cliente no tiene dirección de correo electrónico'
    setTimeout(() => error.value = null, 3000)
    return
  }

  sendingEmail.value[budget.id] = true
  error.value = null

  try {
    const response = await axios.post(`/budgets/${budget.id}/send-email`)
    
    if (response.data.status) {
      // Mostrar mensaje de éxito
      const successMessage = response.data.message
      alert(`✅ ${successMessage}`)
    } else {
      error.value = response.data.message || 'Error al enviar el correo'
    }
  } catch (err) {
    console.error('Error sending email:', err)
    error.value = err.response?.data?.message || 'Error al enviar el correo. Por favor, intente de nuevo.'
  } finally {
    sendingEmail.value[budget.id] = false
  }
}

const downloadPDF = async (id) => {
  try {
    // Obtener datos del presupuesto y configuración de empresa
    const [budgetResponse, companyResponse] = await Promise.all([
      axios.get(`/budgets/${id}`),
      axios.get('/company-settings')
    ])
    
    const budget = budgetResponse.data.budget
    const company = companyResponse.data.data || {}
    
    console.log('Company settings:', company)
    
    const doc = new jsPDF()
    const pageWidth = doc.internal.pageSize.getWidth()
    const pageHeight = doc.internal.pageSize.getHeight()
    
    // Colores del diseño original
    const azulOscuro = [40, 34, 94]  // #28225e
    const azulClaro = [8, 174, 188]  // #08aebc
    const grisClaro = [231, 231, 233]  // #e7e7e9
    const rojoLinea = [220, 20, 60]  // Rojo más fuerte (Crimson)
    
    // ========== HEADER - Logo y datos de la empresa ==========
    let y = 20
    
    // Logo de la empresa si existe
    if (company.logo) {
      try {
        // Convertir URL relativa a absoluta
        const logoUrl = company.logo.startsWith('http') 
          ? company.logo 
          : window.location.origin + company.logo
        
        console.log('Loading logo from:', logoUrl)
        
        // Cargar imagen y convertir a base64
        const response = await fetch(logoUrl)
        if (!response.ok) {
          throw new Error(`Failed to fetch logo: ${response.status} ${response.statusText}`)
        }
        
        const blob = await response.blob()
        console.log('Blob loaded, type:', blob.type, 'size:', blob.size)
        
        const base64 = await new Promise((resolve, reject) => {
          const reader = new FileReader()
          reader.onloadend = () => {
            console.log('Base64 conversion complete, length:', reader.result?.length)
            resolve(reader.result)
          }
          reader.onerror = (err) => {
            console.error('FileReader error:', err)
            reject(err)
          }
          reader.readAsDataURL(blob)
        })
        
        console.log('Logo loaded successfully as base64')
        
        // Crear imagen temporal para obtener dimensiones
        const img = new Image()
        img.crossOrigin = 'anonymous' // Intentar evitar problemas de CORS
        
        await new Promise((resolve, reject) => {
          img.onload = () => {
            console.log('Image loaded with dimensions:', img.width, 'x', img.height)
            resolve()
          }
          img.onerror = (err) => {
            console.error('Error loading image element:', err)
            reject(new Error('Failed to load image into Image element'))
          }
          // Timeout de 5 segundos
          setTimeout(() => reject(new Error('Image load timeout')), 5000)
          img.src = base64
        })
        
        // Añadir logo (ajustar altura manteniendo proporción)
        const logoHeight = 25
        const logoWidth = (img.width / img.height) * logoHeight
        
        // Detectar formato de imagen desde el blob type
        let format = 'JPEG'
        if (blob.type === 'image/png') {
          format = 'PNG'
        } else if (blob.type === 'image/jpeg' || blob.type === 'image/jpg') {
          format = 'JPEG'
        }
        
        console.log('Adding image to PDF with format:', format)
        doc.addImage(base64, format, 15, y, logoWidth, logoHeight)
        console.log('Image added successfully to PDF')
        y += logoHeight + 5
      } catch (err) {
        console.error('Error loading logo:', err)
        // Si falla el logo, mostrar nombre de empresa
        doc.setFontSize(26)
        doc.setFont(undefined, 'bold')
        doc.setTextColor(...azulOscuro)
        doc.text(company.name || 'Empresa', 15, y)
        y += 10
      }
    } else {
      // Título de la empresa sin logo
      doc.setFontSize(26)
      doc.setFont(undefined, 'bold')
      doc.setTextColor(...azulOscuro)
      const companyName = company.name || 'REFORMAS'
      doc.text(companyName, 15, y)
      y += 10
    }
    
    // Datos de la empresa
    doc.setFontSize(8.5)
    doc.setTextColor(...azulOscuro)
    doc.setFont(undefined, 'normal')
    
    if (company.address) {
      doc.text(company.address, 15, y)
      y += 4
    }
    
    if (company.phone) {
      doc.setFontSize(10)
      doc.setFont(undefined, 'bold')
      doc.text(company.phone, 15, y)
      doc.setFont(undefined, 'normal')
      y += 4.5
      doc.setFontSize(8.5)
    }
    
    if (company.email) {
      doc.text(`@ ${company.email}`, 15, y)
      y += 4
    }
    
    if (company.website) {
      doc.setFont(undefined, 'bold')
      doc.text(company.website, 15, y)
      doc.setFont(undefined, 'normal')
      y += 4
    }
    
    // Línea separadora azul oscura
    y += 6
    doc.setDrawColor(...azulOscuro)
    doc.setLineWidth(0.5)
    doc.line(10, y, pageWidth - 10, y)
    
    // ========== INFORMACIÓN CLIENTE Y PRESUPUESTO ==========
    y += 8
    const clientY = y
    
    // Cliente (lado izquierdo)
    doc.setFontSize(9)
    doc.setTextColor(...azulOscuro)
    doc.setFont(undefined, 'bold')
    doc.text(`Cliente: ${budget.client?.name || 'N/A'}`, 15, y)
    doc.setFont(undefined, 'normal')
    doc.setFontSize(8.5)
    y += 4
    doc.text(`DNI: ${budget.client?.dni || ''}`, 15, y)
    y += 4
    doc.text(`Teléfono: ${budget.client?.phone || ''}`, 15, y)
    y += 4
    doc.text(`Calle: ${budget.client?.street || ''}, nº ${budget.client?.number || ''}, ${budget.client?.floor || ''}º ${budget.client?.door || ''}`, 15, y)
    y += 4
    doc.text(`${budget.client?.postal_code || ''} ${budget.client?.city || ''} (${budget.client?.province || ''})`, 15, y)
    
    // Fecha y Presupuesto (lado derecho)
    doc.setFontSize(8.5)
    doc.setFont(undefined, 'bold')
    doc.text(`Fecha: ${formatDate(budget.issue_date)}`, pageWidth - 15, clientY, { align: 'right' })
    doc.text('Presupuesto', pageWidth - 15, clientY + 4, { align: 'right' })
    doc.setFont(undefined, 'normal')
    
    // Línea roja decorativa (más corta, solo debajo del cliente)
    y += 2
    doc.setDrawColor(...rojoLinea)
    doc.setLineWidth(0.5)
    doc.line(15, y, 85, y)
    
    // ========== PROYECTO ==========
    y += 10
    doc.setFontSize(10)
    doc.setFont(undefined, 'bold')
    doc.setTextColor(...azulOscuro)
    doc.text('Proyecto:', 20, y)
    doc.setFont(undefined, 'normal')
    
    // Header de la tabla del proyecto
    y += 5
    doc.setFontSize(8.5)
    doc.text('Tarea', 22, y)
    doc.text('Descripción', 50, y)
    doc.text('Precio', pageWidth - 20, y, { align: 'right' })
    
    // Línea azul claro debajo del header
    y += 1
    doc.setDrawColor(...azulClaro)
    doc.setLineWidth(0.5)
    doc.line(20, y, pageWidth - 20, y)
    
    // Items del proyecto
    y += 5
    const items = Array.isArray(budget.budget_item) ? budget.budget_item : []
    doc.setFontSize(8)
    doc.setTextColor(...azulOscuro)
    
    items.forEach((item, index) => {
      const titulo = item.title || `Item ${index + 1}`
      const descripcion = item.description || ''
      
      // Calcular el número de líneas necesarias
      const tituloLines = doc.splitTextToSize(titulo, 25)
      const descLines = doc.splitTextToSize(descripcion, 110)
      const maxLines = Math.max(tituloLines.length, descLines.length)
      const lineHeight = 4
      
      if (y + (maxLines * lineHeight) > pageHeight - 90) {
        doc.addPage()
        y = 20
      }
      
      // Título (primera columna)
      doc.setFont(undefined, 'bold')
      doc.text(tituloLines, 22, y)
      doc.setFont(undefined, 'normal')
      
      // Descripción (segunda columna)
      if (descripcion) {
        doc.text(descLines, 50, y)
      }
      
      // Precio (tercera columna)
      doc.text(`${parseFloat(item.total || 0).toFixed(2)} €`, pageWidth - 20, y, { align: 'right' })
      
      y += maxLines * lineHeight + 1
      
      // Línea separadora azul claro después de cada item
      doc.setDrawColor(...azulClaro)
      doc.setLineWidth(0.3)
      doc.line(20, y, pageWidth - 20, y)
      y += 3
    })
    
    // Calcular subtotal e IVA desde el total (que ya incluye IVA)
    const totalAmount = parseFloat(budget.total || 0)
    const subtotalAmount = totalAmount / 1.21  // Total sin IVA
    const ivaAmount = totalAmount - subtotalAmount  // IVA (21%)
    
    // Total (sin IVA)
    y += 2
    doc.setFont(undefined, 'bold')
    doc.setFontSize(9.5)
    doc.text('Total', pageWidth / 2 - 10, y, { align: 'center' })
    doc.text(`${subtotalAmount.toFixed(2)} €`, pageWidth - 20, y, { align: 'right' })
    doc.setFont(undefined, 'normal')
    
    // Nota de que no incluye IVA
    y += 6
    doc.setFontSize(8)
    doc.text('Los precios no incluyen IVA', pageWidth - 20, y, { align: 'right' })
    
    // Línea separadora azul oscura
    y += 5
    doc.setDrawColor(...azulOscuro)
    doc.setLineWidth(0.5)
    doc.line(10, y, pageWidth - 10, y)
    
    // ========== CONDICIONES GENERALES ==========
    y += 8
    const condicionesHeight = 40
    doc.setFillColor(...grisClaro)
    doc.rect(10, y, pageWidth - 20, condicionesHeight, 'F')
    
    y += 6
    doc.setFontSize(10)
    doc.setFont(undefined, 'bold')
    doc.setTextColor(...azulOscuro)
    doc.text('Condiciones generales', 20, y)
    doc.setFont(undefined, 'normal')
    
    // Header de tabla de condiciones
    y += 5
    doc.setFontSize(8)
    doc.text('Descripción', 25, y)
    doc.text('Cantidad', 130, y)
    doc.text('Precio', pageWidth - 20, y, { align: 'right' })
    
    // Línea debajo del header
    y += 1
    doc.setDrawColor(...azulClaro)
    doc.setLineWidth(0.3)
    doc.line(20, y, pageWidth - 20, y)
    
    // Items de condiciones
    y += 5
    // totalAmount ya está declarado arriba
    
    // Calculate payment amounts
    const paymentAmount1 = (totalAmount * (budget.payment_stage_1 || 0)) / 100
    const paymentAmount2 = (totalAmount * (budget.payment_stage_2 || 0)) / 100
    const paymentAmount3 = (totalAmount * (budget.payment_stage_3 || 0)) / 100
    
    doc.setFontSize(7.5)
    doc.text('1º Pago, firma del presupuesto', 25, y)
    doc.text(`${budget.payment_stage_1 || 0}%`, 130, y)
    doc.text(`${paymentAmount1.toFixed(2)} €`, pageWidth - 20, y, { align: 'right' })
    
    y += 1
    doc.line(20, y, pageWidth - 20, y)
    
    y += 5
    doc.text('2º Pago, mitad obra', 25, y)
    doc.text(`${budget.payment_stage_2 || 0}%`, 130, y)
    doc.text(`${paymentAmount2.toFixed(2)} €`, pageWidth - 20, y, { align: 'right' })
    
    y += 1
    doc.line(20, y, pageWidth - 20, y)
    
    y += 5
    doc.text('3º Pago, entrega y finalización del proyecto', 25, y)
    doc.text(`${budget.payment_stage_3 || 0}%`, 130, y)
    doc.text(`${paymentAmount3.toFixed(2)} €`, pageWidth - 20, y, { align: 'right' })
    
    y += 1
    doc.line(20, y, pageWidth - 20, y)
    
    y += 5
    doc.setFont(undefined, 'bold')
    doc.setFontSize(8.5)
    doc.text('Total', 130, y, { align: 'center' })
    doc.text(`${totalAmount.toFixed(2)} €`, pageWidth - 20, y, { align: 'right' })
    doc.setFont(undefined, 'normal')
    
    // Salir del fondo gris
    y += 8
    
    // Línea separadora azul oscura
    doc.setDrawColor(...azulOscuro)
    doc.setLineWidth(0.5)
    doc.line(10, y, pageWidth - 10, y)
    
    // ========== FORMA DE PAGO ==========
    y += 8
    doc.setFontSize(10)
    doc.setFont(undefined, 'bold')
    doc.setTextColor(...azulOscuro)
    doc.text('Forma de pago', 15, y)
    doc.setFont(undefined, 'normal')
    
    y += 6
    doc.setFontSize(8)
    doc.text('Transferencia bancaria', 15, y)
    doc.setFont(undefined, 'bold')
    doc.text('ES 52 0049 0865 6025 1026 5625', 52, y)
    doc.text(`Total: ${totalAmount.toFixed(2)} €`, pageWidth - 15, y, { align: 'right' })
    doc.setFont(undefined, 'normal')
    
    y += 6
    doc.setFontSize(7.5)
    doc.setFont(undefined, 'bolditalic')
    const textoAceptacion = 'La Aceptación del presente presupuesto implica el compromiso de ejecución. En caso de cancelación por parte del cliente, se reserva el derecho de retener el importe entregado en concepto de señal, así como los gastos generados hasta la fecha.'
    const lineasTexto = doc.splitTextToSize(textoAceptacion, pageWidth - 30)
    doc.text(lineasTexto, 15, y)
    doc.setFont(undefined, 'normal')
    
    y += 10
    doc.setFontSize(8)
    doc.text('Presupuesto válido por 15 días', 15, y)
    
    // ========== FOOTER ==========
    const footerY = pageHeight - 12
    doc.setFillColor(...azulOscuro)
    doc.rect(0, footerY, pageWidth, 12, 'F')
    doc.setFontSize(10)
    doc.setTextColor(255, 255, 255)
    const footerText = company.name 
      ? `Gracias por confiar en ${company.name}`
      : 'Gracias por su confianza'
    doc.text(footerText, pageWidth / 2, footerY + 7, { align: 'center' })

    console.log('PDF generation complete, saving...')
    doc.save(`budget_${budget.budget_number}.pdf`)
    console.log('PDF saved successfully')
  } catch (err) {
    console.error('Error downloading PDF:', err)
    console.error('Error stack:', err.stack)
    error.value = err.response?.data?.message || err.message || 'Failed to download PDF. Please try again.'
    alert('Error al generar PDF: ' + (err.message || 'Error desconocido'))
  }
}

const confirmDelete = (budget) => {
  budgetToDelete.value = budget
  showDeleteModal.value = true
}

const deleteBudget = async () => {
  if (!budgetToDelete.value) return
  
  deleting.value = true
  
  try {
    const response = await axios.delete(`/budgets/${budgetToDelete.value.id}`)
    
    if (response.data.status) {
      budgets.value = budgets.value.filter(b => b.id !== budgetToDelete.value.id)
      showDeleteModal.value = false
      budgetToDelete.value = null
    } else {
      error.value = response.data.message || 'Failed to delete budget'
    }
  } catch (err) {
    console.error('Error deleting budget:', err)
    error.value = err.response?.data?.message || 'Error al eliminar presupuesto. Por favor, intente de nuevo.'
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  loadBudgets()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-4 sm:py-8">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Presupuestos</h1>
        <router-link to="/budgets/create" class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-block text-center">
          <span class="sm:inline">Crear Nuevo Presupuesto</span>
          <span class="hidden sm:inline"></span>
        </router-link>
      </div>

      <!-- Filtros y Búsqueda -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Barra de búsqueda -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar por número, cliente o email..."
                class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <!-- Filtro por estado -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select
              v-model="statusFilter"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
            >
              <option value="all">Todos los estados</option>
              <option value="draft">Borrador</option>
              <option value="sent">Enviado</option>
              <option value="approved">Aprobado</option>
              <option value="rejected">Rechazado</option>
            </select>
          </div>

          <!-- Ordenar por -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
            <select
              v-model="sortBy"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900"
            >
              <option value="date-desc">Fecha (más reciente)</option>
              <option value="date-asc">Fecha (más antiguo)</option>
              <option value="total-desc">Total (mayor a menor)</option>
              <option value="total-asc">Total (menor a mayor)</option>
            </select>
          </div>
        </div>

        <!-- Resultados y botón limpiar -->
        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
          <p class="text-sm text-gray-600">
            Mostrando <span class="font-semibold">{{ filteredBudgets.length }}</span> de <span class="font-semibold">{{ budgets.length }}</span> presupuestos
          </p>
          <button
            v-if="searchQuery || statusFilter !== 'all' || sortBy !== 'date-desc'"
            @click="clearFilters"
            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
          >
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <p class="text-red-600">{{ error }}</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-lg shadow p-8 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="text-gray-600 mt-4">Cargando presupuestos...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="!budgets.length" class="bg-white rounded-lg shadow p-12 text-center">
        <div class="mx-auto h-24 w-24 text-gray-400">
          <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
          <h3 class="mt-2 text-lg font-medium text-gray-900">No se encontraron presupuestos</h3>
          <p class="mt-1 text-gray-600">¡Cree su primer presupuesto para comenzar!</p>
          <router-link to="/budgets/create" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
            Crear Presupuesto
          </router-link>
        </div>

      <!-- No Results State -->
      <div v-else-if="!filteredBudgets.length" class="bg-white rounded-lg shadow p-12 text-center">
        <div class="mx-auto h-24 w-24 text-gray-400">
          <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
        <h3 class="mt-2 text-lg font-medium text-gray-900">No se encontraron resultados</h3>
        <p class="mt-1 text-gray-600">Intenta ajustar los filtros de búsqueda</p>
        <button @click="clearFilters" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
          Limpiar filtros
        </button>
      </div>

      <!-- Budgets Views -->
      <template v-else>
        <!-- Budgets Grid - Mobile View -->
        <div class="lg:hidden space-y-4">
        <div v-for="budget in filteredBudgets" :key="budget.id" class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
          <div class="flex justify-between items-start mb-3">
            <div>
              <h3 class="text-lg font-bold text-gray-900">#{{ budget.budget_number }}</h3>
              <p class="text-sm text-gray-600">{{ budget.client?.name || 'N/A' }}</p>
            </div>
            <span :class="getStatusClass(budget.status)" class="px-3 py-1 text-xs font-semibold rounded-full">
              {{ capitalizeFirst(budget.status) }}
            </span>
          </div>

          <div class="space-y-2 mb-4 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Emisión:</span>
              <span class="font-medium">{{ formatDate(budget.issue_date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Vencimiento:</span>
              <span class="font-medium">{{ formatDate(budget.due_date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Total:</span>
              <span class="font-bold text-lg text-indigo-600">€{{ parseFloat(budget.total).toFixed(2) }}</span>
            </div>
          </div>

          <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-200">
            <button @click="viewBudget(budget.id)" class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors">
              Ver
            </button>
            <button @click="editBudget(budget.id)" class="flex-1 bg-yellow-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-yellow-600 transition-colors">
              Editar
            </button>
            <button @click="downloadPDF(budget.id)" class="bg-blue-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </button>
            <button 
              @click="sendEmail(budget)" 
              :disabled="sendingEmail[budget.id] || !budget.client?.email"
              :class="[budget.client?.email ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-300 cursor-not-allowed', 'text-white px-3 py-2 rounded-lg text-sm transition-colors']"
            >
              <svg v-if="!sendingEmail[budget.id]" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </button>
            <button @click="confirmDelete(budget)" class="bg-red-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Budgets Table - Desktop View -->
      <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Presupuesto #
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cliente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Fecha Emisión
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Fecha Vencimiento
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estado
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="budget in filteredBudgets" :key="budget.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #{{ budget.budget_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ budget.client?.name || 'N/A' }}</div>
                  <div class="text-sm text-gray-500">{{ budget.client?.email || 'Sin email' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(budget.issue_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(budget.due_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(budget.status)" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ capitalizeFirst(budget.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                  €{{ parseFloat(budget.total).toFixed(2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <!-- View Button -->
                    <button
                      @click="viewBudget(budget.id)"
                      class="text-indigo-600 hover:text-indigo-900 transition-colors"
                      title="Ver"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>

                    <!-- Edit Button -->
                    <button
                      @click="editBudget(budget.id)"
                      class="text-yellow-600 hover:text-yellow-900 transition-colors"
                      title="Editar"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>

                    <!-- Download PDF Button -->
                    <button
                      @click="downloadPDF(budget.id)"
                      class="text-blue-600 hover:text-blue-900 transition-colors"
                      title="Descargar PDF"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </button>

                    <!-- Send Email Button -->
                    <button
                      @click="sendEmail(budget)"
                      :disabled="sendingEmail[budget.id] || !budget.client?.email"
                      :class="[
                        budget.client?.email 
                          ? 'text-green-600 hover:text-green-900' 
                          : 'text-gray-400 cursor-not-allowed',
                        'transition-colors'
                      ]"
                      :title="budget.client?.email ? 'Enviar Email al Cliente' : 'Cliente sin email'"
                    >
                      <svg v-if="!sendingEmail[budget.id]" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                      <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                    </button>

                    <!-- Delete Button -->
                    <button
                      @click="confirmDelete(budget)"
                      class="text-red-600 hover:text-red-900 transition-colors"
                      title="Eliminar"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
                </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </template>
                    
                          <!-- Delete Confirmation Modal -->
                          <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                              <div class="mt-3 text-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Budget</h3>
                                <div class="mt-2 px-7 py-3">
                                  <p class="text-sm text-gray-500">
                                    Are you sure you want to delete budget #{{ budgetToDelete?.budget_number }}?
                                    This action cannot be undone.
                                  </p>
                                </div>
                                <div class="items-center px-4 py-3">
                                  <button
                                        @click="deleteBudget"
                                        :disabled="deleting"
                                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                                      >
                                        {{ deleting ? 'Deleting...' : 'Delete' }}
                                      </button>
                                      <button
                                        @click="showDeleteModal = false"
                                        class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-24 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                      >
                                        Cancel
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </template>
                    
                        <style scoped>
                        </style>
<script setup>
</script>