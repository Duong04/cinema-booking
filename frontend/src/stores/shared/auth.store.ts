// src/stores/shared/auth.store.ts
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from '@/services/shared/auth.service'
import type {
  User,
  LoginPayload,
  RegisterPayload,
  ValidationError,
} from '@/types/shared/auth'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()

  // ── State ──
  const user = ref<User | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationError['errors']>({})

  // ── Getters ──
  const isLoggedIn = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isClient = computed(() => user.value?.role === 'client')
  const fullName = computed(() => user.value?.name ?? '')
  const avatar = computed(() => user.value?.avatar ?? null)

  // ── Helpers ──
  function resetError() {
    error.value = null
    validationErrors.value = {}
  }

  function isValidationError(err: unknown): err is ValidationError {
    return (
      typeof err === 'object' &&
      err !== null &&
      'errors' in err &&
      typeof (err as ValidationError).errors === 'object'
    )
  }

  function isErrorWithMessage(err: unknown): err is { message: string } {
    return (
      typeof err === 'object' &&
      err !== null &&
      'message' in err &&
      typeof (err as { message: unknown }).message === 'string'
    )
  }

  function handleError(err: unknown) {
    if (isValidationError(err)) {
      validationErrors.value = err.errors
      return
    }
    if (isErrorWithMessage(err)) {
      error.value = err.message
      return
    }
    error.value = 'Đã có lỗi xảy ra'
  }

  // ── Actions ──
  async function login(payload: LoginPayload) {
    loading.value = true
    resetError()

    try {
      const res = await authService.login(payload)
      user.value = res.user

      const redirect = router.currentRoute.value.query.redirect as string
      await router.push(redirect || '/')
    } catch (err: unknown) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  async function register(payload: RegisterPayload) {
    loading.value = true
    resetError()

    try {
      const res = await authService.register(payload)
      user.value = res.user

      await router.push('/')
    } catch (err: unknown) {
      handleError(err)
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true

    try {
      await authService.logout()
    } finally {
      user.value = null
      loading.value = false
      await router.push('/auth/login')
    }
  }

  async function fetchMe() {
    try {
      user.value = await authService.getMe()
    } catch {
      user.value = null
    }
  }

  return {
    user,
    loading,
    error,
    validationErrors,
    isLoggedIn,
    isAdmin,
    isClient,
    fullName,
    avatar,
    login,
    register,
    logout,
    fetchMe,
  }
})