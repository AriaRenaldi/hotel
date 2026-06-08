<template>
  <div
    v-if="total > 0"
    class="flex flex-wrap items-center justify-between gap-3 border-t px-6 py-4"
    :class="themeClass.wrapper"
  >
    <p v-if="showSummary" class="text-sm" :class="themeClass.summary">
      Menampilkan {{ from }} - {{ to }} dari {{ total }} data
    </p>
    <div v-else />

    <div class="flex items-center gap-1.5 sm:gap-2">
      <button
        :disabled="currentPage <= 1"
        class="rounded-lg border px-3 py-1.5 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
        :class="themeClass.button"
        @click="goTo(currentPage - 1)"
      >
        Sebelumnya
      </button>

      <button
        v-for="(page, index) in visiblePages"
        :key="`${page}-${index}`"
        :disabled="page === '...'"
        class="min-w-9 rounded-lg border px-2.5 py-1.5 text-sm font-semibold transition disabled:cursor-default disabled:opacity-70"
        :class="page === currentPage ? themeClass.active : themeClass.button"
        @click="typeof page === 'number' && goTo(page)"
      >
        {{ page }}
      </button>

      <button
        :disabled="currentPage >= totalPages"
        class="rounded-lg border px-3 py-1.5 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
        :class="themeClass.button"
        @click="goTo(currentPage + 1)"
      >
        Berikutnya
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Number, required: true },
  totalPages: { type: Number, required: true },
  from: { type: Number, default: 0 },
  to: { type: Number, default: 0 },
  total: { type: Number, default: 0 },
  showSummary: { type: Boolean, default: true },
  theme: { type: String, default: 'slate' },
})

const emit = defineEmits(['update:modelValue'])

const currentPage = computed(() => Math.min(Math.max(1, Number(props.modelValue || 1)), Math.max(1, Number(props.totalPages || 1))))

const visiblePages = computed(() => {
  const totalPages = Math.max(1, Number(props.totalPages || 1))
  const page = currentPage.value
  if (totalPages <= 7) return Array.from({ length: totalPages }, (_, i) => i + 1)

  const pages = [1]
  if (page > 3) pages.push('...')
  const start = Math.max(2, page - 1)
  const end = Math.min(totalPages - 1, page + 1)
  for (let p = start; p <= end; p += 1) pages.push(p)
  if (page < totalPages - 2) pages.push('...')
  pages.push(totalPages)
  return pages
})

const themeClass = computed(() => {
  if (props.theme === 'amber') {
    return {
      wrapper: 'border-amber-100 bg-amber-50/30',
      summary: 'text-slate-600',
      button: 'border-amber-200 bg-white text-slate-700 hover:bg-amber-50',
      active: 'border-amber-300 bg-slate-900 text-white',
    }
  }
  return {
    wrapper: 'border-slate-100 bg-white/80',
    summary: 'text-slate-600',
    button: 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
    active: 'border-slate-400 bg-slate-800 text-white',
  }
})

const goTo = (page) => {
  const target = Math.min(Math.max(1, Number(page || 1)), Math.max(1, Number(props.totalPages || 1)))
  if (target !== currentPage.value) emit('update:modelValue', target)
}
</script>
