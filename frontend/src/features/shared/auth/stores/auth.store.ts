import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { ApiError } from '@/plugins/axios'
import { authService } from '../services/auth.service'
import { EnumUserRole } from '@/shared/types/auth'
import type { User, LoginPayload, RegisterPayload, ValidationError } from '@/shared/types/auth'
import { STORAGE_KEYS } from '@/shared/constants/storage'

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
    if (err instanceof ApiError) {
      if (err.status === 422 && err.errors) {
        validationErrors.value = err.errors
        return
      }
      error.value = err.message
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
      await authService.register(payload)
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
      user.value = null
      localStorage.removeItem(STORAGE_KEYS.IS_LOGGED_IN)
      return true
    }catch {
      return false
    }
  }

  async function fetchMe() {
    try {
      const res = await authService.getMe()
      user.value = res.data
      localStorage.setItem(STORAGE_KEYS.IS_LOGGED_IN, 'true')
    } catch {
      user.value = null
      localStorage.removeItem(STORAGE_KEYS.IS_LOGGED_IN)
    } finally {
      isInitialized.value = true
    }
  }

  return { user, loading, error, validationErrors, isLoggedIn, isAdmin, isClient, login, register, logout, fetchMe, isInitialized, resetError }
})