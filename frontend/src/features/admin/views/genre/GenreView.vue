<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed } from 'vue'
import { NButton } from 'naive-ui'
import { useGenre } from '../../composables/useGenre'
import GenreFormModal from './components/GenreFormModal.vue'
import type { Genre } from '../../types/genre.type'
import { formatDate } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'

const { data, loading, filters, pagination, fetchGenres, deleteGenre } = useGenre()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedGenre = ref<Genre | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)

function createColumns(): DataTableColumns<Genre> {
  return [
    { type: 'selection' },
    { title: 'Name', key: 'name' },
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
  selectedGenre.value = null
  showModal.value = true
}

function openEditModal(row: Genre) {
  selectedGenre.value = row
  showModal.value = true
}

function handleDelete(row: Genre) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa thể loại "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteGenre(row.id)
        message.success('Xóa thể loại thành công')
        fetchGenres()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa thể loại')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} thể loại đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteGenre(id as string)))
        message.success(`Đã xóa ${count} thể loại thành công`)
        checkedRowKeysRef.value = []
        fetchGenres()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các thể loại')
      }
    },
  })
}

onMounted(fetchGenres)
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
    <n-button type="primary" @click="openCreateModal">+ Tạo thể loại</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Genre) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <GenreFormModal v-model:show="showModal" :genre="selectedGenre" @success="fetchGenres" />
</template>
