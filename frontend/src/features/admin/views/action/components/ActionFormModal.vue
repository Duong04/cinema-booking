<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { actionService } from '@/features/admin/services/action.service'
import { ApiError } from '@/plugins/axios'
import type { Action } from '@/features/admin/types/action.type'

interface ActionForm {
  name: string
  key: string
}

const props = defineProps<{
  action?: Action | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const formData = reactive<ActionForm>({
  name: '',
  key: '',
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [
    { required: true, message: 'Vui lòng nhập tên hành động', trigger: 'blur' },
    { min: 3, message: 'Tên hành động phải ít nhất 3 ký tự', trigger: 'blur' }
  ],
  key: [
    { required: true, message: 'Vui lòng nhập khóa hành động', trigger: 'blur' },
    { pattern: /^[a-zA-Z0-9_]+$/, message: 'Khóa hành động chỉ được chứa chữ cái, số và dấu gạch dưới', trigger: 'blur' }
  ],
}

function clearFieldError(field: keyof ActionForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.action) {
    isEdit.value = true
    formData.name = props.action.name
    formData.key = props.action.key
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

    if (isEdit.value && props.action?.id) {
      await actionService.updateAction(props.action.id, payload as Action)
      message.success('Cập nhật hành động thành công')
    } else {
      await actionService.createAction(payload as Action)
      message.success('Tạo hành động mới thành công')
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

watch(() => props.action, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa hành động' : 'Tạo hành động mới'"
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
        label="Tên hành động"
        path="name"
        :validation-status="backendErrors.name ? 'error' : undefined"
        :feedback="backendErrors.name"
      >
        <n-input
          v-model:value="formData.name"
          placeholder="Ví dụ: Create, Update..."
          @input="clearFieldError('name')"
        />
      </n-form-item>

      <n-form-item
        label="Khóa hành động"
        path="key"
        :validation-status="backendErrors.key ? 'error' : undefined"
        :feedback="backendErrors.key"
      >
        <n-input
          v-model:value="formData.key"
          placeholder="Ví dụ: create, update..."
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
        {{ isEdit ? 'Cập nhật ngay' : 'Tạo hành động' }}
      </n-button>
    </template>
  </n-modal>
</template>