<script setup lang="ts">
import { computed, nextTick, reactive, ref, watch } from 'vue'
import type { FormInst, FormRules, SelectOption } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { ApiError } from '@/plugins/axios'
import { userService } from '@/features/admin/services/user.service'
import type { User, Gender } from '@/features/admin/types/user.type'

interface UserForm {
  name: string
  email: string
  password: string
  phone: string
  role_id: string | null
  is_active: boolean
  gender: Gender
}

type UserPayload = Omit<UserForm, 'password'> & {
  password?: string
}

const props = defineProps<{
  user?: User | null
  roles: SelectOption[]
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const backendErrors = reactive<Record<string, string>>({})

const isEdit = computed(() => Boolean(props.user?.id))
const modalTitle = computed(() => (isEdit.value ? 'Chỉnh sửa người dùng' : 'Tạo người dùng mới'))
const submitLabel = computed(() => (isEdit.value ? 'Cập nhật' : 'Tạo người dùng'))

const genderOptions: SelectOption[] = [
  { label: 'Nam', value: 'male' },
  { label: 'Nữ', value: 'female' },
  { label: 'Khác', value: 'other' },
]

const formData = reactive<UserForm>({
  name: '',
  email: '',
  password: '',
  phone: '',
  role_id: null,
  is_active: true,
  gender: 'other',
})

const formRules: FormRules = {
  name: [
    { required: true, message: 'Vui lòng nhập tên người dùng', trigger: ['blur', 'input'] },
    { min: 3, message: 'Tên người dùng phải có ít nhất 3 ký tự', trigger: ['blur', 'input'] },
  ],
  email: [
    { required: true, message: 'Vui lòng nhập email', trigger: ['blur', 'input'] },
    { type: 'email', message: 'Email không hợp lệ', trigger: ['blur', 'input'] },
  ],
  password: [
    {
      validator: (_rule, value: string) => {
        if (!isEdit.value && !value) return new Error('Vui lòng nhập mật khẩu')
        if (value && value.length < 8) return new Error('Mật khẩu phải có ít nhất 8 ký tự')
        return true
      },
      trigger: ['blur', 'input'],
    },
  ],
  phone: [
    {
      validator: (_rule, value: string) => {
        if (!value) return true
        if (!/^0[0-9]{9}$/.test(value)) return new Error('Số điện thoại không hợp lệ')
        return true
      },
      trigger: ['blur', 'input'],
    },
  ],
  role_id: [{ required: true, message: 'Vui lòng chọn vai trò', trigger: ['change', 'blur'] }],
  gender: [{ required: true, message: 'Vui lòng chọn giới tính', trigger: ['change', 'blur'] }],
}

function clearBackendError(field: keyof UserForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function clearBackendErrors() {
  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
}

function getDefaultRoleId() {
  const roleValue = props.roles.length === 1 ? props.roles[0]?.value : null

  if (roleValue === null || roleValue === undefined) {
    return null
  }

  return String(roleValue)
}

function resetForm() {
  formData.name = ''
  formData.email = ''
  formData.password = ''
  formData.phone = ''
  formData.role_id = getDefaultRoleId()
  formData.is_active = true
  formData.gender = 'other'
}

function syncFormWithUser() {
  if (!props.user) {
    resetForm()
  } else {
    formData.name = props.user.name ?? ''
    formData.email = props.user.email ?? ''
    formData.password = ''
    formData.phone = props.user.phone ?? ''
    formData.role_id = props.user.role_id ?? null
    formData.is_active = Boolean(props.user.is_active)
    formData.gender = props.user.gender ?? 'other'
  }

  clearBackendErrors()
  nextTick(() => formRef.value?.restoreValidation())
}

function buildPayload(): UserPayload {
  const payload: UserPayload = {
    name: formData.name.trim(),
    email: formData.email.trim(),
    phone: formData.phone.trim(),
    role_id: formData.role_id,
    is_active: formData.is_active,
    gender: formData.gender,
  }

  if (formData.password) {
    payload.password = formData.password
  }

  return payload
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()

    formLoading.value = true
    clearBackendErrors()

    const payload = buildPayload()

    if (isEdit.value && props.user?.id) {
      await userService.updateUser(props.user.id, payload as User)
      message.success('Cập nhật người dùng thành công')
    } else {
      await userService.createUser(payload as User)
      message.success('Tạo người dùng mới thành công')
    }

    showModal.value = false
    emit('success')
  } catch (err) {
    if (err instanceof ApiError && err.status === 422 && err.errors) {
      Object.entries(err.errors).forEach(([field, messages]) => {
        backendErrors[field] = (messages as string[])[0] ?? ''
      })
      message.error('Dữ liệu không hợp lệ, vui lòng kiểm tra lại')
      return
    }

    message.error(err instanceof ApiError ? err.message : 'Đã có lỗi hệ thống xảy ra')
  } finally {
    formLoading.value = false
  }
}

watch(() => [props.user, props.roles], syncFormWithUser, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="modalTitle"
    :show-icon="false"
    style="width: 620px"
    @after-leave="syncFormWithUser"
  >
    <n-form
      ref="formRef"
      :model="formData"
      :rules="formRules"
      label-placement="top"
      require-mark-placement="right-hanging"
      class="mt-4"
    >
      <n-grid :cols="2" :x-gap="16">
        <n-form-item-gi
          label="Tên người dùng"
          path="name"
          :validation-status="backendErrors.name ? 'error' : undefined"
          :feedback="backendErrors.name"
        >
          <n-input
            v-model:value="formData.name"
            placeholder="Ví dụ: Nguyễn Văn A"
            @input="clearBackendError('name')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Email"
          path="email"
          :validation-status="backendErrors.email ? 'error' : undefined"
          :feedback="backendErrors.email"
        >
          <n-input
            v-model:value="formData.email"
            placeholder="user@example.com"
            @input="clearBackendError('email')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Mật khẩu"
          path="password"
          :validation-status="backendErrors.password ? 'error' : undefined"
          :feedback="backendErrors.password || (isEdit ? 'Để trống nếu không muốn đổi mật khẩu' : undefined)"
        >
          <n-input
            v-model:value="formData.password"
            type="password"
            show-password-on="click"
            :placeholder="isEdit ? 'Nhập mật khẩu mới' : 'Tối thiểu 8 ký tự'"
            @input="clearBackendError('password')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Số điện thoại"
          path="phone"
          :validation-status="backendErrors.phone ? 'error' : undefined"
          :feedback="backendErrors.phone"
        >
          <n-input
            v-model:value="formData.phone"
            placeholder="Ví dụ: 0901234567"
            @input="clearBackendError('phone')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Vai trò"
          path="role_id"
          :validation-status="backendErrors.role_id ? 'error' : undefined"
          :feedback="backendErrors.role_id"
        >
          <n-select
            v-model:value="formData.role_id"
            :options="roles"
            placeholder="Chọn vai trò"
            filterable
            @update:value="clearBackendError('role_id')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Giới tính"
          path="gender"
          :validation-status="backendErrors.gender ? 'error' : undefined"
          :feedback="backendErrors.gender"
        >
          <n-select
            v-model:value="formData.gender"
            :options="genderOptions"
            placeholder="Chọn giới tính"
            @update:value="clearBackendError('gender')"
          />
        </n-form-item-gi>
      </n-grid>

      <n-form-item
        label="Trạng thái"
        path="is_active"
        :validation-status="backendErrors.is_active ? 'error' : undefined"
        :feedback="backendErrors.is_active"
      >
        <n-switch v-model:value="formData.is_active" @update:value="clearBackendError('is_active')">
          <template #checked>Hoạt động</template>
          <template #unchecked>Không hoạt động</template>
        </n-switch>
      </n-form-item>
    </n-form>

    <template #action>
      <n-button ghost :disabled="formLoading" @click="showModal = false">Hủy bỏ</n-button>
      <n-button type="primary" :loading="formLoading" @click="handleSubmit">
        {{ submitLabel }}
      </n-button>
    </template>
  </n-modal>
</template>
