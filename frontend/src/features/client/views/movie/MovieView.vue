<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import type { SelectOption } from 'naive-ui'
import { useLanguageStore } from '@/stores/language'
import MovieCard from '../../components/ui/MovieCard.vue'
import { Search, SlidersHorizontal, Film, X } from 'lucide-vue-next'
import { useMovie } from '@/features/client/composables/useMovie'
import { useGenre } from '@/features/client/composables/useGenre'
import type { PublicMovieSort } from '@/features/client/types/movie.type'

const languageStore = useLanguageStore()
const searchQuery = ref('')
const selectedGenreId = ref<string | null>(null)
const sortBy = ref<PublicMovieSort>('top_rated')
const activeStatus = ref<'all' | 'now_showing' | 'coming_soon'>('all')
const page = ref(1)
const isClearingFilters = ref(false)
const showGenreFilter = ref(false)
const limit = 10
const {
  allMovies,
  allMoviesMeta,
  loadingAllMovies,
  fetchAllMovies,
} = useMovie()
const { genres: apiGenres, fetchGenres } = useGenre()

const sortOptions = computed<SelectOption[]>(() => [
  { label: languageStore.language === 'en' ? 'Top Rated' : 'Đánh giá cao', value: 'top_rated' },
  { label: languageStore.language === 'en' ? 'Newest' : 'Mới nhất', value: 'release_date_desc' },
  { label: languageStore.language === 'en' ? 'Longest' : 'Dài nhất', value: 'duration_desc' },
])

const visibleGenres = computed(() => apiGenres.value.slice(0, 5))

const filteredMovies = computed(() => {
  return allMovies.value
})

const pageCount = computed(() => allMoviesMeta.value?.last_page ?? 1)
const totalMovies = computed(() => allMoviesMeta.value?.total ?? filteredMovies.value.length)
const selectedGenreName = computed(() => {
  if (!selectedGenreId.value) return languageStore.language === 'en' ? 'All genres' : 'Tất cả thể loại'
  return apiGenres.value.find((genre) => genre.id === selectedGenreId.value)?.name ?? ''
})

function fetchMovies(pageNumber = page.value) {
  page.value = pageNumber
  fetchAllMovies({
    page: page.value,
    limit,
    q: searchQuery.value || undefined,
    status: activeStatus.value === 'all' ? undefined : activeStatus.value,
    genre_id: selectedGenreId.value || undefined,
    sort: sortBy.value,
  })
}

function resetAndFetchMovies() {
  fetchMovies(1)
}

const debouncedSearch = useDebounceFn(resetAndFetchMovies, 500)

const clearFilters = () => {
  isClearingFilters.value = true
  searchQuery.value = ''
  selectedGenreId.value = null
  sortBy.value = 'top_rated'
  activeStatus.value = 'all'
  showGenreFilter.value = false
  fetchMovies(1)
  queueMicrotask(() => {
    isClearingFilters.value = false
  })
}

function selectGenre(genreId: string | null) {
  selectedGenreId.value = genreId
  showGenreFilter.value = false
}

onMounted(() => {
  fetchMovies()
  fetchGenres()
})

watch([sortBy, activeStatus, selectedGenreId], () => {
  if (!isClearingFilters.value) resetAndFetchMovies()
})
watch(searchQuery, () => {
  if (!isClearingFilters.value) debouncedSearch()
})
</script>

<template>
  <div class="min-h-screen bg-zinc-950">
    <!-- Hero Section -->
    <section
      class="relative h-[80vh] md:h-[100vh] flex flex-col items-center justify-center overflow-hidden"
    >
      <div>
        <img
          src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=2070&auto=format&fit=crop"
          class="w-full h-full object-cover opacity-40 grayscale"
          alt="Movies background"
        />
        <div
          class="absolute inset-0 bg-gradient-to-t from-zinc-950/10 via-zinc-950/60 to-transparent"
        />
      </div>

      <div class="absolute z-10 text-center px-4 max-w-4xl mx-auto">
        <h1
          class="text-5xl md:text-8xl font-black text-white uppercase italic tracking-tighter mb-6 drop-shadow-2xl"
        >
          {{ languageStore.t('nav.movies') }}
        </h1>
        <p
          class="text-gray-400 text-lg md:text-xl font-medium mb-12 max-w-2xl mx-auto leading-relaxed"
        >
          {{
            languageStore.language === 'en'
              ? 'Discover the latest blockbusters and cinematic masterpieces.'
              : 'Khám phá những siêu phẩm điện ảnh mới nhất và hấp dẫn nhất.'
          }}
        </p>

        <!-- Status Toggle -->
        <div class="flex flex-wrap items-center justify-center gap-3 mb-12">
          <button
            @click="activeStatus = 'all'"
            :class="[
              'px-8 py-3 rounded-2xl text-sm font-black uppercase tracking-widest transition-all',
              activeStatus === 'all'
                ? 'bg-red-600 text-white shadow-xl shadow-red-600/40'
                : 'bg-white/5 text-gray-400 hover:bg-white/10',
            ]"
          >
            {{ languageStore.language === 'en' ? 'All Movies' : 'Tất cả phim' }}
          </button>
          <button
            @click="activeStatus = 'now_showing'"
            :class="[
              'px-8 py-3 rounded-2xl text-sm font-black uppercase tracking-widest transition-all',
              activeStatus === 'now_showing'
                ? 'bg-red-600 text-white shadow-xl shadow-red-600/40'
                : 'bg-white/5 text-gray-400 hover:bg-white/10',
            ]"
          >
            {{ languageStore.language === 'en' ? 'Now Playing' : 'Đang chiếu' }}
          </button>
          <button
            @click="activeStatus = 'coming_soon'"
            :class="[
              'px-8 py-3 rounded-2xl text-sm font-black uppercase tracking-widest transition-all',
              activeStatus === 'coming_soon'
                ? 'bg-red-600 text-white shadow-xl shadow-red-600/40'
                : 'bg-white/5 text-gray-400 hover:bg-white/10',
            ]"
          >
            {{ languageStore.language === 'en' ? 'Coming Soon' : 'Sắp chiếu' }}
          </button>
        </div>

        <!-- Search Bar -->
        <div class="relative max-w-2xl mx-auto group">
          <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
            <Search
              class="w-6 h-6 text-gray-500 group-focus-within:text-red-600 transition-colors"
            />
          </div>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="
              languageStore.language === 'en'
                ? 'Search for movies, actors, directors...'
                : 'Tìm kiếm phim, diễn viên, đạo diễn...'
            "
            class="w-full bg-white/5 backdrop-blur-2xl border border-white/10 rounded-3xl py-6 pl-16 pr-6 text-white placeholder:text-gray-600 focus:ring-2 focus:ring-red-600/50 focus:border-red-600 outline-none transition-all text-lg shadow-2xl"
          />
        </div>
      </div>
    </section>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20 pb-32">
      <!-- Filter Bar -->
      <div
        class="relative z-50 bg-zinc-900/60 backdrop-blur-3xl border border-white/5 rounded-2xl p-4 mb-12 flex flex-col lg:flex-row items-center justify-between gap-6 shadow-2xl"
      >
        <div class="flex w-full items-center gap-2 overflow-x-auto pb-2 lg:w-auto lg:overflow-visible lg:pb-0 scrollbar-hide">

          <div class="relative shrink-0">
            <button
              @click="showGenreFilter = !showGenreFilter"
              class="flex items-center justify-center gap-3 rounded-xl border border-white/10 bg-white/5 px-5 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-gray-300 transition-all hover:border-red-500/60 hover:bg-red-600/10 hover:text-white"
            >
              <SlidersHorizontal class="w-4 h-4 text-red-600" />
              {{ selectedGenreName }}
            </button>

            <div
              v-if="showGenreFilter"
              class="absolute left-0 top-full z-[999] mt-3 w-[min(88vw,420px)] rounded-2xl border border-white/10 bg-zinc-950/95 p-3 shadow-2xl shadow-black/40 backdrop-blur-2xl"
            >
              <div class="max-h-80 overflow-y-auto pr-1 genre-filter-scroll">
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                  <button
                    @click="selectGenre(null)"
                    :class="[
                      'rounded-xl border px-3 py-2 text-left text-[10px] font-black uppercase tracking-[0.12em] transition-all',
                      selectedGenreId === null
                        ? 'border-red-600 bg-red-600 text-white shadow-lg shadow-red-600/20'
                        : 'border-white/5 bg-white/5 text-gray-400 hover:border-white/10 hover:text-white',
                    ]"
                  >
                    {{ languageStore.language === 'en' ? 'All' : 'Tất cả' }}
                  </button>
                  <button
                    v-for="genre in apiGenres"
                    :key="genre.id"
                    @click="selectGenre(genre.id)"
                    :class="[
                      'rounded-xl border px-3 py-2 text-left text-[10px] font-black uppercase tracking-[0.12em] transition-all',
                      selectedGenreId === genre.id
                        ? 'border-red-600 bg-red-600 text-white shadow-lg shadow-red-600/20'
                        : 'border-white/5 bg-white/5 text-gray-400 hover:border-white/10 hover:text-white',
                    ]"
                  >
                    {{ genre.name }}
                  </button>
                </div>
              </div>
            </div>
          </div>
          <button
            @click="selectGenre(null)"
            :class="[
              'shrink-0 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] transition-all whitespace-nowrap border',
              selectedGenreId === null
                ? 'bg-red-600 border-red-600 text-white shadow-lg shadow-red-600/20'
                : 'bg-white/5 border-white/5 text-gray-400 hover:border-white/10 hover:text-white',
            ]"
          >
            {{ languageStore.language === 'en' ? 'All' : 'Tất cả' }}
          </button>
          <button
            v-for="genre in visibleGenres"
            :key="genre.id"
            @click="selectGenre(genre.id)"
            :class="[
              'shrink-0 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] transition-all whitespace-nowrap border',
              selectedGenreId === genre.id
                ? 'bg-red-600 border-red-600 text-white shadow-lg shadow-red-600/20'
                : 'bg-white/5 border-white/5 text-gray-400 hover:border-white/10 hover:text-white',
            ]"
          >
            {{ genre.name }}
          </button>
        </div>

        <div
          class="flex items-center gap-6 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 border-white/5 pt-4 lg:pt-0"
        >
          <div class="flex items-center gap-3">
            <n-select
              v-model:value="sortBy"
              :options="sortOptions"
              size="small"
              class="movie-sort-select"
              :bordered="false"
              :consistent-menu-width="false"
            />
          </div>

          <div class="h-6 w-px bg-white/10 hidden sm:block" />

          <p class="text-gray-600 text-[10px] font-black uppercase tracking-widest">
            {{ totalMovies }}
            {{ languageStore.language === 'en' ? 'Results' : 'Kết quả' }}
          </p>
        </div>
      </div>

      <div class="relative">
        <div
          v-if="loadingAllMovies"
          class="absolute inset-x-0 top-8 z-30 flex justify-center pointer-events-none"
        >
          <div
            class="flex h-12 w-12 items-center justify-center rounded-full border border-red-500/30 bg-zinc-950/85 shadow-2xl shadow-red-950/30 backdrop-blur-xl"
          >
            <span class="movie-loading-spinner" />
          </div>
        </div>

        <!-- Movies Grid -->
        <div
          v-if="filteredMovies.length > 0"
          :class="[
            'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6 lg:gap-8 transition-all duration-300',
            loadingAllMovies ? 'opacity-45 blur-[1px]' : 'opacity-100 blur-0',
          ]"
        >
          <MovieCard v-for="movie in filteredMovies" :key="movie.id" :movie="movie" />
        </div>

        <!-- Empty State -->
        <div
          v-else
          :class="[
            'text-center py-32 bg-zinc-900/30 rounded-[4rem] border border-dashed border-white/10 transition-opacity duration-300',
            loadingAllMovies ? 'opacity-45' : 'opacity-100',
          ]"
        >
          <div
            class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-8"
          >
            <Film class="w-12 h-12 text-gray-600" />
          </div>
          <h3 class="text-3xl font-black text-white mb-4 uppercase italic">
            {{ languageStore.language === 'en' ? 'No Movies Found' : 'Không tìm thấy phim' }}
          </h3>
          <p class="text-gray-500 mb-12 max-w-md mx-auto">
            {{
              languageStore.language === 'en'
                ? 'Try adjusting your search or filters to find what you are looking for.'
                : 'Hãy thử điều chỉnh tìm kiếm hoặc bộ lọc để tìm thấy nội dung bạn muốn.'
            }}
          </p>
          <button
            @click="clearFilters"
            class="px-10 py-4 bg-white text-black rounded-2xl font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center gap-3 mx-auto"
          >
            <X class="w-5 h-5" />
            {{ languageStore.language === 'en' ? 'Clear All Filters' : 'Xóa tất cả bộ lọc' }}
          </button>
        </div>
      </div>

      <div v-if="pageCount > 1" class="mt-12 flex justify-center">
        <n-pagination
          v-model:page="page"
          :page-count="pageCount"
          :page-slot="6"
          :disabled="loadingAllMovies"
          class="movie-pagination"
          @update:page="fetchMovies"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.genre-filter-scroll::-webkit-scrollbar {
  width: 6px;
}

.genre-filter-scroll::-webkit-scrollbar-thumb {
  border-radius: 9999px;
  background-color: rgba(220, 38, 38, 0.7);
}

.genre-filter-scroll::-webkit-scrollbar-track {
  background-color: rgba(255, 255, 255, 0.06);
  border-radius: 9999px;
}

.movie-sort-select {
  width: 150px;
}

.movie-sort-select :deep(.n-base-selection) {
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 0.75rem;
}

.movie-sort-select :deep(.n-base-selection-label) {
  padding: 0 0.25rem;
}

.movie-sort-select :deep(.n-base-selection-input),
.movie-sort-select :deep(.n-base-selection-placeholder) {
  color: rgb(212 212 216);
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.movie-sort-select :deep(.n-base-suffix) {
  color: rgb(220 38 38);
}

.movie-pagination :deep(.n-pagination-item) {
  border-color: rgba(255, 255, 255, 0.08);
  background-color: rgba(255, 255, 255, 0.05);
  color: rgb(212 212 216);
  font-weight: 800;
  transition:
    border-color 0.2s ease,
    background-color 0.2s ease,
    color 0.2s ease,
    transform 0.2s ease;
}

.movie-pagination :deep(.n-pagination-item:not(.n-pagination-item--disabled):hover) {
  border-color: rgba(220, 38, 38, 0.65);
  background-color: rgba(220, 38, 38, 0.14);
  color: white;
  transform: translateY(-1px);
}

.movie-pagination :deep(.n-pagination-item.n-pagination-item--active) {
  border-color: transparent;
  background-color: rgb(220 38 38);
  color: white;
  box-shadow: 0 12px 28px rgba(220, 38, 38, 0.28);
}

.movie-pagination :deep(.n-pagination-item.n-pagination-item--disabled) {
  opacity: 0.35;
}

.movie-loading-spinner {
  width: 1.35rem;
  height: 1.35rem;
  border-radius: 9999px;
  border: 3px solid rgba(255, 255, 255, 0.16);
  border-top-color: rgb(220 38 38);
  animation: movie-loading-spin 0.75s linear infinite;
}

@keyframes movie-loading-spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
