<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 text-slate-600">
    Mengalihkan ke halaman cetak...
  </div>
</template>

<script setup>
const route = useRoute()
const id = route.params.id ? String(route.params.id) : ''

if (id) {
  await navigateTo(`/guest/booking/print/${encodeURIComponent(id)}`, { replace: true })
} else {
  await navigateTo('/guest/check-booking', { replace: true })
}
</script>
