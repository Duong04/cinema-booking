<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed } from 'vue'
import { NButton } from 'naive-ui'
import { usePermission } from '../../composables/usePermission'
import PermissionFormModal from './components/PermissionFormModal.vue'
import type { Permission } from '../../types/permission.type'
import { formatDate } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'

const { data, loading, filters, pagination, fetchPermissions, deletePermission } = usePermission()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedPermission = ref<Permission | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)

function createColumns(): DataTableColumns<Permission> {
  return [
    { type: 'selection' },
    { title: 'Name', key: 'name' },
    {
      title: 'Key',
      key: 'key',
      render: (row) => h('span', row.key || 'N/A'),
    },
    {
      title: 'Created At',
      key: 'created_at',
      render: (row) => h('span', formatDate(row.created_at)),
    },
    {
      title: 'Updated At',
      key: 'updated_at',
      render: (row) => h('span', formatDate(row.updated_at)),
    },
    {
      title: 'Actions',
      key: 'actions',
      render: (row) =>
        h('div', { style: 'display: flex; gap: 8px' }, [
          h(
            NButton,
            { size: 'small', type: 'primary', secondary: true, onClick: () => openEditModal(row) },
            { default: () => 'Edit' },
          ),
          h(
            NButton,
            { size: 'small', type: 'error', secondary: true, onClick: () => handleDelete(row) },
            { default: () => 'Delete' },
          ),
        ]),
    },
  ]
}

const columns = createColumns()

function openCreateModal() {
  selectedPermission.value = null
  showModal.value = true
}

function openEditModal(row: Permission) {
  selectedPermission.value = row
  showModal.value = true
}

function handleDelete(row: Permission) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa quyền "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deletePermission(row.id)
        message.success('Xóa quyền thành công')
        fetchPermissions()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa quyền')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} thành phố đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deletePermission(id as string)))
        message.success(`Đã xóa ${count} quyền thành công`)
        checkedRowKeysRef.value = []
        fetchPermissions()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các quyền')
      }
    },
  })
}

onMounted(fetchPermissions)
</script>

<template>
  <n-space justify="space-between" class="mb-4">
    <n-space>
      <n-input
        v-model:value="filters.search"
        placeholder="Search by name..."
        clearable
        style="width: 300px"
      >
        <template #suffix>
          <n-icon>
            <SearchIcon />
          </n-icon>
        </template>
      </n-input>
      <n-button
        v-if="hasChecked"
        type="error"
        @click="handleDeleteMultiple"
      >
        Xóa {{ checkedRowKeysRef.length }} mục đã chọn
      </n-button>
    </n-space>
    <n-button type="primary" @click="openCreateModal">+ Tạo quyền</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Permission) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <PermissionFormModal v-model:show="showModal" :permission="selectedPermission" @success="fetchPermissions" />
</template>
