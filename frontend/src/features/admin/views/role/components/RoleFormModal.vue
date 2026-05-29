<script setup lang="ts">
import { computed, ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { Checkmark } from '@vicons/ionicons5'
import { roleService } from '@/features/admin/services/role.service'
import { permissionService } from '@/features/admin/services/permission.service'
import { ApiError } from '@/plugins/axios'
import type { Role, RolePayload } from '@/features/admin/types/role.type'
import type { Permission } from '@/features/admin/types/permission.type'

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
const permissionLoading = ref(false)
const isEdit = ref(false)
const allPermissions = ref<Permission[]>([])

const formData = reactive<RoleForm>({
  name: '',
  description: '',
})

const backendErrors = reactive<Record<string, string>>({})
const selectedActionIdsByPermission = reactive<Record<string, string[]>>({})

const actionColumns = computed(() => {
  const actions = allPermissions.value.flatMap((permission) => permission.actions ?? [])
  const uniqueActions = new Map<string, { id: string; name: string }>()

  actions.forEach((action) => {
    uniqueActions.set(action.id, {
      id: action.id,
      name: action.name,
    })
  })

  return Array.from(uniqueActions.values())
})

const permissionRows = computed(() =>
  allPermissions.value.map((permission) => ({
    ...permission,
    allowedActionIds: new Set((permission.actions ?? []).map((action) => action.id)),
  })),
)

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

function resetPermissionSelection() {
  Object.keys(selectedActionIdsByPermission).forEach((permissionId) => {
    delete selectedActionIdsByPermission[permissionId]
  })
}

function setSelectedPermissions(role: Role) {
  resetPermissionSelection()

  role.permissions?.forEach((permission) => {
    selectedActionIdsByPermission[permission.id] = permission.actions.map((action) => action.id)
  })
}

async function loadPermissionMatrix() {
  if (allPermissions.value.length) return

  permissionLoading.value = true
  try {
    const permissionRes = await permissionService.getAllPermissions({ limit: 100 })

    allPermissions.value = permissionRes.data
    allPermissions.value.forEach((permission) => {
      selectedActionIdsByPermission[permission.id] ??= []
    })
  } finally {
    permissionLoading.value = false
  }
}

async function syncFormWithProps() {
  await loadPermissionMatrix()

  if (props.role) {
    isEdit.value = true
    formData.name = props.role.name
    formData.description = props.role.description ?? ''
    setSelectedPermissions(props.role)
  } else {
    isEdit.value = false
    formData.name = ''
    formData.description = ''
    resetPermissionSelection()
  }
  
  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
  nextTick(() => formRef.value?.restoreValidation())
}

function selectAllActions(permissionId: string) {
  const permission = allPermissions.value.find((item) => item.id === permissionId)
  selectedActionIdsByPermission[permissionId] = permission?.actions?.map((action) => action.id) ?? []
}

function clearActions(permissionId: string) {
  selectedActionIdsByPermission[permissionId] = []
}

function togglePermissionActions(permissionId: string) {
  const current = selectedActionIdsByPermission[permissionId] ?? []
  const permission = allPermissions.value.find((item) => item.id === permissionId)
  const availableActionIds = permission?.actions?.map((action) => action.id) ?? []

  selectedActionIdsByPermission[permissionId] =
    current.length === availableActionIds.length ? [] : availableActionIds
}

function selectAllPermissions() {
  allPermissions.value.forEach((permission) => {
    selectedActionIdsByPermission[permission.id] = permission.actions?.map((action) => action.id) ?? []
  })
}

function clearAllPermissions() {
  allPermissions.value.forEach((permission) => {
    selectedActionIdsByPermission[permission.id] = []
  })
}

function buildPayload(): RolePayload {
  return {
    name: formData.name,
    description: formData.description || undefined,
    permissions: Object.entries(selectedActionIdsByPermission)
      .filter(([, actionIds]) => actionIds.length > 0)
      .map(([permissionId, actionIds]) => ({
        id: permissionId,
        actions: actionIds.map((actionId) => ({ id: actionId })),
      })),
  }
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()

    formLoading.value = true
    Object.keys(backendErrors).forEach((k) => delete backendErrors[k])

    const payload = buildPayload()

    if (isEdit.value && props.role?.id) {
      await roleService.updateRole(props.role.id, payload)
      message.success('Cập nhật vai trò thành công')
    } else {
      await roleService.createRole(payload)
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

watch(
  () => [props.role?.id, showModal.value],
  () => {
    if (showModal.value) syncFormWithProps()
  },
  { immediate: true },
)
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa vai trò' : 'Tạo vai trò mới'"
    :show-icon="false"
    style="width: min(980px, 92vw)"
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

      <section class="permission-section">
        <div class="permission-section__header">
          <div class="permission-section__label">
            Phân quyền theo hành động
          </div>

          <n-space size="small">
            <n-button size="small" tertiary @click="selectAllPermissions">
              Chọn tất cả
            </n-button>
            <n-button size="small" tertiary @click="clearAllPermissions">
              Bỏ chọn tất cả
            </n-button>
          </n-space>
        </div>

        <n-spin :show="permissionLoading">
          <div class="permission-panel">
            <n-empty
              v-if="!allPermissions.length || !actionColumns.length"
              description="Chưa có quyền để phân quyền"
            />

            <div
              v-else
              class="permission-table"
              :style="{ '--action-count': actionColumns.length }"
            >
              <div class="permission-table__head">
                <div class="permission-table__permission">Permission</div>
                <div
                  v-for="action in actionColumns"
                  :key="action.id"
                  class="permission-table__action"
                >
                  {{ action.name }}
                </div>
                <div class="permission-table__quick"></div>
              </div>

              <div
                v-for="permission in permissionRows"
                :key="permission.id"
                class="permission-row"
              >
                <div class="permission-row__name">
                  <p>{{ permission.name }}</p>
                  <span>{{ permission.key }}</span>
                </div>

                <div
                  v-for="action in actionColumns"
                  :key="action.id"
                  class="permission-row__action"
                >
                  <n-checkbox-group
                    v-if="permission.allowedActionIds.has(action.id)"
                    v-model:value="selectedActionIdsByPermission[permission.id]"
                  >
                    <n-checkbox :value="action.id" />
                  </n-checkbox-group>
                  <span v-else class="permission-row__disabled">-</span>
                </div>

                <button
                  class="permission-row__quick"
                  type="button"
                  @click="togglePermissionActions(permission.id)"
                >
                  <n-icon size="18">
                    <Checkmark />
                  </n-icon>
                </button>
              </div>
            </div>
          </div>
        </n-spin>
      </section>
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

<style scoped>
.permission-section {
  width: 100%;
  margin-top: 2px;
}

.permission-section__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 8px;
}

.permission-section__label {
  color: var(--n-label-text-color);
  font-size: var(--n-label-font-size);
  font-weight: var(--n-label-font-weight);
}

.permission-section :deep(.n-spin-container),
.permission-section :deep(.n-spin-content) {
  width: 100%;
}

.permission-panel {
  width: 100%;
  max-height: 420px;
  overflow: auto;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: transparent;
  scrollbar-width: thin;
  scrollbar-color: rgba(148, 163, 184, 0.35) transparent;
}

.permission-panel::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.permission-panel::-webkit-scrollbar-track {
  background: transparent;
}

.permission-panel::-webkit-scrollbar-thumb {
  border-radius: 999px;
  background: rgba(148, 163, 184, 0.35);
}

.permission-table {
  min-width: 760px;
}

.permission-table__head,
.permission-row {
  display: grid;
  grid-template-columns: minmax(190px, 1.4fr) repeat(var(--action-count), minmax(92px, 1fr)) 40px;
  align-items: center;
  column-gap: 14px;
}

.permission-table__head {
  min-height: 42px;
  padding: 0 12px;
  background: transparent;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  color: var(--n-text-color-disabled);
  font-size: 12px;
  font-weight: 700;
}

.permission-row {
  min-height: 64px;
  padding: 0 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.permission-row:last-child {
  border-bottom: 0;
}

.permission-row__name {
  min-width: 0;
}

.permission-row__name p {
  margin: 0;
  color: var(--n-text-color);
  font-weight: 700;
  line-height: 1.35;
}

.permission-row__name span {
  display: block;
  margin-top: 2px;
  color: var(--n-text-color-disabled);
  font-size: 12px;
  word-break: break-word;
}

.permission-table__action,
.permission-row__action {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 0;
}

.permission-row__disabled {
  color: var(--n-text-color-disabled);
}

.permission-row__quick {
  display: inline-flex;
  width: 28px;
  height: 28px;
  align-items: center;
  justify-content: center;
  border: 0;
  border-radius: 999px;
  color: var(--n-text-color-disabled);
  background: transparent;
  cursor: pointer;
}

.permission-row__quick:hover {
  color: var(--n-primary-color);
  background: rgba(148, 163, 184, 0.12);
}

@media (max-width: 640px) {
  .permission-section__header {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>
