<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed } from 'vue'
import { NButton } from 'naive-ui'
import { useAction } from '../../composables/useAction'
import ActionFormModal from './components/ActionFormModal.vue'
import type { Action } from '../../types/action.type'
import { formatDate } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'

const { data, loading, filters, pagination, fetchActions, deleteAction } = useAction()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedAction = ref<Action | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)

function createColumns(): DataTableColumns<Action> {
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
  selectedAction.value = null
  showModal.value = true
}

function openEditModal(row: Action) {
  selectedAction.value = row
  showModal.value = true
}

function handleDelete(row: Action) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa hành động "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteAction(row.id)
        message.success('Xóa hành động thành công')
        fetchActions()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa hành động')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} hành động đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteAction(id as string)))
        message.success(`Đã xóa ${count} hành động thành công`)
        checkedRowKeysRef.value = []
        fetchActions()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các hành động')
      }
    },
  })
}

onMounted(fetchActions)
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
    :row-key="(row: Action) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <ActionFormModal v-model:show="showModal" :action="selectedAction" @success="fetchActions" />
</template>
