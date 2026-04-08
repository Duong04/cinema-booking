<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h } from 'vue'
import { NButton } from 'naive-ui'
import { useRole } from '../../composables/useRole'
import RoleFormModal from '../../components/modal/RoleFormModal.vue'
import type { Role } from '../../types/role.type'
import { formatDate } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'

const { data, loading, filters, pagination, fetchRoles, handleDelete } = useRole()

const showModal = ref(false)
const selectedRole = ref<Role | null>(null)

function openCreateModal() {
  selectedRole.value = null
  showModal.value = true
}

function openEditModal(row: Role) {
  selectedRole.value = row
  showModal.value = true
}

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
            { size: 'small', type: 'primary', onClick: () => openEditModal(row) },
            { default: () => 'Edit' },
          ),
          h(
            NButton,
            { size: 'small', type: 'error', onClick: () => handleDelete(row) },
            { default: () => 'Delete' },
          ),
        ]),
    },
  ]
}

const columns = createColumns()
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

onMounted(fetchRoles)
</script>

<template>
  <n-space justify="space-between" class="mb-4">
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
    <n-button type="primary" @click="openCreateModal">+ Create Role</n-button>
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
