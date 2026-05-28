<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { computed, h, onMounted, ref, resolveComponent } from 'vue'
import { NAvatar, NButton, NIcon, useDialog, useMessage } from 'naive-ui'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { useCombo } from '../../composables/useCombo'
import { useCinema } from '../../composables/useCinema'
import type { Combo } from '../../types/combo.type'
import { formatDateTime } from '@/shared/utils/formatDate'
import ComboFormModal from './components/ComboFormModal.vue'
import { useAdminPermission } from '../../composables/useAdminPermission'
import { ADMIN_ACTIONS, ADMIN_PERMISSIONS } from '@/features/admin/configs/access-control.config'

const { data, loading, filters, pagination, fetchCombos, deleteCombo } = useCombo()
const { data: cinemas, fetchCinemas } = useCinema()
const { can } = useAdminPermission()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedCombo = ref<Combo | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)
const canCreate = computed(() => can(ADMIN_PERMISSIONS.COMBOS, ADMIN_ACTIONS.CREATE))
const canUpdate = computed(() => can(ADMIN_PERMISSIONS.COMBOS, ADMIN_ACTIONS.UPDATE))
const canDelete = computed(() => can(ADMIN_PERMISSIONS.COMBOS, ADMIN_ACTIONS.DELETE))
const cinemaOptions = computed(() =>
  cinemas.value.map((cinema) => ({
    label: cinema.name,
    value: cinema.id,
  })),
)

function formatVND(value: number | string) {
  return Number(value ?? 0).toLocaleString('vi-VN') + 'đ'
}

function createColumns(): DataTableColumns<Combo> {
  return [
    { type: 'selection' },
    {
      title: 'Image',
      key: 'image',
      width: 90,
      render: (row) =>
        h(NAvatar, {
          src: row.image ?? '',
          size: 48,
          objectFit: 'cover',
          style: 'border-radius: 8px',
        }),
    },
    { title: 'Name', key: 'name' },
    {
      title: 'Cinema',
      key: 'cinema',
      render: (row) => h('span', row.cinema?.name ?? '—'),
    },
    {
      title: 'Price',
      key: 'price',
      render: (row) => h('strong', { style: 'color: #dc2626' }, formatVND(row.price)),
    },
    {
      title: 'Status',
      key: 'status',
      render: (row) =>
        h(
          resolveComponent('n-tag'),
          {
            round: true,
            type: row.status === 'active' ? 'success' : 'default',
          },
          { default: () => (row.status === 'active' ? 'Hoạt động' : 'Không hoạt động') },
        ),
    },
    {
      title: 'Created At',
      key: 'created_at',
      render: (row) => h('span', formatDateTime(row.created_at)),
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
  selectedCombo.value = null
  showModal.value = true
}

function openEditModal(row: Combo) {
  selectedCombo.value = row
  showModal.value = true
}

function handleDelete(row: Combo) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa combo "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteCombo(row.id)
        message.success('Xóa combo thành công')
        fetchCombos()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa combo')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} combo đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteCombo(id as string)))
        message.success(`Đã xóa ${count} combo thành công`)
        checkedRowKeysRef.value = []
        fetchCombos()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các combo')
      }
    },
  })
}

onMounted(() => {
  fetchCombos()
  fetchCinemas()
})
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

      <n-select
        v-model:value="filters.cinemaId"
        :options="cinemaOptions"
        placeholder="Lọc theo rạp"
        clearable
        filterable
        style="width: 260px"
      />

      <n-select
        v-model:value="filters.status"
        :options="[
          { label: 'Hoạt động', value: 'active' },
          { label: 'Không hoạt động', value: 'inactive' },
        ]"
        placeholder="Lọc theo trạng thái"
        clearable
        style="width: 200px"
      />

      <n-button v-if="hasChecked && canDelete" type="error" @click="handleDeleteMultiple">
        Xóa {{ checkedRowKeysRef.length }} mục đã chọn
      </n-button>
    </n-space>

    <n-button v-if="canCreate" type="primary" @click="openCreateModal">+ Tạo combo</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Combo) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <ComboFormModal
    v-model:show="showModal"
    :combo="selectedCombo"
    :cinemas="cinemas"
    @success="fetchCombos"
  />
</template>
