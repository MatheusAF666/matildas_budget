import { ref, readonly } from 'vue'
import axios from 'axios'

const user = ref(null)
const isAuthenticated = ref(false)
const loading = ref(false)

export function useAuth() {
  const login = async (credentials) => {
    loading.value = true
    try {
      const response = await axios.post('/login', credentials)
      
      if (response.data.status) {
        user.value = response.data.user
        isAuthenticated.value = true
        return { success: true }
      }
      
      return { success: false, message: response.data.message }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Login failed' 
      }
    } finally {
      loading.value = false
    }
  }

  const register = async (credentials) => {
    loading.value = true
    try {
      const response = await axios.post('/register', credentials)
      
      if (response.data.status) {
        user.value = response.data.user
        isAuthenticated.value = true
        return { success: true }
      }
      
      return { success: false, message: response.data.message }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Registration failed' 
      }
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await axios.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    }
    
    user.value = null
    isAuthenticated.value = false
  }

  const checkAuth = async () => {
    try {
      const response = await axios.get('/user')
      if (response.data.status && response.data.user) {
        user.value = response.data.user
        isAuthenticated.value = true
      }
    } catch (error) {
      user.value = null
      isAuthenticated.value = false
    }
  }

  return {
    user: readonly(user),
    isAuthenticated: readonly(isAuthenticated),
    loading: readonly(loading),
    login,
    register,
    logout,
    checkAuth
  }
}