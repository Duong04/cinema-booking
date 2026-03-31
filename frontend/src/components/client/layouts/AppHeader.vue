<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { ref, onMounted, onUnmounted, h } from 'vue'
import { useLanguageStore } from '@/stores/shared/language'
import {
  Film,
  Search,
  Heart,
  Bell,
  User,
  Globe,
  Calendar,
  MapPin,
  Menu,
  X,
  ChevronRight,
  LogOut, LayoutDashboard,
} from 'lucide-vue-next'
import { NAvatar } from 'naive-ui'
import { useAuthStore } from '@/stores/shared/auth.store'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const { isLoggedIn, isAdmin, fullName, avatar, user } = storeToRefs(authStore)
const languageStore = useLanguageStore()
const isMobileMenuOpen = ref(false)
const isScrolled = ref(false)
const navItems = [
  { name: 'nav.home', namepath: 'home', icon: Film },
  { name: 'nav.movies', namepath: 'movies', icon: Film },
  { name: 'nav.cinemas', namepath: 'cinemas', icon: MapPin },
  { name: 'nav.schedule', namepath: 'schedule', icon: Calendar },
]

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

const userMenuOptions = [
  {
    label: () => languageStore.language === 'en' ? 'Profile' : 'Tài khoản',
    key: 'profile',
    icon: () => h(User, { class: 'w-4 h-4' }),
  },
  {
    label: () => languageStore.language === 'en' ? 'Admin Dashboard' : 'Quản trị',
    key: 'admin',
    icon: () => h(LayoutDashboard, { class: 'w-4 h-4' }),
    show: isAdmin.value,
  },
  { type: 'divider', key: 'divider' },
  {
    label: () => languageStore.language === 'en' ? 'Sign Out' : 'Đăng xuất',
    key: 'logout',
    icon: () => h(LogOut, { class: 'w-4 h-4' }),
  },
]

async function handleUserMenu(key: string) {
  if (key === 'profile') router.push('/profile')
  if (key === 'admin') router.push('/admin')
  if (key === 'logout') await authStore.logout()
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <nav
    :class="[
      'fixed top-0 left-0 right-0 z-[100] transition-all duration-500 border-b',
      isScrolled
        ? 'bg-zinc-950/80 backdrop-blur-2xl border-white/10 py-3'
        : 'bg-transparent border-transparent py-5',
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between">
        <!-- Logo & Desktop Nav -->
        <div class="flex items-center gap-12">
          <router-link
            to="/"
            class="flex items-center gap-3 group"
            @click="isMobileMenuOpen = false"
          >
            <div
              class="w-10 h-10 bg-red-600 rounded-2xl flex items-center justify-center shadow-lg shadow-red-600/20 group-hover:scale-110 transition-transform duration-500"
            >
              <Film class="text-white w-6 h-6" />
            </div>
            <span class="text-2xl font-black text-white tracking-tighter uppercase italic"
              >CINEMAX</span
            >
          </router-link>

          <!-- Desktop Menu -->
          <div class="hidden lg:flex items-center gap-8">
            <router-link
              v-for="item in navItems"
              :key="item.namepath"
              :to="{ name: item.namepath }"
              class="relative text-xs font-black uppercase tracking-widest transition-all duration-300 group"
              :class="
                route.name === item.namepath ? 'text-white' : 'text-gray-400 hover:text-white'
              "
            >
              {{ languageStore.t(item.name) }}
              <span
                :class="[
                  'absolute -bottom-2 left-0 h-0.5 bg-red-600 transition-all duration-500',
                  route.name === item.namepath ? 'w-full' : 'w-0 group-hover:w-full',
                ]"
              />
            </router-link>
          </div>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-3">
          <!-- Search (Desktop) -->
          <div
            :class="[
              isScrolled ? 'border-white/10 bg-white/5' : 'border-white bg-white/10',
              'hidden md:flex items-center border rounded-2xl px-4 py-2 focus-within:border-red-600/50 transition-all group',
            ]"
          >
            <Search
              class="w-4 h-4 text-gray-400 group-focus-within:text-red-500 transition-colors"
            />
            <input
              type="text"
              :placeholder="languageStore.language === 'en' ? 'Search movies...' : 'Tìm phim...'"
              :class="[
                isScrolled
                  ? 'placeholder:text-gray-500 text-white'
                  : 'placeholder:text-white/70 text-white',
                'bg-transparent border-none focus:ring-0 text-xs w-32 lg:w-48 ml-2 focus:outline-0',
              ]"
            />
          </div>

          <!-- Icons -->
          <div class="hidden sm:flex items-center gap-1">
            <router-link
              to="/wishlist"
              :class="[
                isScrolled ? 'text-gray-400 hover:bg-white/5' : 'text-white hover:bg-white/10',
                'p-2.5 rounded-xl hover:text-red-500 transition-all',
              ]"
            >
              <Heart class="w-5 h-5" />
            </router-link>

            <router-link
              to="/notifications"
              :class="[
                isScrolled ? 'text-gray-400 hover:bg-white/5' : 'text-white hover:bg-white/10',
                'p-2.5 rounded-xl hover:text-red-500 transition-all relative',
              ]"
            >
              <Bell class="w-5 h-5" />
              <span
                class="absolute top-2 right-2 w-2 h-2 bg-red-600 rounded-full border-2 border-zinc-950"
              />
            </router-link>
          </div>

          <!-- Language -->
          <button
            @click="languageStore.setLanguage(languageStore.language === 'en' ? 'vi' : 'en')"
            :class="[
              isScrolled
                ? 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10'
                : 'bg-white/10 border-white text-white hover:bg-white/20',
              'flex items-center gap-2 px-3 py-2 rounded-xl border text-[10px] font-black transition-all',
            ]"
          >
            <Globe class="w-3.5 h-3.5" />
            {{ languageStore.language === 'en' ? 'EN' : 'VI' }}
          </button>

          <router-link
            v-if="!isLoggedIn"
            :to="{ name: 'login' }"
            class="hidden sm:flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-red-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all transform active:scale-95 shadow-xl shadow-red-600/20"
          >
            <User class="w-4 h-4" />
            {{ languageStore.language === 'en' ? 'Sign In' : 'Đăng nhập' }}
          </router-link>

          <n-dropdown
            v-else
            trigger="click"
            :options="userMenuOptions.filter(o => o.show !== false)"
            @select="handleUserMenu"
            placement="bottom-end"
          >
            <button class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
              <n-avatar
                :src="avatar ?? undefined"
                :fallback-src="undefined"
                round
                size="small"
                class="w-7 h-7"
              >
                <template v-if="!avatar">
                  <span class="text-xs font-black">
                    {{ fullName.charAt(0).toUpperCase() }}
                  </span>
                </template>
              </n-avatar>
              <span class="text-xs font-black text-white max-w-[80px] truncate">
                {{ fullName }}
              </span>
              <ChevronRight class="w-3 h-3 text-gray-400 rotate-90" />
            </button>
          </n-dropdown>
          <button
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="lg:hidden p-2.5 bg-white/5 border border-white/10 rounded-xl text-gray-300 hover:text-white transition-all"
          >
            <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
            <X v-else class="w-6 h-6" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <transition
      enter-active-class="transition duration-500 ease-out"
      enter-from-class="opacity-0 -translate-y-10"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-300 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-10"
    >
      <div
        v-if="isMobileMenuOpen"
        class="lg:hidden absolute top-full left-0 right-0 bg-zinc-950/95 backdrop-blur-3xl border-b border-white/10 overflow-hidden"
      >
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-4">
          <router-link
            v-for="item in navItems"
            :key="item.namepath"
            :to="{ name: item.namepath }"
            @click="isMobileMenuOpen = false"
            class="flex items-center justify-between px-6 py-4 rounded-2xl text-sm font-black uppercase tracking-widest transition-all"
            :class="
              route.name === item.namepath
                ? 'bg-red-600 text-white shadow-lg shadow-red-600/20'
                : 'text-gray-400 hover:bg-white/5 hover:text-white'
            "
          >
            <div class="flex items-center gap-4">
              <component :is="item.icon" class="w-5 h-5" />
              {{ languageStore.t(item.name) }}
            </div>
            <ChevronRight v-if="route.name !== item.namepath" class="w-4 h-4 opacity-50" />
          </router-link>

          <div class="pt-6 mt-6 border-t border-white/5 space-y-4">
            <router-link
              v-if="!isLoggedIn"
              :to="{ name: 'login' }"
              @click="isMobileMenuOpen = false"
              class="w-full py-4 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest flex items-center justify-center gap-3 shadow-xl shadow-red-600/20"
            >
              <User class="w-5 h-5" />
              {{ languageStore.language === 'en' ? 'Sign In' : 'Đăng nhập' }}
            </router-link>

            <!-- Mobile: đã login -->
            <div v-else class="space-y-3">
              <!-- User info -->
              <div class="flex items-center gap-3 px-4 py-3 bg-white/5 rounded-2xl">
                <NAvatar :src="avatar ?? undefined" round size="medium">
                  <template v-if="!avatar">
                    <span class="text-sm font-black">{{ fullName.charAt(0).toUpperCase() }}</span>
                  </template>
                </NAvatar>
                <div>
                  <p class="text-white font-black text-sm">{{ fullName }}</p>
                  <p class="text-gray-500 text-xs">{{ user?.email }}</p>
                </div>
              </div>

              <!-- Admin link -->
              <router-link
                v-if="isAdmin"
                to="/admin"
                @click="isMobileMenuOpen = false"
                class="w-full py-3 bg-white/5 border border-white/10 text-white rounded-2xl font-black uppercase tracking-widest flex items-center justify-center gap-3"
              >
                <LayoutDashboard class="w-5 h-5" />
                {{ languageStore.language === 'en' ? 'Admin Dashboard' : 'Quản trị' }}
              </router-link>

              <!-- Logout -->
              <button
                @click="authStore.logout(); isMobileMenuOpen = false"
                class="w-full py-3 bg-red-600/10 border border-red-600/20 text-red-500 rounded-2xl font-black uppercase tracking-widest flex items-center justify-center gap-3"
              >
                <LogOut class="w-5 h-5" />
                {{ languageStore.language === 'en' ? 'Sign Out' : 'Đăng xuất' }}
              </button>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <router-link
                to="/search"
                @click="isMobileMenuOpen = false"
                class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl text-gray-400 hover:text-white transition-all"
              >
                <Search class="w-6 h-6" />
                <span class="text-[8px] font-black uppercase">Search</span>
              </router-link>
              <router-link
                to="/wishlist"
                @click="isMobileMenuOpen = false"
                class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl text-gray-400 hover:text-white transition-all"
              >
                <Heart class="w-6 h-6" />
                <span class="text-[8px] font-black uppercase">Wishlist</span>
              </router-link>
              <router-link
                to="/notifications"
                @click="isMobileMenuOpen = false"
                class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl text-gray-400 hover:text-white transition-all"
              >
                <Bell class="w-6 h-6" />
                <span class="text-[8px] font-black uppercase">Alerts</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </nav>
</template>

<style scoped></style>
