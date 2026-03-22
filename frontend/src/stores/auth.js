import { defineStore } from 'pinia'
import api from '@/services/api'
import router from '@/router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(email, senha) {
      this.loading = true
      try {
        const response = await api.post('auth/login', { email, senha })
        // The API returns { success: true, data: { access_token, user } }
        const dataPayload = response.data.data || response.data
        const { access_token, user } = dataPayload
        
        this.token = access_token
        this.user = user
        localStorage.setItem('token', access_token)
        return response.data
      } catch (error) {
        throw error
      } finally {
        this.loading = false
      }
    },
    async register(userData) {
      this.loading = true
      try {
        // Assume registration is creating a user via POST /usuarios
        // Or check if there is a separate auth/register
        // For this challenge, we'll try auth/register if it fails let's try usuarios
        const response = await api.post('usuarios', userData)
        return response.data
      } catch (error) {
        throw error
      } finally {
        this.loading = false
      }
    },
    async fetchMe() {
      if (!this.token) return
      try {
        const response = await api.get('auth/me')
        this.user = response.data.data || response.data
      } catch (error) {
        this.logout()
      }
    },
    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      router.push('/login')
    }
  }
})
