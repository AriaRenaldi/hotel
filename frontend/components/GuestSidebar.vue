<template>
  <!-- Top Navigation Bar -->
  <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-blue-100">
    <div class="max-w-7xl mx-auto px-6 py-3">
      <div class="flex justify-between items-center">
        <!-- Logo -->
        <NuxtLink to="/guest/dashboard" class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-br from-sky-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <span class="text-xl font-bold bg-gradient-to-r from-sky-600 to-blue-700 bg-clip-text text-transparent">HotelKu</span>
        </NuxtLink>
        
        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-1">
          <NuxtLink v-for="item in navItems" :key="item.to" :to="item.to" 
            :class="active === item.active ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-gray-600 hover:bg-sky-50 hover:text-sky-600'" 
            class="px-4 py-2 rounded-xl text-sm font-medium transition">
            {{ item.label }}
          </NuxtLink>
          <div class="w-px h-6 bg-gray-200 mx-2"></div>
          <button @click="handleLogout" class="px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-xl hover:from-red-500 hover:to-red-600 transition text-sm font-medium shadow-sm">
            Logout
          </button>
        </div>

        <!-- Mobile Menu Button -->
        <button @click="menuOpen = !menuOpen" class="md:hidden p-2 rounded-xl hover:bg-sky-50 transition">
          <svg v-if="!menuOpen" class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
          <svg v-else class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Mobile Navigation -->
      <div v-if="menuOpen" class="md:hidden mt-4 pb-4 space-y-1">
        <NuxtLink v-for="item in navItems" :key="item.to" :to="item.to" 
          :class="active === item.active ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-gray-600'" 
          class="block px-4 py-3 rounded-xl text-sm font-medium transition"
          @click="menuOpen = false">
          {{ item.label }}
        </NuxtLink>
        <button @click="handleLogout" class="w-full text-left px-4 py-3 text-red-500 rounded-xl text-sm font-medium hover:bg-red-50 transition mt-2">
          Logout
        </button>
      </div>
    </div>
  </nav>

  <!-- Sidebar (Desktop - Collapsible) -->
  <aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" class="hidden lg:flex flex-col fixed left-0 top-16 h-[calc(100vh-4rem)] bg-gradient-to-b from-white via-sky-50 to-blue-50 border-r border-blue-100 shadow-lg z-40 transition-all duration-300">
    <!-- Toggle Button -->
    <div class="p-4 flex justify-end border-b border-blue-100">
      <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 rounded-xl hover:bg-sky-100 transition">
        <svg v-if="!sidebarCollapsed" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
        </svg>
        <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
        </svg>
      </button>
    </div>

    <!-- Sidebar Menu -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-2">
      <template v-for="section in sidebarSections" :key="section.title">
        <p v-if="!sidebarCollapsed" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2 mt-4 first:mt-0">{{ section.title }}</p>
        <NuxtLink 
          v-for="item in section.items" :key="item.to" :to="item.to"
          :class="[
            active === item.active ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-blue-700 shadow-sm' : 'text-gray-600 hover:bg-sky-50 hover:text-sky-700',
            sidebarCollapsed ? 'justify-center' : ''
          ]"
          class="flex items-center space-x-3 px-3 py-3 rounded-xl transition-all duration-200 group relative"
        >
          <div v-html="item.icon" class="w-5 h-5 flex-shrink-0"></div>
          <span v-if="!sidebarCollapsed" class="font-medium text-sm">{{ item.label }}</span>
          <span v-if="sidebarCollapsed" class="absolute left-full ml-2 px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 whitespace-nowrap z-50 pointer-events-none transition shadow-lg">{{ item.label }}</span>
        </NuxtLink>
      </template>
    </nav>

    <!-- User Info -->
    <div class="p-4 border-t border-blue-100">
      <div :class="sidebarCollapsed ? 'justify-center' : ''" class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-sky-400 to-blue-500 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
          <span class="text-white font-bold">{{ userName?.charAt(0) || 'G' }}</span>
        </div>
        <div v-if="!sidebarCollapsed">
          <p class="text-sm font-semibold text-gray-800 truncate max-w-[140px]">{{ userName }}</p>
          <p class="text-xs text-gray-400">Tamu</p>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <div :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'" class="transition-all duration-300">
    <slot />
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

defineProps({
  active: { type: String, default: '' }
})

const authStore = useAuthStore()
const menuOpen = ref(false)
const sidebarCollapsed = ref(false)

const userName = computed(() => authStore.user?.full_name)

const navItems = [
  { to: '/guest/dashboard', active: 'dashboard', label: 'Beranda' },
  { to: '/guest/rooms', active: 'rooms', label: 'Kamar' },
  { to: '/guest/booking', active: 'booking', label: 'Pesananku' },
  { to: '/guest/check-booking', active: 'check', label: 'Cek Pesanan' },
  { to: '/profile', active: 'profile', label: 'Profil' }
]

const sidebarSections = [
  {
    title: 'Menu Utama',
    items: [
      { to: '/guest/dashboard', active: 'dashboard', label: 'Beranda', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>' },
      { to: '/guest/rooms', active: 'rooms', label: 'Lihat Kamar', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>' },
    ]
  },
  {
    title: 'Pemesanan',
    items: [
      { to: '/guest/booking', active: 'booking', label: 'Pesananku', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' },
      { to: '/guest/check-booking', active: 'check', label: 'Cek Pesanan', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>' },
    ]
  },
  {
    title: 'Akun',
    items: [
      { to: '/profile', active: 'profile', label: 'Profil Saya', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>' },
    ]
  }
]

const handleLogout = async () => {
  await authStore.logout()
  navigateTo('/login')
}
</script>
