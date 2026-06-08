<template>
  <div class="min-h-screen bg-gradient-to-br from-[#f3f7fb] via-[#eef4fb] to-[#dbe6f3] py-16 text-center text-slate-600">
    Mengalihkan ke halaman pesan...
  </div>
</template>

<script setup>
definePageMeta({ middleware: 'guest' })

const route = useRoute()

onMounted(() => {
  navigateTo({
    path: '/guest/pesan',
    query: route.query,
  }, { replace: true })
})
</script>
