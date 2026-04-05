<script setup lang="ts">
import { ref, onUnmounted } from 'vue'
import { useForm } from 'vee-validate'
import { Film, Mail, Lock, Eye, EyeOff, ArrowRight, Github, Chrome } from 'lucide-vue-next'
import { useAuthStore } from '@/features/shared/auth/stores/auth.store'
import { useLanguageStore } from '@/stores/language'
import { loginSchema } from '@/features/shared/auth/validators/auth.validation'
import { useRouter, useRoute } from 'vue-router'
import { STORAGE_KEYS } from '@/shared/constants/storage'

const authStore = useAuthStore()
const languageStore = useLanguageStore()

const showPassword = ref(false)
const router = useRouter()
const route = useRoute()

onUnmounted(() => {
  authStore.resetError()
})

const { handleSubmit, defineField, errors, isSubmitting } = useForm({
  validationSchema: loginSchema,
  initialValues: {
    email: '',
    password: '',
    remember: false,
  },
})

const [email, emailAttrs] = defineField('email')
const [password, passwordAttrs] = defineField('password')

const onSubmit = handleSubmit(async (values) => {
  const success = await authStore.login(values)

  if (success) {
    localStorage.setItem(STORAGE_KEYS.IS_LOGGED_IN, 'true')

    const redirectPath = (route.query.redirect as string) || '/'
    router.push(redirectPath)
  }
})

const t = (en: string, vi: string) => (languageStore.language === 'en' ? en : vi)
</script>

<template>
  <div
    class="min-h-screen flex items-center justify-center px-4 py-24 bg-zinc-950 relative overflow-hidden"
  >
    <!-- Background Glow -->
    <div
      class="absolute top-1/4 -left-1/4 w-96 h-96 bg-red-600/20 rounded-full blur-[120px] pointer-events-none"
    />
    <div
      class="absolute bottom-1/4 -right-1/4 w-96 h-96 bg-orange-600/10 rounded-full blur-[120px] pointer-events-none"
    />

    <div class="w-full max-w-md relative z-10">
      <!-- Header -->
      <div class="text-center mb-10">
        <router-link to="/" class="inline-flex items-center gap-2 mb-6 group">
          <div
            class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-red-600/20"
          >
            <Film class="text-white w-6 h-6" />
          </div>
          <span class="text-3xl font-black text-white tracking-tighter">CINEMAX</span>
        </router-link>
        <h1 class="text-3xl font-black text-white uppercase italic tracking-tight mb-2">
          {{ t('Welcome Back', 'Chào mừng trở lại') }}
        </h1>
        <p class="text-gray-500 font-medium">
          {{ t('Sign in to your account', 'Đăng nhập vào tài khoản của bạn') }}
        </p>
      </div>

      <!-- Card -->
      <div
        class="bg-zinc-900/50 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 md:p-10 shadow-2xl"
      >
        <form @submit.prevent="onSubmit" novalidate class="space-y-6">
          <!-- Email -->
          <div class="space-y-2">
            <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">
              Email
            </label>
            <div class="relative group">
              <Mail
                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-red-500 transition-colors"
              />
              <input
                v-model="email"
                v-bind="emailAttrs"
                type="email"
                placeholder="name@example.com"
                :class="[
                  'w-full bg-black/40 border rounded-2xl py-4 pl-12 pr-4 text-white placeholder:text-gray-700 focus:outline-none transition-all',
                  errors.email || authStore.validationErrors?.email
                    ? 'border-red-600/50 ring-4 ring-red-600/10'
                    : 'border-white/5 focus:border-red-600/50 focus:ring-4 focus:ring-red-600/10',
                ]"
              />
            </div>
            <!-- client validation -->
            <p v-if="errors.email" class="text-red-500 text-xs font-medium ml-1">
              {{ errors.email }}
            </p>
            <!-- laravel 422 validation -->
            <p
              v-else-if="authStore.validationErrors?.email"
              class="text-red-500 text-xs font-medium ml-1"
            >
              {{ authStore.validationErrors.email[0] }}
            </p>
          </div>

          <!-- Password -->
          <div class="space-y-2">
            <div class="flex justify-between items-center ml-1">
              <label class="text-xs font-black text-gray-500 uppercase tracking-widest">
                {{ t('Password', 'Mật khẩu') }}
              </label>
              <router-link
                to="/auth/forgot-password"
                class="text-xs font-bold text-red-500 hover:text-red-400 transition-colors"
              >
                {{ t('Forgot?', 'Quên mật khẩu?') }}
              </router-link>
            </div>
            <div class="relative group">
              <Lock
                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-red-500 transition-colors"
              />
              <input
                v-model="password"
                v-bind="passwordAttrs"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                :class="[
                  'w-full bg-black/40 border rounded-2xl py-4 pl-12 pr-12 text-white placeholder:text-gray-700 focus:outline-none focus:ring-4 transition-all',
                  errors.password || authStore.validationErrors?.password
                    ? 'border-red-500/50 focus:border-red-500 focus:ring-red-500/10'
                    : 'border-white/5 focus:border-red-600/50 focus:ring-red-600/10',
                ]"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors"
              >
                <Eye v-if="!showPassword" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
              </button>
            </div>
            <p v-if="errors.password" class="text-red-500 text-xs font-medium ml-1">
              {{ errors.password }}
            </p>
            <p
              v-else-if="authStore.validationErrors?.password"
              class="text-red-500 text-xs font-medium ml-1"
            >
              {{ authStore.validationErrors.password[0] }}
            </p>
          </div>

          <!-- Error chung từ server -->
          <div
            v-if="authStore.error"
            class="bg-red-500/10 border border-red-500/20 rounded-2xl px-4 py-3"
          >
            <p class="text-red-400 text-sm font-medium text-center">
              {{ authStore.error }}
            </p>
          </div>

          <!-- Submit -->
          <div class="col-span-2 pt-4">
            <button
              type="submit"
              :disabled="isSubmitting || authStore.loading"
              class="w-full py-4 bg-red-600 text-white rounded-2xl font-black flex items-center justify-center gap-2 hover:bg-red-700 transition-all transform active:scale-95 shadow-xl shadow-red-600/20 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!isSubmitting && !authStore.loading">{{
                languageStore.language === 'en' ? 'Sign In' : 'Đăng nhập'
              }}</span>
              <div
                v-else
                class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"
              />
              <ArrowRight v-if="!isSubmitting && !authStore.loading" class="w-5 h-5" />
            </button>
          </div>
        </form>

        <!-- Divider -->
        <div class="mt-8 relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-white/5" />
          </div>
          <div class="relative flex justify-center text-xs uppercase font-black">
            <span class="bg-zinc-900 px-4 text-gray-600 tracking-widest">
              {{ t('Or continue with', 'Hoặc tiếp tục với') }}
            </span>
          </div>
        </div>

        <!-- Social login -->
        <div class="grid grid-cols-2 gap-4 mt-8">
          <button
            class="flex items-center justify-center gap-2 py-3 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 transition-all font-bold text-sm text-white"
          >
            <Chrome class="w-5 h-5" />
            Google
          </button>
          <button
            class="flex items-center justify-center gap-2 py-3 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 transition-all font-bold text-sm text-white"
          >
            <Github class="w-5 h-5" />
            Github
          </button>
        </div>
      </div>

      <!-- Footer -->
      <p class="text-center mt-8 text-gray-500 font-medium">
        {{ t("Don't have an account?", 'Chưa có tài khoản?') }}
        <router-link
          :to="{ name: 'register' }"
          class="text-red-500 font-black hover:text-red-400 transition-colors ml-1"
        >
          {{ t('Create one', 'Tạo tài khoản') }}
        </router-link>
      </p>
    </div>
  </div>
</template>
