<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed } from 'vue'
import { NButton, NAvatar } from 'naive-ui'
import { useCinema } from '../../composables/useCinema'
import CinemaFormModal from './components/CinemaFormModal.vue'
import type { Cinema } from '../../types/cinema.type'
import { formatDateTime } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'
import { useAdminPermission } from '../../composables/useAdminPermission'
import { ADMIN_ACTIONS, ADMIN_PERMISSIONS } from '@/features/admin/configs/access-control.config'

const { data, loading, filters, pagination, fetchCinemas, deleteCinema } = useCinema()
const { can } = useAdminPermission()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedCinema = ref<Cinema | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)
const canCreate = computed(() => can(ADMIN_PERMISSIONS.CINEMAS, ADMIN_ACTIONS.CREATE))
const canUpdate = computed(() => can(ADMIN_PERMISSIONS.CINEMAS, ADMIN_ACTIONS.UPDATE))
const canDelete = computed(() => can(ADMIN_PERMISSIONS.CINEMAS, ADMIN_ACTIONS.DELETE))

function createColumns(): DataTableColumns<Cinema> {
  return [
    { type: 'selection' },
    { title: 'Name', key: 'name' },
    {
      title: 'Chain',
      key: 'cinema_chain',
      render: (row) =>
        h('div', { style: 'display: flex; align-items: center; gap: 8px' }, [
          h(NAvatar, {
            src: row.cinema_chain?.logo ?? '',
            size: 28,
            round: true,
            objectFit: 'cover',
          }),
          h('span', row.cinema_chain?.name ?? '—'),
        ]),
    },
    {
      title: 'Address',
      key: 'address',
    },
    {
      title: 'City',
      key: 'city',
      render: (row) => h('span', row.city?.name ?? '—'),
    },
    {
      title: 'Created At',
      key: 'created_at',
      render: (row) => h('span', formatDateTime(row.created_at)),
    },
    {
      title: 'Updated At',
      key: 'updated_at',
      render: (row) => h('span', formatDateTime(row.updated_at)),
    },
    {
      title: 'Actions',
      key: 'actions',
      render: (row) =>
        h('div', { style: 'display: flex; gap: 8px' }, [
          ...(canUpdate.value
            ? [
                h(
                  NButton,
                  { size: 'small', type: 'primary', secondary: true, onClick: () => openEditModal(row) },
                  { default: () => 'Edit' },
                ),
              ]
            : []),
          ...(canDelete.value
            ? [
                h(
                  NButton,
                  { size: 'small', type: 'error', secondary: true, onClick: () => handleDelete(row) },
                  { default: () => 'Delete' },
                ),
              ]
            : []),
        ]),
    },
  ]
}

const columns = createColumns()

function openCreateModal() {
  selectedCinema.value = null
  showModal.value = true
}

function openEditModal(row: Cinema) {
  selectedCinema.value = row
  showModal.value = true
}

function handleDelete(row: Cinema) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa rạp phim "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteCinema(row.id)
        message.success('Xóa rạp phim thành công')
        fetchCinemas()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa rạp phim')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} rạp phim đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteCinema(id as string)))
        message.success(`Đã xóa ${count} rạp phim thành công`)
        checkedRowKeysRef.value = []
        fetchCinemas()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các rạp phim')
      }
    },
  })
}

onMounted(fetchCinemas)
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
      <n-button v-if="hasChecked && canDelete" type="error" @click="handleDeleteMultiple">
        Xóa {{ checkedRowKeysRef.length }} mục đã chọn
      </n-button>
    </n-space>
    <n-button v-if="canCreate" type="primary" @click="openCreateModal">+ Tạo rạp phim</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Cinema) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <CinemaFormModal v-model:show="showModal" :cinema="selectedCinema" @success="fetchCinemas" />
</template>
