<script setup lang="ts">
import { ref, computed } from 'vue'
import { MOVIES } from '@/data/mockData'
import { useLanguageStore } from '@/stores/shared/language'
import MovieCard from '@/components/client/ui/MovieCard.vue'
import { Search, SlidersHorizontal, Film, X } from 'lucide-vue-next'

const languageStore = useLanguageStore()
const searchQuery = ref('')
const selectedGenre = ref('All')
const sortBy = ref('rating')
const activeStatus = ref<'now-playing' | 'coming-soon'>('now-playing')

const genres = computed(() => {
  const allGenres = MOVIES.flatMap((m) => (languageStore.language === 'en' ? m.genres : m.genresVi))
  return ['All', ...new Set(allGenres)]
})

const filteredMovies = computed(() => {
  const result = MOVIES.filter((movie) => {
    const title = languageStore.language === 'en' ? movie.title : movie.titleVi
    const matchesSearch = title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const movieGenres = languageStore.language === 'en' ? movie.genres : movie.genresVi
    const matchesGenre = selectedGenre.value === 'All' || movieGenres.includes(selectedGenre.value)
    const matchesStatus = movie.status === activeStatus.value
    return matchesSearch && matchesGenre && matchesStatus
  })

  if (sortBy.value === 'rating') {
    result.sort((a, b) => b.rating - a.rating)
  } else if (sortBy.value === 'newest') {
    result.sort((a, b) => new Date(b.releaseDate).getTime() - new Date(a.releaseDate).getTime())
  } else if (sortBy.value === 'duration') {
    result.sort((a, b) => b.duration - a.duration)
  }

  return result
})

const clearFilters = () => {
  searchQuery.value = ''
  selectedGenre.value = 'All'
  sortBy.value = 'rating'
}
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
        <div class="flex items-center justify-center gap-4 mb-12">
          <button
            @click="activeStatus = 'now-playing'"
            :class="[
              'px-8 py-3 rounded-2xl text-sm font-black uppercase tracking-widest transition-all',
              activeStatus === 'now-playing'
                ? 'bg-red-600 text-white shadow-xl shadow-red-600/40'
                : 'bg-white/5 text-gray-400 hover:bg-white/10',
            ]"
          >
            {{ languageStore.language === 'en' ? 'Now Playing' : 'Đang chiếu' }}
          </button>
          <button
            @click="activeStatus = 'coming-soon'"
            :class="[
              'px-8 py-3 rounded-2xl text-sm font-black uppercase tracking-widest transition-all',
              activeStatus === 'coming-soon'
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
        class="bg-zinc-900/60 backdrop-blur-3xl border border-white/5 rounded-2xl p-4 mb-12 flex flex-col lg:flex-row items-center justify-between gap-6 shadow-2xl"
      >
        <div
          class="flex items-center gap-2 overflow-x-auto w-full lg:w-auto pb-2 lg:pb-0 scrollbar-hide"
        >
          <button
            v-for="genre in genres"
            :key="genre"
            @click="selectedGenre = genre"
            :class="[
              'px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] transition-all whitespace-nowrap border',
              selectedGenre === genre
                ? 'bg-red-600 border-red-600 text-white shadow-lg shadow-red-600/20'
                : 'bg-white/5 border-white/5 text-gray-400 hover:border-white/10 hover:text-white',
            ]"
          >
            {{ genre }}
          </button>
        </div>

        <div
          class="flex items-center gap-6 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 border-white/5 pt-4 lg:pt-0"
        >
          <div class="flex items-center gap-3">
            <SlidersHorizontal class="w-4 h-4 text-red-600" />
            <select
              v-model="sortBy"
              class="bg-transparent border-none focus:ring-0 text-gray-400 text-[10px] font-black uppercase tracking-widest cursor-pointer hover:text-white transition-colors"
            >
              <option value="rating">Top Rated</option>
              <option value="newest">Newest</option>
              <option value="duration">Longest</option>
            </select>
          </div>

          <div class="h-6 w-px bg-white/10 hidden sm:block" />

          <p class="text-gray-600 text-[10px] font-black uppercase tracking-widest">
            {{ filteredMovies.length }}
            {{ languageStore.language === 'en' ? 'Results' : 'Kết quả' }}
          </p>
        </div>
      </div>

      <!-- Movies Grid -->
      <div
        v-if="filteredMovies.length > 0"
        class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6 lg:gap-8"
      >
        <MovieCard v-for="movie in filteredMovies" :key="movie.id" :movie="movie" />
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="text-center py-32 bg-zinc-900/30 rounded-[4rem] border border-dashed border-white/10"
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
</style>
