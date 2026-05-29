<script setup lang="ts">
import { Check } from 'lucide-vue-next';
import { useLanguageStore } from '@/stores/language';

const languageStore = useLanguageStore();

defineProps<{
  currentStep: number;
}>();

const steps = [
  { id: 1, name: 'Showtime', nameVi: 'Lịch chiếu' },
  { id: 2, name: 'Seats', nameVi: 'Chọn ghế' },
  { id: 3, name: 'Combo', nameVi: 'Bắp nước' },
  { id: 4, name: 'Checkout', nameVi: 'Thanh toán' },
];
</script>

<template>
  <div class="w-full max-w-3xl mx-auto mb-16 px-4">
    <div class="relative flex justify-between">
      <!-- Background Line -->
      <div class="absolute top-5 left-0 w-full h-0.5 bg-white/5 -translate-y-1/2 z-0" />
      
      <!-- Progress Line -->
      <div 
        class="absolute top-5 left-0 h-0.5 bg-red-600 -translate-y-1/2 z-0 transition-all duration-700 ease-in-out"
        :style="{ width: `${((currentStep - 1) / (steps.length - 1)) * 100}%` }"
      />

      <!-- Steps -->
      <div 
        v-for="step in steps" 
        :key="step.id"
        class="relative z-10 flex flex-col items-center"
      >
        <div 
          :class="[
            'w-10 h-10 rounded-2xl flex items-center justify-center border-2 transition-all duration-500',
            currentStep > step.id 
              ? 'bg-red-600 border-red-600 text-white' 
              : currentStep === step.id
                ? 'bg-zinc-950 border-red-600 text-red-600 shadow-[0_0_25px_rgba(220,38,38,0.3)]'
                : 'bg-zinc-950 border-white/10 text-gray-600'
          ]"
        >
          <Check v-if="currentStep > step.id" class="w-5 h-5" />
          <span v-else class="text-sm font-black">{{ step.id }}</span>
        </div>
        <span 
          :class="[
            'mt-3 text-[9px] font-black uppercase tracking-[0.2em] transition-colors duration-500 text-center',
            currentStep >= step.id ? 'text-white' : 'text-gray-600'
          ]"
        >
          {{ languageStore.language === 'en' ? step.name : step.nameVi }}
        </span>
      </div>
    </div>
  </div>
</template>
