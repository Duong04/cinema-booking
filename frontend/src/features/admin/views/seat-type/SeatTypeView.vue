<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed, resolveComponent } from 'vue'
import { NButton } from 'naive-ui'
import { useSeatType } from '../../composables/useSeatType'
import SeatTypeFormModal from './components/SeatTypeFormModal.vue'
import type { SeatType } from '../../types/seat-type.type'
import { formatDate } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'

const { data, loading, filters, pagination, fetchSeatTypes, deleteSeatType } = useSeatType()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedSeatType = ref<SeatType | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)

function createColumns(): DataTableColumns<SeatType> {
  return [
    { type: 'selection' },
    { title: 'Name', key: 'name' },
    {
      title: 'Base Multiplier',
      key: 'base_multiplier',
      render: (row) =>
        h(
          resolveComponent('n-tag'),
          {
            round: true,
            type: 'primary',
          },
          { default: () => row.base_multiplier },
        ),
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
  selectedSeatType.value = null
  showModal.value = true
}

function openEditModal(row: SeatType) {
  selectedSeatType.value = row
  showModal.value = true
}

function handleDelete(row: SeatType) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa loại ghế "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteSeatType(row.id)
        message.success('Xóa loại ghế thành công')
        fetchSeatTypes()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa loại ghế')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} loại ghế đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteSeatType(id as string)))
        message.success(`Đã xóa ${count} loại ghế thành công`)
        checkedRowKeysRef.value = []
        fetchSeatTypes()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các loại ghế')
      }
    },
  })
}

onMounted(fetchSeatTypes)
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
      <n-button v-if="hasChecked" type="error" @click="handleDeleteMultiple">
        Xóa {{ checkedRowKeysRef.length }} mục đã chọn
      </n-button>
    </n-space>
    <n-button type="primary" @click="openCreateModal">+ Tạo loại ghế</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: SeatType) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <SeatTypeFormModal
    v-model:show="showModal"
    :seatType="selectedSeatType"
    @success="fetchSeatTypes"
  />
</template>
