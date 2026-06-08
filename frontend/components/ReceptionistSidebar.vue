<template>
  <button @click="toggleMobile" class="lg:hidden fixed top-4 left-4 z-50 p-2.5 bg-white rounded-xl shadow-lg border border-blue-100">
    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
  </button>
  <div v-if="mobileOpen" @click="closeMobile" class="fixed inset-0 bg-black/30 z-40 lg:hidden"></div>

  <aside :class="[mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', collapsed ? 'lg:w-20' : 'lg:w-72']" class="fixed left-0 top-0 h-full bg-gradient-to-b from-[#07111f] via-[#0f2742] to-[#163b63] text-white shadow-xl border-r border-white/20 z-50 transition-all duration-300 ease-in-out flex flex-col">
    <div class="p-5 flex items-center justify-between border-b border-blue-100 min-h-[72px]">
      <div class="flex items-center space-x-3 overflow-hidden">
        <div class="w-11 h-11 bg-gradient-to-br from-sky-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200 flex-shrink-0">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div v-if="!collapsed"><span class="text-lg font-bold bg-gradient-to-r from-sky-600 to-blue-700 bg-clip-text text-transparent">HotelKu</span><p class="text-xs text-gray-400">Resepsionis</p></div>
      </div>
      <button @click="toggle" class="hidden lg:flex p-1.5 rounded-lg hover:bg-sky-100 transition">
        <svg v-if="!collapsed" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
        <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
      </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
      <p v-if="!collapsed" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Menu Utama</p>
      <NuxtLink to="/receptionist/dashboard" :class="[active === 'dashboard' ? 'bg-amber-400/20 text-white shadow-sm' : 'text-slate-200 hover:bg-white/10 hover:text-white', collapsed ? 'justify-center px-3' : '']" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group relative" @click="closeMobile">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        <span v-if="!collapsed" class="font-medium">Dashboard</span>
        <span v-if="collapsed" class="absolute left-full ml-2 px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 whitespace-nowrap z-50 pointer-events-none transition shadow-lg">Dashboard</span>
      </NuxtLink>

      <p v-if="!collapsed" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2 mt-6">Pemesanan</p>
      <NuxtLink v-for="item in bookingItems" :key="item.to" :to="item.to" :class="[active === item.active ? 'bg-amber-400/20 text-white shadow-sm' : 'text-slate-200 hover:bg-white/10 hover:text-white', collapsed ? 'justify-center px-3' : '']" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group relative" @click="closeMobile">
        <div v-html="item.icon" class="w-5 h-5 flex-shrink-0"></div>
        <span v-if="!collapsed" class="font-medium">{{ item.label }}</span>
        <span v-if="collapsed" class="absolute left-full ml-2 px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 whitespace-nowrap z-50 pointer-events-none transition shadow-lg">{{ item.label }}</span>
      </NuxtLink>

      <p v-if="!collapsed" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2 mt-6">Laporan & Akun</p>
      <NuxtLink v-for="item in otherItems" :key="item.to" :to="item.to" :class="[active === item.active ? 'bg-amber-400/20 text-white shadow-sm' : 'text-slate-200 hover:bg-white/10 hover:text-white', collapsed ? 'justify-center px-3' : '']" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group relative" @click="closeMobile">
        <div v-html="item.icon" class="w-5 h-5 flex-shrink-0"></div>
        <span v-if="!collapsed" class="font-medium">{{ item.label }}</span>
        <span v-if="collapsed" class="absolute left-full ml-2 px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 whitespace-nowrap z-50 pointer-events-none transition shadow-lg">{{ item.label }}</span>
      </NuxtLink>
    </nav>
    
    <div class="p-4 border-t border-blue-100">
      <button @click="handleLogout" :class="collapsed ? 'justify-center' : ''" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition w-full">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        <span v-if="!collapsed" class="font-medium">Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useSidebar } from '~/composables/useSidebar'

defineProps({ active: { type: String, default: '' } })

const authStore = useAuthStore()
const { collapsed, mobileOpen, toggle, toggleMobile, closeMobile } = useSidebar()

const bookingItems = [
  { to: '/receptionist/bookings', active: 'bookings', label: 'Daftar Pemesanan', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' },
]
const otherItems = [
  { to: '/receptionist/reports', active: 'reports', label: 'Laporan Harian', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' },
  { to: '/profile', active: 'profile', label: 'Profil Saya', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>' },
]

const handleLogout = async () => { await authStore.logout(); navigateTo('/login') }
</script>
