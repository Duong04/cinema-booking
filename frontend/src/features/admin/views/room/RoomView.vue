<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed, resolveComponent } from 'vue'
import { NButton } from 'naive-ui'
import { useRoom } from '../../composables/useRoom'
import { useCinema } from '../../composables/useCinema'
import RoomFormModal from './components/RoomFormModal.vue'
import type { Room } from '../../types/room.type'
import { formatDate } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'
import SeatFormModal from './components/SeatFormModal.vue'

const { data, loading, filters, pagination, fetchRooms, deleteRoom } = useRoom()
const { data: cinemaData, fetchCinemas } = useCinema()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedRoom = ref<Room | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)
const typeColor = {
  '2D': 'primary',
  '3D': 'info',
  IMAX: 'success',
  '4DX': 'warning',
  VIP: 'error',
}

const cinemaOptions = computed(() =>
  cinemaData.value.map((c) => ({ label: c.name, value: c.id }))
)

function createColumns(): DataTableColumns<Room> {
  return [
    { type: 'selection' },
    { title: 'Name', key: 'name' },
    {
      title: 'Type',
      key: 'type',
      render: (row) =>
        h(
          resolveComponent('n-tag'),
          {
            round: true,
            type: typeColor[row.type],
          },
          { default: () => row.type },
        ),
    },
    {
      title: 'Cinema',
      key: 'cinema',
      render: (row) =>
        h('div', { style: 'display: flex; flex-direction: column; gap: 2px' }, [
          h('span', { style: 'font-weight: 600;' }, row.cinema.name),
          h('span', { style: 'font-size: 12px; color: #888;' }, row.cinema.address),
        ]),
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
      title: 'Show Seats',
      key: 'show_seats',
      render: (row) =>
        h(
          NButton,
          { size: 'small', type: 'info', secondary: true, onClick: () => openSeatModal(row) },
          { default: () => 'Seats' },
        ),
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

const showSeatModal = ref(false)
const selectedRoomForSeat = ref<Room | null>(null)

function openSeatModal(row: Room) {
  selectedRoomForSeat.value = row
  showSeatModal.value = true
}

function openCreateModal() {
  selectedRoom.value = null
  showModal.value = true
}

function openEditModal(row: Room) {
  selectedRoom.value = row
  showModal.value = true
}

function handleDelete(row: Room) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa phòng "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteRoom(row.id)
        message.success('Xóa phòng thành công')
        fetchRooms()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa phòng')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} phòng đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteRoom(id as string)))
        message.success(`Đã xóa ${count} phòng thành công`)
        checkedRowKeysRef.value = []
        fetchRooms()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các phòng')
      }
    },
  })
}

onMounted(() => {
  Promise.all([fetchRooms(), fetchCinemas()])
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
      v-model:value="filters.cinema_id"
        placeholder="Chọn rạp phim"
        filterable
        clearable
        :options="cinemaOptions"
      />
      <n-button v-if="hasChecked" type="error" @click="handleDeleteMultiple">
        Xóa {{ checkedRowKeysRef.length }} mục đã chọn
      </n-button>
    </n-space>
    <n-button type="primary" @click="openCreateModal">+ Tạo phòng</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Room) => row.id"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <SeatFormModal v-model:show="showSeatModal" :room="selectedRoomForSeat" />
  <RoomFormModal v-model:show="showModal" :cinemas="cinemaOptions" :room="selectedRoom" @success="fetchRooms" />
</template>
