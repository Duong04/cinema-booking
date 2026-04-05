import { STORAGE_KEYS } from '@/shared/constants/storage'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(localStorage.getItem(STORAGE_KEYS.THEME) === 'dark')

  function toggleTheme() {
    isDark.value = !isDark.value
    localStorage.setItem(STORAGE_KEYS.THEME, isDark.value ? 'dark' : 'light')
  }

  return { isDark, toggleTheme }
})