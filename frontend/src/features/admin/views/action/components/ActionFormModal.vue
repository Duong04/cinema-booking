<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { actionService } from '@/features/admin/services/action.service'
import { permissionService } from '@/features/admin/services/permission.service'
import { ApiError } from '@/plugins/axios'
import type { Action, ActionPayload } from '@/features/admin/types/action.type'
import type { Permission } from '@/features/admin/types/permission.type'

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
const permissionLoading = ref(false)
const isEdit = ref(false)
const allPermissions = ref<Permission[]>([])
const selectedPermissionIds = ref<string[]>([])

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
    selectedPermissionIds.value = []
  }
  
  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
  nextTick(() => formRef.value?.restoreValidation())
}

async function loadPermissions() {
  if (allPermissions.value.length) return

  permissionLoading.value = true
  try {
    const response = await permissionService.getAllPermissions({ limit: 100 })
    allPermissions.value = response.data
  } finally {
    permissionLoading.value = false
  }
}

async function syncActionPermissions() {
  await loadPermissions()

  if (!props.action?.id) return

  const response = await actionService.getActionById(props.action.id)
  selectedPermissionIds.value = response.data.permissions?.map((permission) => permission.id) ?? []
}

function buildPayload(): ActionPayload {
  return {
    name: formData.name,
    key: formData.key,
    permissions: selectedPermissionIds.value.map((permissionId) => ({
      permission_id: permissionId,
    })),
  }
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()

    formLoading.value = true
    Object.keys(backendErrors).forEach((k) => delete backendErrors[k])

    const payload = buildPayload()

    if (isEdit.value && props.action?.id) {
      await actionService.updateAction(props.action.id, payload)
      message.success('Cập nhật hành động thành công')
    } else {
      await actionService.createAction(payload)
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

watch(
  () => [props.action?.id, showModal.value],
  async () => {
    if (!showModal.value) return

    syncFormWithProps()
    await syncActionPermissions()
  },
  { immediate: true },
)
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

      <n-form-item label="Quyền sử dụng hành động này">
        <n-spin :show="permissionLoading">
          <n-empty v-if="!allPermissions.length" description="Chưa có quyền nào" />
          <n-checkbox-group v-else v-model:value="selectedPermissionIds">
            <n-space vertical>
              <n-checkbox
                v-for="permission in allPermissions"
                :key="permission.id"
                :value="permission.id"
              >
                {{ permission.name }} <span class="option-key">({{ permission.key }})</span>
              </n-checkbox>
            </n-space>
          </n-checkbox-group>
        </n-spin>
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

<style scoped>
.option-key {
  color: var(--n-text-color-disabled);
  font-size: 12px;
}
</style>
