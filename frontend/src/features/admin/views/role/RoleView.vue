<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed } from 'vue'
import { NButton } from 'naive-ui'
import { useRole } from '../../composables/useRole'
import RoleFormModal from './components/RoleFormModal.vue'
import type { Role } from '../../types/role.type'
import { formatDate } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'

const { data, loading, filters, pagination, fetchRoles, deleteRole } = useRole()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedRole = ref<Role | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)

function createColumns(): DataTableColumns<Role> {
  return [
    { type: 'selection' },
    { title: 'Name', key: 'name' },
    {
      title: 'Description',
      key: 'description',
      render: (row) => h('span', row.description || 'N/A'),
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
  selectedRole.value = null
  showModal.value = true
}

function openEditModal(row: Role) {
  selectedRole.value = row
  showModal.value = true
}

function handleDelete(row: Role) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa vai trò "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteRole(row.id)
        message.success('Xóa vai trò thành công')
        fetchRoles()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa vai trò')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} vai trò đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteRole(id as string)))
        message.success(`Đã xóa ${count} vai trò thành công`)
        checkedRowKeysRef.value = []
        fetchRoles()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các vai trò')
      }
    },
  })
}

onMounted(fetchRoles)
</script>

<template>
  <n-space justify="space-between" class="mb-4">
    <n-space>
      <n-input
        v-model:value="filters.search"
        placeholder="Tìm kiếm theo tên..."
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
    <n-button type="primary" @click="openCreateModal">+ Tạo vai trò</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Role) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <RoleFormModal v-model:show="showModal" :role="selectedRole" @success="fetchRoles" />
</template>
