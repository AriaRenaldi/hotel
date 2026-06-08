<template>
  <div class="min-h-screen bg-gradient-to-br from-sky-50 via-blue-50 to-white">
    <!-- Mobile Menu Button -->
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-xl shadow-lg border border-blue-100">
      <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>

    <!-- Sidebar Overlay -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/30 z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', collapsed ? 'lg:w-20' : 'lg:w-72']" 
           class="fixed left-0 top-0 h-full w-72 bg-gradient-to-b from-white via-sky-50 to-blue-50 text-gray-800 shadow-xl border-r border-blue-100 z-50 transition-all duration-300 ease-in-out flex flex-col">
      
      <!-- Logo -->
      <div class="p-6 flex items-center justify-between border-b border-blue-100">
        <div class="flex items-center space-x-3" v-if="!collapsed">
          <div class="w-11 h-11 bg-gradient-to-br from-sky-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200 flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div>
            <span class="text-lg font-bold bg-gradient-to-r from-sky-600 to-blue-700 bg-clip-text text-transparent">HotelKu</span>
            <p class="text-xs text-gray-400">{{ subtitle }}</p>
          </div>
        </div>
        <div v-if="!collapsed" class="hidden lg:block">
          <button @click="collapsed = true" class="p-1.5 rounded-lg hover:bg-sky-100 transition">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
          </button>
        </div>
        <div v-if="collapsed" class="hidden lg:flex justify-center w-full">
          <button @click="collapsed = false" class="p-1.5 rounded-lg hover:bg-sky-100 transition">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <template v-for="section in sections" :key="section.title">
          <p v-if="!collapsed" class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2 mt-4 first:mt-0">{{ section.title }}</p>
          <NuxtLink 
            v-for="item in section.items" 
            :key="item.to"
            :to="item.to" 
            :class="[
              active === item.active ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-blue-700 shadow-sm' : 'text-gray-600 hover:bg-sky-50 hover:text-sky-700',
              collapsed ? 'justify-center' : ''
            ]" 
            class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group"
            @click="sidebarOpen = false"
          >
            <div v-html="item.icon" class="w-5 h-5 flex-shrink-0"></div>
            <span v-if="!collapsed" class="font-medium">{{ item.label }}</span>
            <span v-if="collapsed" class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 whitespace-nowrap z-50 pointer-events-none transition">{{ item.label }}</span>
          </NuxtLink>
        </template>
      </nav>
      
      <!-- Logout -->
      <div class="p-4 border-t border-blue-100">
        <button @click="handleLogout" :class="collapsed ? 'justify-center' : ''" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition w-full">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          <span v-if="!collapsed" class="font-medium">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div :class="collapsed ? 'lg:ml-20' : 'lg:ml-72'" class="transition-all duration-300">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

const props = defineProps({
  active: { type: String, default: '' },
  subtitle: { type: String, default: 'Panel' },
  sections: { type: Array, default: () => [] }
})

const authStore = useAuthStore()
const sidebarOpen = ref(false)
const collapsed = ref(false)

const handleLogout = async () => {
  await authStore.logout()
  navigateTo('/login')
}
</script>
