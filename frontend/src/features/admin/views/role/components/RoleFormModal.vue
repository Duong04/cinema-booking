<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { roleService } from '@/features/admin/services/role.service'
import { ApiError } from '@/plugins/axios'
import type { Role } from '@/features/admin/types/role.type'

interface RoleForm {
  name: string
  description: string
}

const props = defineProps<{
  role?: Role | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const formData = reactive<RoleForm>({
  name: '',
  description: '',
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [
    { required: true, message: 'Vui lòng nhập tên vai trò', trigger: 'blur' },
    { min: 3, message: 'Tên vai trò phải ít nhất 3 ký tự', trigger: 'blur' }
  ],
}

function clearFieldError(field: keyof RoleForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.role) {
    isEdit.value = true
    formData.name = props.role.name
    formData.description = props.role.description ?? ''
  } else {
    isEdit.value = false
    formData.name = ''
    formData.description = ''
  }
  
  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
  nextTick(() => formRef.value?.restoreValidation())
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()

    formLoading.value = true
    Object.keys(backendErrors).forEach((k) => delete backendErrors[k])

    const payload = { ...formData }

    if (isEdit.value && props.role?.id) {
      await roleService.updateRole(props.role.id, payload as Role)
      message.success('Cập nhật vai trò thành công')
    } else {
      await roleService.createRole(payload as Role)
      message.success('Tạo vai trò mới thành công')
    }

    showModal.value = false
    emit('success')
  } catch (err) {
    if (err instanceof ApiError && err.status === 422 && err.errors) {
      Object.entries(err.errors).forEach(([field, messages]) => {
        backendErrors[field] = (messages as string[])[0] ?? ''
      })
      message.error('Dữ liệu không hợp lệ, vui lòng kiểm tra lại')
    } else {
      message.error(err instanceof ApiError ? err.message : 'Đã có lỗi hệ thống xảy ra')
    }
  } finally {
    formLoading.value = false
  }
}

watch(() => props.role, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa vai trò' : 'Tạo vai trò mới'"
    :show-icon="false"
    style="width: 550px"
    @after-leave="syncFormWithProps" 
  >
    <n-form
      ref="formRef"
      :model="formData"
      :rules="formRules"
      label-placement="top"
      require-mark-placement="right-hanging"
      class="mt-4"
    >
      <n-form-item
        label="Tên vai trò"
        path="name"
        :validation-status="backendErrors.name ? 'error' : undefined"
        :feedback="backendErrors.name"
      >
        <n-input
          v-model:value="formData.name"
          placeholder="Ví dụ: Admin, Editor..."
          @input="clearFieldError('name')"
        />
      </n-form-item>

      <n-form-item
        label="Mô tả chi tiết"
        path="description"
        :validation-status="backendErrors.description ? 'error' : undefined"
        :feedback="backendErrors.description"
      >
        <n-input
          v-model:value="formData.description"
          type="textarea"
          placeholder="Nhập mô tả về quyền hạn của vai trò này"
          :rows="3"
          @input="clearFieldError('description')"
        />
      </n-form-item>
    </n-form>

    <template #action>
      <n-button ghost @click="showModal = false">
        Hủy bỏ
      </n-button>
      <n-button 
        type="primary" 
        :loading="formLoading" 
        @click="handleSubmit"
      >
        {{ isEdit ? 'Cập nhật ngay' : 'Tạo vai trò' }}
      </n-button>
    </template>
  </n-modal>
</template>