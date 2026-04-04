import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios' 
import { authService } from '../services/auth.service'
import { EnumUserRole } from '@/shared/types/auth'
import type { User, LoginPayload, RegisterPayload, ValidationError } from '@/shared/types/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationError['errors']>({})
  const isInitialized = ref(false)

  const isLoggedIn = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.role?.name === EnumUserRole.ADMIN)
  const isClient = computed(() => user.value?.role?.name === EnumUserRole.CUSTOMER)

  function resetError() {
    error.value = null
    validationErrors.value = {}
  }

  function handleError(err: unknown) {
    if (axios.isAxiosError(err)) {
      if (err.response?.status === 422) {
        validationErrors.value = err.response.data.errors
        return
      }
      error.value = err.response?.data?.message || 'Đã có lỗi xảy ra từ máy chủ'
    } else {
      error.value = 'Lỗi kết nối không xác định'
    }
  }

  async function login(payload: LoginPayload) {
    loading.value = true
    resetError()
    try {
      const res = await authService.login(payload)
      user.value = res.data
      return true 
    } catch (err) {
      handleError(err)
      return false
    } finally {
      loading.value = false
    }
  }

  async function register(payload: RegisterPayload) {
    loading.value = true
    resetError()
    try {
      const res = await authService.register(payload)
      user.value = res.data
      return true 
    } catch (err) {
      handleError(err)
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout(): Promise<boolean> {
    try {
      await authService.logout()
      return true
    }catch {
      return false
    } finally {
      user.value = null
      localStorage.removeItem('is_logged_in')
    }
  }

  async function fetchMe() {
    try {
      const res = await authService.getMe()
      user.value = res.data
      localStorage.setItem('is_logged_in', 'true')
    } catch {
      user.value = null
      localStorage.removeItem('is_logged_in')
    } finally {
      isInitialized.value = true
    }
  }

  return { user, loading, error, validationErrors, isLoggedIn, isAdmin, isClient, login, register, logout, fetchMe, isInitialized }
})