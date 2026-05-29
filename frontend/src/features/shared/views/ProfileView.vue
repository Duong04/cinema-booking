<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'
import {
  Camera,
  CheckCircle2,
  KeyRound,
  Loader2,
  Mail,
  Phone,
  Save,
  ShieldCheck,
  Ticket,
  Trophy,
  User,
} from 'lucide-vue-next'
import { useMessage } from 'naive-ui'
import type { UploadCustomRequestOptions } from 'naive-ui'
import { useAuthStore } from '@/features/shared/stores/auth.store'
import { uploadService } from '@/features/shared/services/upload.service'

type Gender = 'male' | 'female' | 'other' | null
type TabKey = 'profile' | 'settings'

const route = useRoute()
const message = useMessage()
const authStore = useAuthStore()
const { user, loading, validationErrors } = storeToRefs(authStore)

const activeTab = ref<TabKey>('profile')
const uploadLoading = ref(false)
const profileForm = reactive({
  name: '',
  email: '',
  phone: '',
  avatar: '',
  date_of_birth: '',
  gender: null as Gender,
})
const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const tabs = [
  { key: 'profile' as const, label: 'Hồ sơ', icon: User },
  { key: 'settings' as const, label: 'Bảo mật', icon: KeyRound },
]
const genderOptions = [
  { value: '', label: 'Không chọn' },
  { value: 'male', label: 'Nam' },
  { value: 'female', label: 'Nữ' },
  { value: 'other', label: 'Khác' },
]

const initials = computed(() => {
  const source = user.value?.name || user.value?.email || 'U'
  return source
    .split(' ')
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
})
const roleName = computed(() => user.value?.role?.name ?? 'customer')
const hasAvatar = computed(() => Boolean(profileForm.avatar))
const membership = computed(() => user.value?.membership ?? null)
const membershipTierLabel = computed(() => {
  const tier = membership.value?.tier ?? 'bronze'

  return {
    bronze: 'Bronze',
    silver: 'Silver',
    gold: 'Gold',
    platinum: 'Platinum',
  }[tier]
})
const membershipPoints = computed(() => membership.value?.points ?? 0)
const ticketsPurchasedCount = computed(() => user.value?.tickets_purchased_count ?? 0)

function syncProfileForm() {
  if (!user.value) return

  profileForm.name = user.value.name ?? ''
  profileForm.email = user.value.email ?? ''
  profileForm.phone = user.value.phone ?? ''
  profileForm.avatar = user.value.avatar ?? ''
  profileForm.date_of_birth = user.value.date_of_birth ?? ''
  profileForm.gender = user.value.gender ?? null
}

function fieldError(field: string) {
  return validationErrors.value[field]?.[0] ?? ''
}

function resetPasswordForm() {
  passwordForm.current_password = ''
  passwordForm.password = ''
  passwordForm.password_confirmation = ''
}

async function handleAvatarUpload({ file, onFinish, onError }: UploadCustomRequestOptions) {
  try {
    uploadLoading.value = true
    const res = await uploadService.uploadImage(file.file as File, 'avatars')
    profileForm.avatar = res.data.url
    onFinish()
    message.success('Tải avatar thành công')
  } catch {
    onError()
    message.error('Tải avatar thất bại')
  } finally {
    uploadLoading.value = false
  }
}

async function handleSaveProfile() {
  const success = await authStore.updateProfile({
    name: profileForm.name.trim(),
    email: profileForm.email.trim(),
    phone: profileForm.phone.trim() || null,
    avatar: profileForm.avatar || null,
    date_of_birth: profileForm.date_of_birth || null,
    gender: profileForm.gender || null,
  })

  if (success) {
    message.success('Cập nhật hồ sơ thành công')
  } else {
    message.error(authStore.error || 'Không thể cập nhật hồ sơ')
  }
}

async function handleChangePassword() {
  const success = await authStore.changePassword({
    current_password: passwordForm.current_password,
    password: passwordForm.password,
    password_confirmation: passwordForm.password_confirmation,
  })

  if (success) {
    resetPasswordForm()
    message.success('Đổi mật khẩu thành công')
  } else {
    message.error(authStore.error || 'Không thể đổi mật khẩu')
  }
}

watch(user, syncProfileForm, { immediate: true })
watch(
  () => route.name,
  () => {
    activeTab.value = 'profile'
  },
)

onMounted(async () => {
  if (!user.value) {
    await authStore.fetchMe()
  }
  syncProfileForm()
})
</script>

<template>
  <div class="pt-28 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-[340px_1fr] gap-8">
      <aside class="bg-zinc-900/80 border border-white/10 rounded-3xl p-6 h-fit">
        <div class="flex flex-col items-center text-center">
          <div class="relative">
            <img
              v-if="hasAvatar"
              :src="profileForm.avatar"
              :alt="profileForm.name"
              class="w-28 h-28 rounded-3xl object-cover border border-white/10"
              referrerpolicy="no-referrer"
            >
            <div
              v-else
              class="w-28 h-28 rounded-3xl bg-red-600/20 text-red-200 border border-red-500/20 flex items-center justify-center text-3xl font-black"
            >
              {{ initials }}
            </div>
            <n-upload
              accept="image/*"
              :max="1"
              :show-file-list="false"
              :custom-request="handleAvatarUpload"
            >
              <button
                class="absolute -right-3 -bottom-3 w-11 h-11 rounded-2xl bg-white text-black flex items-center justify-center shadow-xl hover:bg-gray-200"
                :disabled="uploadLoading"
              >
                <Loader2 v-if="uploadLoading" class="w-5 h-5 animate-spin" />
                <Camera v-else class="w-5 h-5" />
              </button>
            </n-upload>
          </div>

          <h1 class="mt-6 text-2xl font-black text-white uppercase tracking-tight">
            {{ profileForm.name || 'Tài khoản' }}
          </h1>
          <p class="text-gray-500 text-sm">{{ profileForm.email }}</p>

          <div class="mt-5 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-black uppercase">
            <ShieldCheck class="w-4 h-4" />
            {{ roleName }}
          </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">
          <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
            <div class="flex items-center gap-2 text-amber-300 text-xs font-black uppercase">
              <Trophy class="w-4 h-4" />
              Hạng
            </div>
            <p class="mt-2 text-xl font-black text-white">{{ membershipTierLabel }}</p>
            <p class="text-xs text-gray-500">{{ membershipPoints.toLocaleString('vi-VN') }} điểm</p>
          </div>

          <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
            <div class="flex items-center gap-2 text-sky-300 text-xs font-black uppercase">
              <Ticket class="w-4 h-4" />
              Vé
            </div>
            <p class="mt-2 text-xl font-black text-white">{{ ticketsPurchasedCount.toLocaleString('vi-VN') }}</p>
            <p class="text-xs text-gray-500">đã mua</p>
          </div>
        </div>

        <div class="mt-8 grid gap-2">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              'w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-left font-bold transition-all',
              activeTab === tab.key
                ? 'bg-red-600 text-white'
                : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white',
            ]"
          >
            <component :is="tab.icon" class="w-5 h-5" />
            {{ tab.label }}
          </button>
        </div>
      </aside>

      <section class="bg-zinc-900/80 border border-white/10 rounded-3xl p-6 sm:p-8">
        <div class="mb-8">
          <p class="text-red-500 text-xs font-black uppercase tracking-[0.2em] mb-2">
            {{ activeTab === 'profile' ? 'Profile' : 'Settings' }}
          </p>
          <h2 class="text-3xl font-black text-white uppercase tracking-tight">
            {{ activeTab === 'profile' ? 'Thông tin cá nhân' : 'Bảo mật tài khoản' }}
          </h2>
        </div>

        <form v-if="activeTab === 'profile'" class="grid grid-cols-1 md:grid-cols-2 gap-5" @submit.prevent="handleSaveProfile">
          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Họ tên</span>
            <div class="relative">
              <User class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-600" />
              <input v-model="profileForm.name" class="form-input pl-12" type="text" required>
            </div>
            <span v-if="fieldError('name')" class="text-sm text-red-400">{{ fieldError('name') }}</span>
          </label>

          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Email</span>
            <div class="relative">
              <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-600" />
              <input v-model="profileForm.email" class="form-input pl-12" type="email" required>
            </div>
            <span v-if="fieldError('email')" class="text-sm text-red-400">{{ fieldError('email') }}</span>
          </label>

          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Số điện thoại</span>
            <div class="relative">
              <Phone class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-600" />
              <input v-model="profileForm.phone" class="form-input pl-12" type="tel">
            </div>
            <span v-if="fieldError('phone')" class="text-sm text-red-400">{{ fieldError('phone') }}</span>
          </label>

          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Ngày sinh</span>
            <input v-model="profileForm.date_of_birth" class="form-input" type="date">
            <span v-if="fieldError('date_of_birth')" class="text-sm text-red-400">{{ fieldError('date_of_birth') }}</span>
          </label>

          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Giới tính</span>
            <select v-model="profileForm.gender" class="form-input">
              <option v-for="option in genderOptions" :key="option.value" :value="option.value || null">
                {{ option.label }}
              </option>
            </select>
            <span v-if="fieldError('gender')" class="text-sm text-red-400">{{ fieldError('gender') }}</span>
          </label>

          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Avatar URL</span>
            <input v-model="profileForm.avatar" class="form-input" type="url">
            <span v-if="fieldError('avatar')" class="text-sm text-red-400">{{ fieldError('avatar') }}</span>
          </label>

          <div class="md:col-span-2 flex justify-end pt-4">
            <button
              type="submit"
              :disabled="loading"
              class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-red-600 text-white font-black hover:bg-red-700 disabled:opacity-70"
            >
              <Loader2 v-if="loading" class="w-5 h-5 animate-spin" />
              <Save v-else class="w-5 h-5" />
              Lưu hồ sơ
            </button>
          </div>
        </form>

        <form v-else class="max-w-2xl grid gap-5" @submit.prevent="handleChangePassword">
          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Mật khẩu hiện tại</span>
            <input v-model="passwordForm.current_password" class="form-input" type="password" required>
            <span v-if="fieldError('current_password')" class="text-sm text-red-400">{{ fieldError('current_password') }}</span>
          </label>

          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Mật khẩu mới</span>
            <input v-model="passwordForm.password" class="form-input" type="password" minlength="8" required>
            <span v-if="fieldError('password')" class="text-sm text-red-400">{{ fieldError('password') }}</span>
          </label>

          <label class="grid gap-2">
            <span class="text-sm font-bold text-gray-400">Xác nhận mật khẩu mới</span>
            <input v-model="passwordForm.password_confirmation" class="form-input" type="password" minlength="8" required>
          </label>

          <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm">
            <CheckCircle2 class="w-5 h-5 shrink-0" />
            <span>Mật khẩu mới cần tối thiểu 8 ký tự.</span>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="loading"
              class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-red-600 text-white font-black hover:bg-red-700 disabled:opacity-70"
            >
              <Loader2 v-if="loading" class="w-5 h-5 animate-spin" />
              <KeyRound v-else class="w-5 h-5" />
              Đổi mật khẩu
            </button>
          </div>
        </form>
      </section>
    </div>
  </div>
</template>

<style scoped>
.form-input {
  width: 100%;
  border: 1px solid rgb(255 255 255 / 0.1);
  border-radius: 1rem;
  background: rgb(0 0 0 / 0.32);
  color: white;
  outline: none;
  padding: 1rem;
  transition: border-color 150ms ease, box-shadow 150ms ease;
}

.form-input:focus {
  border-color: rgb(220 38 38 / 0.75);
  box-shadow: 0 0 0 4px rgb(220 38 38 / 0.14);
}
</style>
