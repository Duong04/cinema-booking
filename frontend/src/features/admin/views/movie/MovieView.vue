<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { ref, onMounted, h, computed, resolveComponent } from 'vue'
import { NButton } from 'naive-ui'
import { useMovie } from '../../composables/useMovie'
import MovieFormModal from './components/MovieFormModal.vue'
import type { Movie, Status } from '../../types/movie.type'
import { formatDate } from '@/shared/utils/formatDate'
import { Search as SearchIcon } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'

const { data, loading, filters, pagination, fetchMovies, deleteMovie } = useMovie()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedMovie = ref<Movie | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const hasChecked = computed(() => checkedRowKeysRef.value.length > 0)

function createColumns(): DataTableColumns<Movie> {
  return [
    { type: 'selection' },
    {
      title: 'Poster',
      key: 'poster_url',
      width: 64,
      render: (row) =>
        h('img', {
          src: row.poster_url,
          style:
            'width:44px;height:62px;object-fit:cover;border-radius:4px;border:0.5px solid var(--n-border-color);display:block',
          onError: (e: Event) => {
            const el = e.target as HTMLImageElement
            el.style.display = 'none'
          },
        }),
    },
    {
      title: 'Title',
      key: 'title',
      render: (row) =>
        h('div', [
          h('div', { style: 'font-weight:500;font-size:13px' }, row.title),
          h(
            'div',
            { style: 'font-size:11px;color:var(--n-td-color-secondary, #999);margin-top:2px' },
            row.slug,
          ),
        ]),
    },
    {
      title: 'Genre',
      key: 'genres',
      render: (row) =>
        h(
          'div',
          { style: 'display:flex;gap:4px;flex-wrap:wrap;max-width:200px' },
          row.genres?.map((g) =>
            h(
              'span',
              {
                style:
                  'background:var(--n-action-color);border:0.5px solid var(--n-border-color);color:var(--n-text-color-3);border-radius:20px;padding:2px 8px;font-size:11px;white-space:nowrap',
              },
              g.name,
            ),
          ),
        ),
    },
    {
      title: 'Duration',
      key: 'duration_minutes',
      width: 100,
      render: (row) =>
        h(
          resolveComponent('n-tag'),
          {
            round: true,
            type: 'info',
          },
          () =>
            h(
              'span',
              { style: 'color:var(--n-text-color-3);font-size:13px' },
              `${row.duration_minutes} phút`,
            ),
        ),
    },
    {
      title: 'Status',
      key: 'status',
      width: 120,
      render: (row) => {
        const map: Record<Status, { text: string; bg: string; color: string; dot: string }> = {
          coming_soon: { text: 'Sắp chiếu', bg: '#FFF8E6', color: '#9B6200', dot: '#E5A000' },
          now_showing: { text: 'Đang chiếu', bg: '#EAF7EF', color: '#1A6E3E', dot: '#27AE60' },
          ended: {
            text: 'Ngừng chiếu',
            bg: 'var(--n-action-color)',
            color: 'var(--n-text-color-3)',
            dot: '#aaa',
          },
          cancelled: {
            text: 'Đã hủy',
            bg: '#FFEAEA',
            color: '#B42318',
            dot: '#F04438',
          },
        }
        const s = map[row.status] || map.ended
        return h(
          'span',
          {
            style: `display:inline-flex;align-items:center;gap:5px;background:${s.bg};color:${s.color};padding:3px 9px;border-radius:20px;font-size:11px;font-weight:500`,
          },
          [
            h('span', {
              style: `width:6px;height:6px;border-radius:50%;background:${s.dot};flex-shrink:0`,
            }),
            s.text,
          ],
        )
      },
    },
    {
      title: 'Release Date',
      key: 'release_date',
      width: 110,
      render: (row) =>
        h(
          'span',
          { style: 'font-size:12px;color:var(--n-text-color-3)' },
          formatDate(row.release_date),
        ),
    },
    {
      title: 'Created At',
      key: 'created_at',
      width: 110,
      render: (row) =>
        h(
          'span',
          { style: 'font-size:12px;color:var(--n-text-color-3)' },
          formatDate(row.created_at),
        ),
    },
    {
      title: 'Actions',
      key: 'actions',
      width: 120,
      render: (row) =>
        h('div', { style: 'display:flex;gap:6px' }, [
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
        ]),
    },
  ]
}

const columns = createColumns()

function openCreateModal() {
  selectedMovie.value = null
  showModal.value = true
}

function openEditModal(row: Movie) {
  selectedMovie.value = row
  showModal.value = true
}

function handleDelete(row: Movie) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa phim "${row.title}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteMovie(row.id)
        message.success('Xóa phim thành công')
        fetchMovies()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa phim')
      }
    },
  })
}

function handleDeleteMultiple() {
  const count = checkedRowKeysRef.value.length
  dialog.warning({
    title: 'Xác nhận xóa nhiều',
    content: `Bạn có chắc chắn muốn xóa ${count} phim đã chọn không?`,
    positiveText: 'Xóa tất cả',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await Promise.all(checkedRowKeysRef.value.map((id) => deleteMovie(id as string)))
        message.success(`Đã xóa ${count} phim thành công`)
        checkedRowKeysRef.value = []
        fetchMovies()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa các phim')
      }
    },
  })
}

onMounted(fetchMovies)
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
      <n-button v-if="hasChecked" type="error" @click="handleDeleteMultiple">
        Xóa {{ checkedRowKeysRef.length }} mục đã chọn
      </n-button>
    </n-space>
    <n-button type="primary" @click="openCreateModal">+ Thêm phim mới</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: Movie) => row.id"
    :scroll-x="1000"
    remote
    @update:checked-row-keys="(keys: DataTableRowKey[]) => (checkedRowKeysRef = keys)"
  />

  <MovieFormModal v-model:show="showModal" :movie="selectedMovie" @success="fetchMovies" />
</template>
