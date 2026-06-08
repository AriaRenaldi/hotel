<template>
  <div class="role-shell min-h-screen" :class="`role-${activeTheme.key}`" :style="shellVars" @mousemove="handlePointerMove" @mouseleave="handlePointerLeave">
    <NuxtRouteAnnouncer />

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
      <div class="role-ambient-spotlight" :style="spotlightStyle"></div>
      <div class="role-ambient-orb role-ambient-orb--one" :style="orbOneStyle"></div>
      <div class="role-ambient-orb role-ambient-orb--two" :style="orbTwoStyle"></div>
    </div>

    <div v-if="loadingVisible" class="pointer-events-none fixed inset-x-0 top-0 z-[120] h-1 bg-transparent">
      <div class="h-full transition-all duration-200" :style="topProgressStyle"></div>
    </div>

    <Transition name="role-page" mode="out-in">
      <div :key="route.fullPath">
        <NuxtPage />
      </div>
    </Transition>

    <RoleLoadingScreen :visible="loadingVisible" :progress="loaderProgress" :theme="activeTheme" :route-label="routeLabel" />
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import RoleLoadingScreen from '~/components/RoleLoadingScreen.vue'

const route = useRoute()
const authStore = useAuthStore()

const THEMES = {
  guest: {
    key: 'guest',
    label: 'Guest',
    title: 'Preparing Your Stay',
    gradient: 'linear-gradient(135deg, #05142b 0%, #0c2d54 55%, #1d4a7a 100%)',
    accent: 'linear-gradient(90deg, #1d4a7a 0%, #8ab2db 100%)',
    spotlight: 'rgba(120, 171, 224, 0.28)',
    orbOne: 'rgba(127, 171, 219, 0.22)',
    orbTwo: 'rgba(255, 255, 255, 0.20)',
  },
  receptionist: {
    key: 'receptionist',
    label: 'Receptionist',
    title: 'Preparing Front Desk',
    gradient: 'linear-gradient(135deg, #4b5563 0%, #6b7280 55%, #9ca3af 100%)',
    accent: 'linear-gradient(90deg, #9ca3af 0%, #e5e7eb 100%)',
    spotlight: 'rgba(232, 235, 239, 0.28)',
    orbOne: 'rgba(228, 232, 237, 0.24)',
    orbTwo: 'rgba(255, 255, 255, 0.22)',
  },
  admin: {
    key: 'admin',
    label: 'Admin',
    title: 'Preparing Executive Panel',
    gradient: 'linear-gradient(135deg, #6b4f1d 0%, #9c7a2b 52%, #d4af37 100%)',
    accent: 'linear-gradient(90deg, #b88b29 0%, #f2df9f 100%)',
    spotlight: 'rgba(247, 217, 129, 0.28)',
    orbOne: 'rgba(255, 227, 151, 0.26)',
    orbTwo: 'rgba(255, 255, 255, 0.20)',
  },
}

const loadingVisible = ref(true)
const loaderProgress = ref(6)
const cursorX = ref(50)
const cursorY = ref(45)
const hasCursor = ref(false)
let progressInterval = null
let loadingTimeout = null

const roleFromPath = computed(() => {
  const path = route.path || ''
  if (path.startsWith('/admin')) return 'admin'
  if (path.startsWith('/receptionist')) return 'receptionist'
  if (path.startsWith('/guest')) return 'guest'
  return ''
})

const activeRole = computed(() => {
  if (roleFromPath.value) return roleFromPath.value
  const role = authStore.user?.role
  if (role === 'admin' || role === 'receptionist' || role === 'guest') return role
  return 'guest'
})

const activeTheme = computed(() => THEMES[activeRole.value] || THEMES.guest)
const routeLabel = computed(() => {
  const path = String(route.path || '').toLowerCase()

  if (path.includes('/booking/print')) return 'Cetak Booking'
  if (path.includes('/booking')) return 'Booking'
  if (path.includes('/rooms')) return 'Kamar'
  if (path.includes('/payment')) return 'Pembayaran'
  if (path.includes('/reports')) return 'Laporan'
  if (path.includes('/dashboard')) return 'Dashboard'
  if (path.includes('/profile')) return 'Profil'
  if (path.includes('/login')) return 'Login'
  if (path.includes('/register')) return 'Pendaftaran'

  return 'Halaman'
})

const shellVars = computed(() => ({
  '--role-spotlight': activeTheme.value.spotlight,
  '--role-orb-one': activeTheme.value.orbOne,
  '--role-orb-two': activeTheme.value.orbTwo,
}))

const spotlightStyle = computed(() => ({
  background: `radial-gradient(circle at ${cursorX.value}% ${cursorY.value}%, var(--role-spotlight) 0%, rgba(255,255,255,0) 52%)`,
  opacity: hasCursor.value ? 1 : 0.75,
}))

const orbOneStyle = computed(() => ({
  transform: `translate3d(${(cursorX.value - 50) * 0.25}px, ${(cursorY.value - 50) * 0.2}px, 0)`,
}))

const orbTwoStyle = computed(() => ({
  transform: `translate3d(${(cursorX.value - 50) * -0.2}px, ${(cursorY.value - 50) * -0.2}px, 0)`,
}))

const topProgressStyle = computed(() => ({
  width: `${Math.max(0, Math.min(100, Math.round(loaderProgress.value)))}%`,
  background: activeTheme.value.accent,
}))

const clearTimers = () => {
  if (progressInterval) {
    clearInterval(progressInterval)
    progressInterval = null
  }
  if (loadingTimeout) {
    clearTimeout(loadingTimeout)
    loadingTimeout = null
  }
}

const beginLoading = () => {
  clearTimers()
  loadingVisible.value = true
  loaderProgress.value = 8

  progressInterval = setInterval(() => {
    if (loaderProgress.value >= 86) return
    loaderProgress.value += Math.random() * 10
  }, 120)
}

const endLoading = () => {
  if (progressInterval) {
    clearInterval(progressInterval)
    progressInterval = null
  }
  loaderProgress.value = 100
  loadingTimeout = setTimeout(() => {
    loadingVisible.value = false
  }, 280)
}

const animateRouteLoading = (duration = 560) => {
  beginLoading()
  loadingTimeout = setTimeout(() => {
    endLoading()
  }, duration)
}

const handlePointerMove = (event) => {
  if (typeof window === 'undefined') return
  const width = window.innerWidth || 1
  const height = window.innerHeight || 1
  cursorX.value = (event.clientX / width) * 100
  cursorY.value = (event.clientY / height) * 100
  hasCursor.value = true
}

const handlePointerLeave = () => {
  hasCursor.value = false
}

watch(
  () => route.fullPath,
  (_newPath, oldPath) => {
    if (!oldPath) return
    animateRouteLoading(520)
  }
)

onMounted(() => {
  authStore.initAuth()
  animateRouteLoading(900)
})

onBeforeUnmount(() => {
  clearTimers()
})
</script>

<style>
.role-shell {
  position: relative;
  isolation: isolate;
}

.role-ambient-spotlight {
  position: absolute;
  inset: -20%;
  transition: opacity 0.35s ease;
}

.role-ambient-orb {
  position: absolute;
  border-radius: 999px;
  filter: blur(65px);
  transition: transform 0.25s ease-out;
}

.role-ambient-orb--one {
  left: -7rem;
  top: 10%;
  height: 18rem;
  width: 18rem;
  background: var(--role-orb-one);
}

.role-ambient-orb--two {
  right: -7rem;
  bottom: 4%;
  height: 20rem;
  width: 20rem;
  background: var(--role-orb-two);
}

.role-page-enter-active,
.role-page-leave-active {
  transition: opacity 0.35s ease, transform 0.35s ease;
}

.role-page-enter-from,
.role-page-leave-to {
  opacity: 0;
  transform: translateY(8px) scale(0.995);
}
</style>
