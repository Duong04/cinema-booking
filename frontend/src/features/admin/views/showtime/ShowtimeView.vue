<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed } from 'vue'
import { NButton, NTag, NSpace, NIcon, useMessage, useDialog } from 'naive-ui'
import { useShowtime } from '../../composables/useShowtime'
import { useMovie } from '../../composables/useMovie'
import { useRoom } from '../../composables/useRoom'
import ShowtimeFormModal from './components/ShowtimeFormModal.vue'
import ShowtimeSeatOverviewModal from './components/ShowtimeSeatOverviewModal.vue'
import type { Showtime, Status } from '../../types/showtime.type'
import { Search as SearchIcon } from '@vicons/ionicons5'

const { data, loading, filters, pagination, fetchShowtimes, deleteShowtime } = useShowtime()
const { data: movieData, fetchMovies } = useMovie()
const { data: roomData, fetchRooms } = useRoom()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const showSeatOverviewModal = ref(false)
const selectedShowtime = ref<Showtime | null>(null)
const selectedSeatOverviewShowtime = ref<Showtime | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)

const STATUS_MAP: Record<
  Status,
  { label: string; type: 'info' | 'success' | 'default' | 'error' | 'warning' }
> = {
  scheduled: { label: 'Sắp chiếu', type: 'info' },
  ongoing: { label: 'Đang chiếu', type: 'success' },
  completed: { label: 'Đã kết thúc', type: 'default' },
  cancelled: { label: 'Đã hủy', type: 'error' },
}

const movieOptions = computed(() => movieData.value.map((c) => ({ label: c.title, value: c.id })))

const roomOptions = computed(() => roomData.value.map((c) => ({ label: c.name, value: c.id })))

function formatVND(value: string | number) {
  return Number(value).toLocaleString('vi-VN') + ' đ'
}

function formatTime(datetimeStr: string) {
  return datetimeStr?.split(' ')[1]?.slice(0, 5) ?? ''
}

function formatShowDate(dateStr: string) {
  if (!dateStr) return ''
  const [y, m, d] = dateStr.split('-')
  return `${d}/${m}/${y}`
}

function createColumns(): DataTableColumns<Showtime> {
  return [
    { type: 'selection' },
    {
      title: 'Phim',
      key: 'movie',
      minWidth: 200,
      render: (row) =>
        h('div', { style: 'display:flex; align-items:center; gap:10px' }, [
          h('img', {
            src: row.movie?.poster_url,
            style: 'width:36px; height:50px; object-fit:cover; border-radius:4px; flex-shrink:0',
          }),
          h('div', [
            h(
              'div',
              { style: 'font-weight:500; font-size:13px; line-height:1.3' },
              row.movie?.title ?? '—',
            ),
            h(
              'div',
              { style: 'font-size:12px; color:#999; margin-top:2px' },
              `${row.movie?.duration_minutes} phút`,
            ),
          ]),
        ]),
    },
    {
      title: 'Phòng chiếu',
      key: 'room',
      width: 130,
      render: (row) =>
        h('div', [
          h('div', { style: 'font-weight:500' }, row.room?.name ?? '—'),
          h('div', { style: 'font-size:12px; color:#999' }, row.room?.type),
        ]),
    },
    {
      title: 'Ngày chiếu',
      key: 'show_date',
      width: 120,
      render: (row) => h('span', formatShowDate(row.show_date)),
    },
    {
      title: 'Giờ chiếu',
      key: 'start_time',
      width: 140,
      render: (row) => h('span', `${formatTime(row.start_time)} → ${formatTime(row.end_time)}`),
    },
    {
      title: 'Giá cơ bản',
      key: 'base_price',
      width: 130,
      render: (row) => h('span', formatVND(row.base_price)),
    },
    {
      title: 'Loại ghế / Giá',
      key: 'prices',
      minWidth: 160,
      render: (row) =>
        h(
          NSpace,
          { vertical: true, size: 4 },
          {
            default: () =>
              row.prices?.length
                ? row.prices.map((p) =>
                    h('div', { style: 'font-size:12px' }, [
                      h('span', { style: 'color:#555' }, `${p.seat_type?.name}: `),
                      h('span', { style: 'font-weight:500' }, formatVND(p.price)),
                    ]),
                  )
                : [h('span', { style: 'color:#ccc; font-size:12px' }, 'Chưa có')],
          },
        ),
    },
    {
      title: 'Trạng thái',
      key: 'status',
      width: 130,
      render: (row) => {
        const s = STATUS_MAP[row.status] ?? { label: row.status, type: 'default' }
        return h(NTag, { type: s.type, size: 'small', round: true }, { default: () => s.label })
      },
    },
    {
      title: 'Thao tác',
      key: 'actions',
      width: 190,
      fixed: 'right',
      render: (row) =>
        h(
          NSpace,
          { size: 8 },
          {
            default: () => [
              h(
                NButton,
                { size: 'small', type: 'info', ghost: true, onClick: () => openSeatOverview(row) },
                { default: () => 'Ghế' },
              ),
              h(
                NButton,
                { size: 'small', type: 'primary', ghost: true, onClick: () => openEditModal(row) },
                { default: () => 'Sửa' },
              ),
              h(
                NButton,
                { size: 'small', type: 'error', ghost: true, onClick: () => handleDelete(row) },
                { default: () => 'Xóa' },
              ),
            ],
          },
        ),
    },
  ]
}

const columns = createColumns()

function openCreateModal() {
  selectedShowtime.value = null
  showModal.value = true
}

function openEditModal(row: Showtime) {
  selectedShowtime.value = row
  showModal.value = true
}

function openSeatOverview(row: Showtime) {
  selectedSeatOverviewShowtime.value = row
  showSeatOverviewModal.value = true
}

function handleDelete(row: Showtime) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa suất chiếu phim "${row.movie?.title}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteShowtime(row.id)
        message.success('Xóa suất chiếu thành công')
        fetchShowtimes()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa suất chiếu')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} suất chiếu đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteShowtime(id as string)))
        message.success(`Đã xóa ${count} suất chiếu thành công`)
        checkedRowKeysRef.value = []
        fetchShowtimes()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các suất chiếu')
      }
    },
  })
}

onMounted(() => {
  Promise.all([fetchShowtimes(), fetchMovies(), fetchRooms()])
})
</script>

<template>
  <n-space justify="space-between" class="mb-4">
    <n-space>
      <n-input
        v-model:value="filters.search"
        placeholder="Tìm theo tên phim..."
        clearable
        style="width: 300px"
      >
        <template #suffix>
          <n-icon><SearchIcon /></n-icon>
        </template>
      </n-input>
      <n-select
        v-model:value="filters.movie_id"
        :options="movieOptions"
        placeholder="Tìm theo phim"
        filterable
        clearable
        style="width: 220px"
      />
      <n-select
        v-model:value="filters.room_id"
        :options="roomOptions"
        placeholder="Tìm theo phòng"
        filterable
        clearable
        style="width: 180px"
      />
      <n-date-picker
        v-model:formatted-value="filters.show_date"
        type="date"
        value-format="yyyy-MM-dd"
        placeholder="Chọn ngày chiếu"
        clearable
      />
      <n-select
        v-model:value="filters.status"
        :options="[
          { label: 'Sắp chiếu', value: 'scheduled' },
          { label: 'Đang chiếu', value: 'ongoing' },
          { label: 'Đã kết thúc', value: 'completed' },
          { label: 'Đã hủy', value: 'cancelled' },
        ]"
        placeholder="Tìm theo trạng thái"
        clearable
        style="width: 180px"
      />
      <n-button v-if="hasChecked" type="error" @click="handleDeleteMultiple">
        Xóa {{ checkedRowKeysRef.length }} mục đã chọn
      </n-button>
    </n-space>
    <n-button type="primary" @click="openCreateModal">+ Tạo suất chiếu</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Showtime) => row.id"
    :scroll-x="1100"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <ShowtimeFormModal
    v-model:show="showModal"
    :showtime="selectedShowtime"
    @success="fetchShowtimes"
    :movies="movieOptions"
    :rooms="roomOptions"
  />

  <ShowtimeSeatOverviewModal
    v-model:show="showSeatOverviewModal"
    :showtime="selectedSeatOverviewShowtime"
  />
</template>
