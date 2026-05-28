<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { computed, h, onMounted, ref, resolveComponent } from 'vue'
import { NButton, useDialog, useMessage } from 'naive-ui'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { usePromotion } from '@/features/admin/composables/usePromotion'
import type { Promotion } from '@/features/admin/types/promotion.type'
import { formatDateTime } from '@/shared/utils/formatDate'
import PromotionFormModal from './components/PromotionFormModal.vue'
import { useAdminPermission } from '@/features/admin/composables/useAdminPermission'
import { ADMIN_ACTIONS, ADMIN_PERMISSIONS } from '@/features/admin/configs/access-control.config'

const { data, loading, filters, pagination, fetchPromotions, deletePromotion } = usePromotion()
const { can } = useAdminPermission()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedPromotion = ref<Promotion | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)
const canCreate = computed(() => can(ADMIN_PERMISSIONS.PROMOTIONS, ADMIN_ACTIONS.CREATE))
const canUpdate = computed(() => can(ADMIN_PERMISSIONS.PROMOTIONS, ADMIN_ACTIONS.UPDATE))
const canDelete = computed(() => can(ADMIN_PERMISSIONS.PROMOTIONS, ADMIN_ACTIONS.DELETE))

const statusOptions = [
  { label: 'Hoạt động', value: 'active' },
  { label: 'Tạm dừng', value: 'paused' },
  { label: 'Hết hạn', value: 'expired' },
]

const applicableOptions = [
  { label: 'Toàn bộ booking', value: 'booking' },
  { label: 'Chỉ tiền vé', value: 'ticket' },
  { label: 'Chỉ combo', value: 'combo' },
]

function formatDiscount(row: Promotion) {
  if (row.discount_type === 'percentage') return `${Number(row.discount_value)}%`
  return Number(row.discount_value ?? 0).toLocaleString('vi-VN') + 'đ'
}

function statusType(status: Promotion['status']) {
  if (status === 'active') return 'success'
  if (status === 'expired') return 'error'
  return 'warning'
}

function statusLabel(status: Promotion['status']) {
  if (status === 'active') return 'Hoạt động'
  if (status === 'expired') return 'Hết hạn'
  return 'Tạm dừng'
}

function applicableLabel(value: Promotion['applicable_to']) {
  if (value === 'ticket') return 'Tiền vé'
  if (value === 'combo') return 'Combo'
  return 'Booking'
}

function createColumns(): DataTableColumns<Promotion> {
  return [
    { type: 'selection' },
    {
      title: 'Code',
      key: 'code',
      render: (row) => h('strong', { style: 'letter-spacing: .04em' }, row.code),
    },
    {
      title: 'Giảm giá',
      key: 'discount',
      render: (row) => h('span', { style: 'font-weight: 700; color: #dc2626' }, formatDiscount(row)),
    },
    {
      title: 'Áp dụng',
      key: 'applicable_to',
      render: (row) =>
        h(
          resolveComponent('n-tag'),
          { round: true, type: 'info' },
          { default: () => applicableLabel(row.applicable_to) },
        ),
    },
    {
      title: 'Trạng thái',
      key: 'status',
      render: (row) =>
        h(
          resolveComponent('n-tag'),
          { round: true, type: statusType(row.status) },
          { default: () => statusLabel(row.status) },
        ),
    },
    {
      title: 'Hiệu lực',
      key: 'duration',
      render: (row) => h('span', `${formatDateTime(row.start_date)} - ${formatDateTime(row.end_date)}`),
    },
    {
      title: 'Limit',
      key: 'limits',
      render: (row) => h('span', `${row.usage_limit ?? '∞'} / user ${row.per_user_limit ?? '∞'}`),
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
  selectedPromotion.value = null
  showModal.value = true
}

function openEditModal(row: Promotion) {
  selectedPromotion.value = row
  showModal.value = true
}

function handleDelete(row: Promotion) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa mã "${row.code}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deletePromotion(row.id)
        message.success('Xóa mã khuyến mãi thành công')
        fetchPromotions()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa mã khuyến mãi')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} mã đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deletePromotion(id as string)))
        message.success(`Đã xóa ${count} mã khuyến mãi thành công`)
        checkedRowKeysRef.value = []
        fetchPromotions()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các mã khuyến mãi')
      }
    },
  })
}

onMounted(fetchPromotions)
</script>

<template>
  <n-space justify="space-between" class="mb-4">
    <n-space>
      <n-input
        v-model:value="filters.search"
        placeholder="Tìm kiếm theo code..."
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
        v-model:value="filters.applicableTo"
        :options="applicableOptions"
        placeholder="Lọc phạm vi"
        clearable
        style="width: 220px"
      />

      <n-select
        v-model:value="filters.status"
        :options="statusOptions"
        placeholder="Lọc trạng thái"
        clearable
        style="width: 200px"
      />

      <n-button v-if="hasChecked && canDelete" type="error" @click="handleDeleteMultiple">
        Xóa {{ checkedRowKeysRef.length }} mục đã chọn
      </n-button>
    </n-space>

    <n-button v-if="canCreate" type="primary" @click="openCreateModal">+ Tạo mã</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Promotion) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <PromotionFormModal
    v-model:show="showModal"
    :promotion="selectedPromotion"
    @success="fetchPromotions"
  />
</template>
