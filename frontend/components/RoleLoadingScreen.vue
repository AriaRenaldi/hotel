<template>
  <Transition name="role-loader-fade">
    <div v-if="visible" class="fixed inset-0 z-[9999] flex items-center justify-center px-6" :style="overlayStyle">
      <div class="relative w-full max-w-sm overflow-hidden rounded-3xl border border-white/25 bg-white/10 p-7 text-white shadow-2xl shadow-black/25 backdrop-blur-xl">
        <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/15 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>

        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/80">
          {{ theme.label }} Experience
        </p>
        <h2 class="mt-3 text-2xl font-bold">{{ title }}</h2>
        <p class="mt-2 text-sm text-white/85">{{ subtitle }}</p>

        <div class="mt-6 flex items-center gap-3">
          <div class="role-loader-spinner h-10 w-10 rounded-full border-4 border-white/25 border-t-white"></div>
          <div class="flex-1">
            <div class="mb-2 flex items-center justify-between text-xs font-semibold text-white/85">
              <span>Loading</span>
              <span>{{ safeProgress }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-white/20">
              <div class="h-full rounded-full bg-gradient-to-r from-white to-white/70 transition-all duration-300" :style="{ width: `${safeProgress}%` }"></div>
            </div>
          </div>
        </div>

        <div class="mt-5 grid grid-cols-3 gap-2">
          <div class="h-2 rounded-full bg-white/25"></div>
          <div class="h-2 rounded-full bg-white/20"></div>
          <div class="h-2 rounded-full bg-white/15"></div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
const props = defineProps({
  visible: { type: Boolean, default: false },
  progress: { type: Number, default: 0 },
  theme: {
    type: Object,
    default: () => ({
      key: 'guest',
      label: 'Guest',
      title: 'Preparing Your Stay',
      subtitle: 'Menyiapkan pengalaman terbaik untuk Anda.',
      gradient: 'linear-gradient(135deg, #061a33 0%, #0f2f57 55%, #1d4a7a 100%)',
    }),
  },
  routeLabel: { type: String, default: '' },
})

const safeProgress = computed(() => Math.max(0, Math.min(100, Math.round(props.progress || 0))))
const title = computed(() => props.theme?.title || 'Loading Experience')
const subtitle = computed(() => props.routeLabel ? `Membuka halaman ${props.routeLabel}...` : 'Sedang menyiapkan halaman...')
const overlayStyle = computed(() => ({
  background: props.theme?.gradient || 'linear-gradient(135deg, #061a33 0%, #0f2f57 55%, #1d4a7a 100%)',
}))
</script>

<style scoped>
@keyframes role-loader-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.role-loader-spinner {
  animation: role-loader-spin 0.9s linear infinite;
}

.role-loader-fade-enter-active,
.role-loader-fade-leave-active {
  transition: opacity 0.3s ease;
}

.role-loader-fade-enter-from,
.role-loader-fade-leave-to {
  opacity: 0;
}
</style>
