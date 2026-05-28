<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'
import type { FormInst, FormRules, UploadCustomRequestOptions } from 'naive-ui'
import { NIcon, useMessage } from 'naive-ui'
import {
  CameraOutline,
  KeyOutline,
  SaveOutline,
  ShieldCheckmarkOutline,
} from '@vicons/ionicons5'
import { useAuthStore } from '@/features/shared/stores/auth.store'
import { uploadService } from '@/features/shared/services/upload.service'
import { formatDate } from '@/shared/utils/formatDate'

type Gender = 'male' | 'female' | 'other' | null
type TabName = 'profile' | 'security'

const route = useRoute()
const message = useMessage()
const authStore = useAuthStore()
const { user, loading, validationErrors } = storeToRefs(authStore)

const profileFormRef = ref<FormInst | null>(null)
const passwordFormRef = ref<FormInst | null>(null)
const uploadLoading = ref(false)
const activeTab = ref<TabName>(route.name === 'admin-settings' ? 'security' : 'profile')

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

const genderOptions = [
  { label: 'Không chọn', value: null },
  { label: 'Nam', value: 'male' },
  { label: 'Nữ', value: 'female' },
  { label: 'Khác', value: 'other' },
]

const profileRules: FormRules = {
  name: [{ required: true, message: 'Vui lòng nhập họ tên', trigger: 'blur' }],
  email: [{ required: true, type: 'email', message: 'Email không hợp lệ', trigger: 'blur' }],
}
const passwordRules: FormRules = {
  current_password: [{ required: true, message: 'Vui lòng nhập mật khẩu hiện tại', trigger: 'blur' }],
  password: [{ required: true, min: 8, message: 'Mật khẩu mới tối thiểu 8 ký tự', trigger: 'blur' }],
  password_confirmation: [{ required: true, message: 'Vui lòng xác nhận mật khẩu mới', trigger: 'blur' }],
}

const initials = computed(() => {
  const source = user.value?.name || user.value?.email || 'A'
  return source
    .split(' ')
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
})
const roleName = computed(() => user.value?.role?.name ?? 'admin')
const permissionsCount = computed(() => user.value?.role?.permissions?.length ?? 0)

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
  passwordFormRef.value?.restoreValidation()
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
  await profileFormRef.value?.validate()

  const success = await authStore.updateProfile({
    name: profileForm.name.trim(),
    email: profileForm.email.trim(),
    phone: profileForm.phone.trim() || null,
    avatar: profileForm.avatar || null,
    date_of_birth: profileForm.date_of_birth || null,
    gender: profileForm.gender || null,
  })

  if (success) {
    message.success('Cập nhật hồ sơ quản trị thành công')
  } else {
    message.error(authStore.error || 'Không thể cập nhật hồ sơ')
  }
}

async function handleChangePassword() {
  await passwordFormRef.value?.validate()

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
  (name) => {
    activeTab.value = name === 'admin-settings' ? 'security' : 'profile'
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
  <div class="admin-profile">
    <n-grid :cols="24" :x-gap="20" :y-gap="20" responsive="screen">
      <n-grid-item :span="24" :lg="8">
        <n-card>
          <div class="profile-summary">
            <div class="avatar-wrap">
              <n-avatar
                v-if="profileForm.avatar"
                :src="profileForm.avatar"
                :size="108"
                object-fit="cover"
                round
              />
              <n-avatar v-else :size="108" round>
                {{ initials }}
              </n-avatar>

              <n-upload
                accept="image/*"
                :max="1"
                :show-file-list="false"
                :custom-request="handleAvatarUpload"
              >
                <n-button circle type="primary" class="avatar-button" :loading="uploadLoading">
                  <template #icon>
                    <n-icon><CameraOutline /></n-icon>
                  </template>
                </n-button>
              </n-upload>
            </div>

            <h2>{{ profileForm.name || 'Admin' }}</h2>
            <p>{{ profileForm.email }}</p>
            <n-tag type="success" round>
              <template #icon>
                <n-icon><ShieldCheckmarkOutline /></n-icon>
              </template>
              {{ roleName }}
            </n-tag>
          </div>

          <n-divider />

          <n-descriptions :column="1" label-placement="left" size="small">
            <n-descriptions-item label="Quyền">
              {{ permissionsCount }} nhóm quyền
            </n-descriptions-item>
            <n-descriptions-item label="SĐT">
              {{ profileForm.phone || 'Chưa cập nhật' }}
            </n-descriptions-item>
            <n-descriptions-item label="Ngày sinh">
              {{ formatDate(profileForm.date_of_birth) || 'Chưa cập nhật' }}
            </n-descriptions-item>
          </n-descriptions>
        </n-card>
      </n-grid-item>

      <n-grid-item :span="24" :lg="16">
        <n-card>
          <n-tabs v-model:value="activeTab" type="line" animated>
            <n-tab-pane name="profile" tab="Hồ sơ quản trị">
              <n-form
                ref="profileFormRef"
                :model="profileForm"
                :rules="profileRules"
                label-placement="top"
                require-mark-placement="right-hanging"
              >
                <n-grid :cols="2" :x-gap="16">
                  <n-form-item-gi
                    label="Họ tên"
                    path="name"
                    :validation-status="fieldError('name') ? 'error' : undefined"
                    :feedback="fieldError('name')"
                  >
                    <n-input v-model:value="profileForm.name" placeholder="Nhập họ tên" />
                  </n-form-item-gi>

                  <n-form-item-gi
                    label="Email"
                    path="email"
                    :validation-status="fieldError('email') ? 'error' : undefined"
                    :feedback="fieldError('email')"
                  >
                    <n-input v-model:value="profileForm.email" placeholder="admin@example.com" />
                  </n-form-item-gi>

                  <n-form-item-gi
                    label="Số điện thoại"
                    path="phone"
                    :validation-status="fieldError('phone') ? 'error' : undefined"
                    :feedback="fieldError('phone')"
                  >
                    <n-input v-model:value="profileForm.phone" placeholder="0901234567" />
                  </n-form-item-gi>

                  <n-form-item-gi
                    label="Giới tính"
                    path="gender"
                    :validation-status="fieldError('gender') ? 'error' : undefined"
                    :feedback="fieldError('gender')"
                  >
                    <n-select v-model:value="profileForm.gender" :options="genderOptions" />
                  </n-form-item-gi>

                  <n-form-item-gi
                    label="Ngày sinh"
                    path="date_of_birth"
                    :validation-status="fieldError('date_of_birth') ? 'error' : undefined"
                    :feedback="fieldError('date_of_birth')"
                  >
                    <n-date-picker
                      v-model:formatted-value="profileForm.date_of_birth"
                      value-format="yyyy-MM-dd"
                      type="date"
                      clearable
                      style="width: 100%"
                    />
                  </n-form-item-gi>

                  <n-form-item-gi
                    label="Avatar URL"
                    path="avatar"
                    :validation-status="fieldError('avatar') ? 'error' : undefined"
                    :feedback="fieldError('avatar')"
                  >
                    <n-input v-model:value="profileForm.avatar" placeholder="https://..." />
                  </n-form-item-gi>
                </n-grid>

                <n-space justify="end">
                  <n-button type="primary" :loading="loading" @click="handleSaveProfile">
                    <template #icon>
                      <n-icon><SaveOutline /></n-icon>
                    </template>
                    Lưu hồ sơ
                  </n-button>
                </n-space>
              </n-form>
            </n-tab-pane>

            <n-tab-pane name="security" tab="Bảo mật">
              <n-form
                ref="passwordFormRef"
                :model="passwordForm"
                :rules="passwordRules"
                label-placement="top"
                require-mark-placement="right-hanging"
                style="max-width: 560px"
              >
                <n-form-item
                  label="Mật khẩu hiện tại"
                  path="current_password"
                  :validation-status="fieldError('current_password') ? 'error' : undefined"
                  :feedback="fieldError('current_password')"
                >
                  <n-input v-model:value="passwordForm.current_password" type="password" show-password-on="click" />
                </n-form-item>

                <n-form-item
                  label="Mật khẩu mới"
                  path="password"
                  :validation-status="fieldError('password') ? 'error' : undefined"
                  :feedback="fieldError('password')"
                >
                  <n-input v-model:value="passwordForm.password" type="password" show-password-on="click" />
                </n-form-item>

                <n-form-item label="Xác nhận mật khẩu mới" path="password_confirmation">
                  <n-input v-model:value="passwordForm.password_confirmation" type="password" show-password-on="click" />
                </n-form-item>

                <n-alert type="info" :bordered="false" class="mb-4">
                  Mật khẩu mới cần tối thiểu 8 ký tự.
                </n-alert>

                <n-space justify="end">
                  <n-button type="primary" :loading="loading" @click="handleChangePassword">
                    <template #icon>
                      <n-icon><KeyOutline /></n-icon>
                    </template>
                    Đổi mật khẩu
                  </n-button>
                </n-space>
              </n-form>
            </n-tab-pane>
          </n-tabs>
        </n-card>
      </n-grid-item>
    </n-grid>
  </div>
</template>

<style scoped>
.admin-profile {
  min-height: 520px;
}

.profile-summary {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.profile-summary h2 {
  margin: 16px 0 4px;
  font-size: 22px;
  font-weight: 800;
}

.profile-summary p {
  margin: 0 0 12px;
  color: var(--n-text-color-3);
}

.avatar-wrap {
  position: relative;
}

.avatar-button {
  position: absolute;
  right: 0;
  bottom: 0;
}

.mb-4 {
  margin-bottom: 16px;
}
</style>
