<template>
  <div class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-2xl shadow-slate-200/60 backdrop-blur md:p-8">
    <div class="pointer-events-none absolute inset-0 skeleton-loader-overlay"></div>

    <div class="relative">
      <div class="flex items-center gap-4">
        <div class="h-10 w-10 rounded-2xl bg-slate-200/80"></div>
        <div class="flex-1 space-y-2">
          <div class="h-3 w-36 rounded-full bg-slate-200/80"></div>
          <div class="h-5 w-60 max-w-full rounded-full bg-slate-200/70"></div>
        </div>
      </div>

      <p class="mt-4 text-sm text-slate-500">{{ message }}</p>

      <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <div
          v-for="cardIndex in safeCardCount"
          :key="`skeleton-card-${cardIndex}`"
          class="space-y-2 rounded-2xl border border-slate-100 bg-slate-50/80 p-4"
        >
          <div class="h-3 w-20 rounded-full bg-slate-200/80"></div>
          <div class="h-4 w-full rounded-full bg-slate-200/70"></div>
          <div class="h-4 w-4/5 rounded-full bg-slate-200/70"></div>
        </div>
      </div>

      <div class="mt-6 space-y-3">
        <div
          v-for="rowIndex in safeRowCount"
          :key="`skeleton-row-${rowIndex}`"
          class="h-4 rounded-full bg-slate-200/75"
          :style="{ width: `${90 - (rowIndex % 4) * 8}%` }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  message: {
    type: String,
    default: 'Sedang menyiapkan data halaman...',
  },
  cardCount: {
    type: Number,
    default: 3,
  },
  rowCount: {
    type: Number,
    default: 5,
  },
})

const safeCardCount = computed(() => Math.max(1, Math.min(6, Number(props.cardCount || 3))))
const safeRowCount = computed(() => Math.max(2, Math.min(10, Number(props.rowCount || 5))))
</script>

<style scoped>
@keyframes skeleton-loader-shimmer {
  0% {
    transform: translateX(-120%);
  }
  100% {
    transform: translateX(120%);
  }
}

.skeleton-loader-overlay::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(110deg, rgba(255, 255, 255, 0) 10%, rgba(255, 255, 255, 0.45) 45%, rgba(255, 255, 255, 0) 80%);
  animation: skeleton-loader-shimmer 1.8s linear infinite;
}
</style>

