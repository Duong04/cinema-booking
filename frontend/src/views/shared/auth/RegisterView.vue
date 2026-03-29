<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useLanguageStore } from '@/stores/shared/language';
import { Film, Mail, Lock, Eye, EyeOff, ArrowRight, User, Phone } from 'lucide-vue-next';

const router = useRouter();
const languageStore = useLanguageStore();

const name = ref('');
const email = ref('');
const phone = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const isLoading = ref(false);

const handleRegister = async () => {
  isLoading.value = true;
  setTimeout(() => {
    isLoading.value = false;
    router.push('/login');
  }, 1500);
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4 py-24 bg-zinc-950 relative overflow-hidden">
    <!-- Background Glow -->
    <div class="absolute top-1/4 -right-1/4 w-96 h-96 bg-red-600/20 rounded-full blur-[120px] pointer-events-none" />
    <div class="absolute bottom-1/4 -left-1/4 w-96 h-96 bg-orange-600/10 rounded-full blur-[120px] pointer-events-none" />

    <div class="w-full max-w-lg relative z-10">
      <div class="text-center mb-10">
        <router-link to="/" class="inline-flex items-center gap-2 mb-6 group">
          <div class="w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 group-hover:rotate-3 shadow-lg shadow-red-600/20">
            <Film class="text-white w-6 h-6" />
          </div>
          <span class="text-3xl font-black text-white tracking-tighter">CINEMAX</span>
        </router-link>
        <h1 class="text-3xl font-black text-white uppercase italic tracking-tight mb-2">
          {{ languageStore.language === 'en' ? 'Create Account' : 'Tạo tài khoản' }}
        </h1>
        <p class="text-gray-500 font-medium">
          {{ languageStore.language === 'en' ? 'Join our movie community' : 'Tham gia cộng đồng yêu phim của chúng tôi' }}
        </p>
      </div>

      <div class="bg-zinc-900/50 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 md:p-10 shadow-2xl">
        <form @submit.prevent="handleRegister" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2 col-span-2 md:col-span-1">
            <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">Full Name</label>
            <div class="relative group">
              <User class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-red-500 transition-colors" />
              <input
                v-model="name"
                type="text"
                required
                placeholder="John Doe"
                class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 pl-12 pr-4 text-white placeholder:text-gray-700 focus:outline-none focus:border-red-600/50 focus:ring-4 focus:ring-red-600/10 transition-all"
              />
            </div>
          </div>

          <div class="space-y-2 col-span-2 md:col-span-1">
            <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">Phone Number</label>
            <div class="relative group">
              <Phone class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-red-500 transition-colors" />
              <input
                v-model="phone"
                type="tel"
                required
                placeholder="090 123 4567"
                class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 pl-12 pr-4 text-white placeholder:text-gray-700 focus:outline-none focus:border-red-600/50 focus:ring-4 focus:ring-red-600/10 transition-all"
              />
            </div>
          </div>

          <div class="space-y-2 col-span-2">
            <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">Email Address</label>
            <div class="relative group">
              <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-red-500 transition-colors" />
              <input
                v-model="email"
                type="email"
                required
                placeholder="name@example.com"
                class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 pl-12 pr-4 text-white placeholder:text-gray-700 focus:outline-none focus:border-red-600/50 focus:ring-4 focus:ring-red-600/10 transition-all"
              />
            </div>
          </div>

          <div class="space-y-2 col-span-2 md:col-span-1">
            <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">Password</label>
            <div class="relative group">
              <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-red-500 transition-colors" />
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="••••••••"
                class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 pl-12 pr-12 text-white placeholder:text-gray-700 focus:outline-none focus:border-red-600/50 focus:ring-4 focus:ring-red-600/10 transition-all"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors"
              >
                <Eye v-if="!showPassword" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <div class="space-y-2 col-span-2 md:col-span-1">
            <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">Confirm Password</label>
            <div class="relative group">
              <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-red-500 transition-colors" />
              <input
                v-model="confirmPassword"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="••••••••"
                class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 pl-12 pr-4 text-white placeholder:text-gray-700 focus:outline-none focus:border-red-600/50 focus:ring-4 focus:ring-red-600/10 transition-all"
              />
            </div>
          </div>

          <div class="col-span-2 pt-4">
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full py-4 bg-red-600 text-white rounded-2xl font-black flex items-center justify-center gap-2 hover:bg-red-700 transition-all transform active:scale-95 shadow-xl shadow-red-600/20 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!isLoading">{{ languageStore.language === 'en' ? 'Create Account' : 'Tạo tài khoản' }}</span>
              <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
              <ArrowRight v-if="!isLoading" class="w-5 h-5" />
            </button>
          </div>
        </form>

        <div class="mt-8 text-center">
          <p class="text-xs text-gray-600 font-medium max-w-xs mx-auto">
            By signing up, you agree to our 
            <a href="#" class="text-red-500/80 hover:text-red-500 underline">Terms of Service</a> 
            and 
            <a href="#" class="text-red-500/80 hover:text-red-500 underline">Privacy Policy</a>
          </p>
        </div>
      </div>

      <p class="text-center mt-8 text-gray-500 font-medium">
        {{ languageStore.language === 'en' ? "Already have an account?" : "Đã có tài khoản?" }}
        <router-link to="/login" class="text-red-500 font-black hover:text-red-400 transition-colors ml-1">
          {{ languageStore.language === 'en' ? 'Sign In' : 'Đăng nhập' }}
        </router-link>
      </p>
    </div>
  </div>
</template>
