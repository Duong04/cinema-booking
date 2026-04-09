<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { permissionService } from '@/features/admin/services/permission.service'
import { ApiError } from '@/plugins/axios'
import type { Permission } from '@/features/admin/types/permission.type'

interface PermissionForm {
  name: string
  key: string
}

const props = defineProps<{
  permission?: Permission | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const formData = reactive<PermissionForm>({
  name: '',
  key: '',
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [
    { required: true, message: 'Vui lòng nhập tên quyền', trigger: 'blur' },
    { min: 3, message: 'Tên quyền phải ít nhất 3 ký tự', trigger: 'blur' }
  ],
  key: [
    { required: true, message: 'Vui lòng nhập khóa quyền', trigger: 'blur' },
    { pattern: /^[a-zA-Z0-9_]+$/, message: 'Khóa quyền chỉ được chứa chữ cái, số và dấu gạch dưới', trigger: 'blur' }
  ],
}

function clearFieldError(field: keyof PermissionForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.permission) {
    isEdit.value = true
    formData.name = props.permission.name
    formData.key = props.permission.key
  } else {
    isEdit.value = false
    formData.name = ''
    formData.key = ''
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

    if (isEdit.value && props.permission?.id) {
      await permissionService.updatePermission(props.permission.id, payload as Permission)
      message.success('Cập nhật quyền thành công')
    } else {
      await permissionService.createPermission(payload as Permission)
      message.success('Tạo quyền mới thành công')
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

watch(() => props.permission, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa quyền' : 'Tạo quyền mới'"
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
        label="Tên quyền"
        path="name"
        :validation-status="backendErrors.name ? 'error' : undefined"
        :feedback="backendErrors.name"
      >
        <n-input
          v-model:value="formData.name"
          placeholder="Ví dụ: Users Management..."
          @input="clearFieldError('name')"
        />
      </n-form-item>

      <n-form-item
        label="Khóa quyền"
        path="key"
        :validation-status="backendErrors.key ? 'error' : undefined"
        :feedback="backendErrors.key"
      >
        <n-input
          v-model:value="formData.key"
          placeholder="Nhập khóa quyền"
          @input="clearFieldError('key')"
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
        {{ isEdit ? 'Cập nhật ngay' : 'Tạo quyền mới' }}
      </n-button>
    </template>
  </n-modal>
</template>