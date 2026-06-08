<template>
  <div class="min-h-screen bg-gradient-to-br from-[#f3f7fb] via-[#eef4fb] to-[#dbe6f3] text-slate-800">
   <template v-if="authStore.isAdmin">
  <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
      <NuxtLink to="/admin/dashboard" class="flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-white shadow-lg">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
        </div>
        <div>
          <p class="text-lg font-bold text-slate-900">HotelKu</p>
          <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Admin Profile</p>
        </div>
      </NuxtLink>

      <div class="flex flex-wrap items-center gap-2 rounded-full border border-amber-100/80 bg-white/80 p-2 text-sm font-medium text-slate-600 shadow-lg shadow-amber-100/40 backdrop-blur">
        <NuxtLink to="/admin/dashboard" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Dashboard</NuxtLink>
        <NuxtLink to="/admin/rooms" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Rooms</NuxtLink>
        <NuxtLink to="/admin/room-types" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Room Types</NuxtLink>
        <NuxtLink to="/admin/facilities" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Facilities</NuxtLink>
        <NuxtLink to="/admin/users" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Users</NuxtLink>
        <NuxtLink to="/admin/reports" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Reports</NuxtLink>
        <NuxtLink to="/admin/penalty-reports" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Penalty Reports</NuxtLink>
        <NuxtLink to="/profile" class="rounded-full bg-slate-900 px-4 py-2 font-semibold text-white shadow-md">Profil</NuxtLink>
        <button @click="handleLogout" class="rounded-full bg-rose-50 px-4 py-2 font-semibold text-rose-600 transition hover:bg-rose-100">Logout</button>
      </div>
    </div>
  </nav>

  <section class="relative overflow-hidden bg-gradient-to-br from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-white">
    <div class="absolute inset-0 opacity-20">
      <div class="absolute left-6 top-8 h-48 w-48 rounded-full bg-white/15 blur-3xl"></div>
      <div class="absolute right-16 bottom-0 h-64 w-64 rounded-full bg-amber-100/20 blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-16">
      <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-100">Executive Admin Suite</p>
      <div class="mt-4 grid lg:grid-cols-[1.15fr_0.85fr] gap-8 items-center">
        <div>
          <h1 class="text-4xl md:text-5xl font-bold leading-tight">Profil administrator dengan nuansa gold yang elegan.</h1>
          <p class="mt-4 max-w-2xl text-lg text-amber-50 leading-relaxed">Kelola identitas akun admin dan keamanan akses sistem dalam tampilan premium yang selaras dengan seluruh halaman admin.</p>
          <div class="mt-8 grid sm:grid-cols-3 gap-4">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Admin</p>
              <p class="mt-2 text-lg font-semibold">{{ authStore.user?.username || 'Administrator' }}</p>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Status</p>
              <p class="mt-2 text-lg font-semibold">Full access</p>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Security</p>
              <p class="mt-2 text-lg font-semibold">Protected</p>
            </div>
          </div>
        </div>

        <div class="rounded-[2rem] border border-white/20 bg-white/95 p-8 shadow-2xl shadow-amber-900/10 text-slate-900 backdrop-blur">
          <div class="flex items-center gap-5">
            <div class="h-20 w-20 overflow-hidden rounded-[1.75rem] border border-amber-200 bg-amber-50 shadow-xl shadow-amber-300/40">
              <img v-if="currentPhotoUrl" :src="currentPhotoUrl" alt="Foto profil admin" class="h-full w-full object-cover">
              <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-3xl font-bold text-white">
                {{ getInitial(authStore.user?.full_name, 'A') }}
              </div>
            </div>
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Admin Identity</p>
              <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ authStore.user?.full_name }}</h2>
              <p class="mt-1 text-slate-500 break-all">{{ authStore.user?.email }}</p>
              <span class="mt-3 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold capitalize text-amber-800">
                {{ authStore.user?.role || 'admin' }}
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-2 gap-4">
            <div class="rounded-2xl bg-amber-50/70 p-4">
              <p class="text-sm text-slate-500">Username</p>
              <p class="mt-2 font-semibold text-slate-900">{{ authStore.user?.username || '-' }}</p>
            </div>
            <div class="rounded-2xl bg-amber-50/70 p-4">
              <p class="text-sm text-slate-500">Telepon</p>
              <p class="mt-2 font-semibold text-slate-900">{{ authStore.user?.phone || '-' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="grid xl:grid-cols-[1.1fr_0.9fr] gap-8 items-start">
      <div class="rounded-[2rem] border border-amber-100 bg-white p-8 shadow-2xl shadow-amber-100/60">
        <div class="mb-8 flex items-start justify-between gap-4">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Admin Details</p>
            <h2 class="mt-2 text-3xl font-bold text-slate-900">Perbarui informasi akun</h2>
            <p class="mt-3 text-slate-500 leading-relaxed">Pastikan data akun administrator tetap akurat dan terbaru.</p>
          </div>
          <div class="hidden sm:flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-100 to-amber-50 text-amber-700">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>

        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/60 p-4">
          <p class="text-sm font-semibold text-amber-800">Foto Profil</p>
          <div class="mt-3 flex items-center gap-4">
            <div class="h-20 w-20 overflow-hidden rounded-2xl border border-amber-200 bg-white">
              <img v-if="currentPhotoUrl" :src="currentPhotoUrl" alt="Preview foto profil" class="h-full w-full object-cover">
              <div v-else class="flex h-full w-full items-center justify-center bg-amber-100 text-xl font-bold text-amber-700">
                {{ getInitial(authStore.user?.full_name, 'A') }}
              </div>
            </div>
            <div class="flex flex-wrap gap-2">
              <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="handlePhotoChange">
              <button type="button" @click="pickPhoto" class="rounded-xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] px-4 py-2 text-sm font-semibold text-white">
                Pilih Foto
              </button>
              <button type="button" @click="uploadPhoto" :disabled="!selectedPhoto || uploadingPhoto" class="rounded-xl bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-800 disabled:cursor-not-allowed disabled:opacity-50">
                {{ uploadingPhoto ? 'Uploading...' : 'Upload' }}
              </button>
              <button type="button" @click="removePhoto" :disabled="!authStore.user?.photo_url || uploadingPhoto" class="rounded-xl bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 disabled:cursor-not-allowed disabled:opacity-50">
                Hapus
              </button>
            </div>
          </div>
          <p class="mt-2 text-xs text-amber-700">Format JPG/PNG/WEBP, maksimal 2MB.</p>
        </div>

        <form @submit.prevent="updateProfile" class="space-y-5">
          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
              <input v-model="profileForm.username" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
              <input v-model="profileForm.full_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
            </div>
          </div>

          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">No. Telepon</label>
              <input v-model="profileForm.phone" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
              <input :value="authStore.user?.email || ''" type="text" disabled class="w-full cursor-not-allowed rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3.5 text-slate-500">
            </div>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
            <textarea v-model="profileForm.address" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"></textarea>
          </div>

          <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
            Simpan Perubahan
          </button>
        </form>
      </div>

      <div class="rounded-[2rem] border border-amber-100 bg-white p-8 shadow-2xl shadow-amber-100/60">
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Security Center</p>
        <h3 class="mt-2 text-3xl font-bold text-slate-900">Ubah password</h3>
        <p class="mt-3 text-slate-500">Perbarui password akun administrator secara berkala untuk menjaga keamanan akses.</p>

        <form @submit.prevent="changePassword" class="mt-8 space-y-4">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Password Saat Ini</label>
            <input v-model="passwordForm.current_password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Password Baru</label>
            <input v-model="passwordForm.new_password" type="password" required minlength="6" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
            <input v-model="passwordForm.new_password_confirmation" type="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
          </div>
          <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-[#8a671f] to-[#d4af37] px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
            Ubah Password
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

    <template v-else-if="authStore.isReceptionist">
      <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
          <NuxtLink to="/receptionist/dashboard" class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#4b5563] via-[#6b7280] to-[#9ca3af] text-white shadow-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div>
              <p class="text-lg font-bold text-slate-900">HotelKu</p>
              <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Reception Profile</p>
            </div>
          </NuxtLink>

          <div class="flex flex-wrap items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 p-2 text-sm font-medium text-slate-600 shadow-lg shadow-slate-200/40 backdrop-blur">
            <NuxtLink to="/receptionist/dashboard" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">Dashboard</NuxtLink>
            <NuxtLink to="/receptionist/bookings" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">Bookings</NuxtLink>
            <NuxtLink to="/receptionist/reports" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">Reports</NuxtLink>
            <NuxtLink to="/receptionist/penalty-verifications" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">Verifikasi Denda</NuxtLink>
            <NuxtLink to="/receptionist/penalty-reports" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">Laporan Denda</NuxtLink>
            <NuxtLink to="/profile" class="rounded-full bg-slate-800 px-4 py-2 font-semibold text-white shadow-md">Profil</NuxtLink>
            <button @click="handleLogout" class="rounded-full bg-rose-50 px-4 py-2 font-semibold text-rose-600 transition hover:bg-rose-100">Logout</button>
          </div>
        </div>
      </nav>

      <section class="relative overflow-hidden bg-gradient-to-br from-[#4b5563] via-[#6b7280] to-[#9ca3af] text-white">
        <div class="absolute inset-0 opacity-20">
          <div class="absolute left-6 top-8 h-48 w-48 rounded-full bg-white/15 blur-3xl"></div>
          <div class="absolute right-16 bottom-0 h-64 w-64 rounded-full bg-slate-200/25 blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-16">
          <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">Reception Profile Suite</p>
          <div class="mt-4 grid lg:grid-cols-[1.15fr_0.85fr] gap-8 items-center">
            <div>
              <h1 class="text-4xl md:text-5xl font-bold leading-tight">Profil resepsionis dengan nuansa silver yang elegan.</h1>
              <p class="mt-4 max-w-2xl text-lg text-slate-100 leading-relaxed">Kelola identitas akun dan keamanan akses front desk dalam tampilan yang selaras dengan halaman receptionist lainnya.</p>
              <div class="mt-8 grid sm:grid-cols-3 gap-4">
                <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Staff</p>
                  <p class="mt-2 text-lg font-semibold">{{ authStore.user?.username || 'Receptionist' }}</p>
                </div>
                <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Status</p>
                  <p class="mt-2 text-lg font-semibold">On duty</p>
                </div>
                <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Access</p>
                  <p class="mt-2 text-lg font-semibold">Secured</p>
                </div>
              </div>
            </div>

            <div class="rounded-[2rem] border border-white/20 bg-white/95 p-8 shadow-2xl shadow-slate-900/10 text-slate-900 backdrop-blur">
              <div class="flex items-center gap-5">
                <div class="h-20 w-20 overflow-hidden rounded-[1.75rem] border border-slate-200 bg-slate-100 shadow-xl shadow-slate-300/40">
                  <img v-if="currentPhotoUrl" :src="currentPhotoUrl" alt="Foto profil receptionist" class="h-full w-full object-cover">
                  <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#4b5563] via-[#6b7280] to-[#9ca3af] text-3xl font-bold text-white">
                    {{ getInitial(authStore.user?.full_name, 'R') }}
                  </div>
                </div>
                <div>
                  <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Reception Identity</p>
                  <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ authStore.user?.full_name }}</h2>
                  <p class="mt-1 break-all text-slate-500">{{ authStore.user?.email }}</p>
                  <span :class="roleBadgeClass" class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize">
                    {{ authStore.user?.role || 'receptionist' }}
                  </span>
                </div>
              </div>

              <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="text-sm text-slate-500">Telepon</p>
                  <p class="mt-2 font-semibold text-slate-900">{{ authStore.user?.phone || '-' }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="text-sm text-slate-500">Alamat</p>
                  <p class="mt-2 line-clamp-2 font-semibold text-slate-900">{{ authStore.user?.address || '-' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <div class="grid xl:grid-cols-[1.1fr_0.9fr] gap-8 items-start">
          <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
            <div class="mb-8 flex items-start justify-between gap-4">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Profile Details</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Perbarui informasi resepsionis</h2>
                <p class="mt-3 text-slate-500 leading-relaxed">Pastikan data akun front desk selalu akurat agar operasional hotel berjalan lancar.</p>
              </div>
              <div class="hidden sm:flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-100 to-zinc-100 text-slate-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>

            <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
              <p class="text-sm font-semibold text-slate-700">Foto Profil</p>
              <div class="mt-3 flex items-center gap-4">
                <div class="h-20 w-20 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                  <img v-if="currentPhotoUrl" :src="currentPhotoUrl" alt="Preview foto profil" class="h-full w-full object-cover">
                  <div v-else class="flex h-full w-full items-center justify-center bg-slate-200 text-xl font-bold text-slate-700">
                    {{ getInitial(authStore.user?.full_name, 'R') }}
                  </div>
                </div>
                <div class="flex flex-wrap gap-2">
                  <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="handlePhotoChange">
                  <button type="button" @click="pickPhoto" class="rounded-xl bg-gradient-to-r from-[#4b5563] via-[#6b7280] to-[#9ca3af] px-4 py-2 text-sm font-semibold text-white">
                    Pilih Foto
                  </button>
                  <button type="button" @click="uploadPhoto" :disabled="!selectedPhoto || uploadingPhoto" class="rounded-xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ uploadingPhoto ? 'Uploading...' : 'Upload' }}
                  </button>
                  <button type="button" @click="removePhoto" :disabled="!authStore.user?.photo_url || uploadingPhoto" class="rounded-xl bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Hapus
                  </button>
                </div>
              </div>
              <p class="mt-2 text-xs text-slate-500">Format JPG/PNG/WEBP, maksimal 2MB.</p>
            </div>

            <form @submit.prevent="updateProfile" class="space-y-5">
              <div class="grid md:grid-cols-2 gap-5">
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
                  <input v-model="profileForm.username" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                  <input v-model="profileForm.full_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition">
                </div>
              </div>

              <div class="grid md:grid-cols-2 gap-5">
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">No. Telepon</label>
                  <input v-model="profileForm.phone" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                  <input :value="authStore.user?.email || ''" type="text" disabled class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3.5 text-slate-500 cursor-not-allowed">
                </div>
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
                <textarea v-model="profileForm.address" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition"></textarea>
              </div>

              <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-[#4b5563] via-[#6b7280] to-[#9ca3af] px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
                Simpan Perubahan Profil
              </button>
            </form>
          </div>

          <div class="space-y-8">
            <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
              <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Security Center</p>
              <h3 class="mt-2 text-3xl font-bold text-slate-900">Jaga keamanan akun Anda</h3>
              <p class="mt-3 text-slate-500 leading-relaxed">Ubah password secara berkala agar akses receptionist tetap aman dan terkendali.</p>

              <form @submit.prevent="changePassword" class="mt-6 space-y-4">
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Password Saat Ini</label>
                  <input v-model="passwordForm.current_password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Password Baru</label>
                  <input v-model="passwordForm.new_password" type="password" required minlength="6" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
                  <input v-model="passwordForm.new_password_confirmation" type="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition">
                </div>
                <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-slate-700 to-slate-500 px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
                  Perbarui Password
                </button>
              </form>
            </div>

            <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
              <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Reception Benefits</p>
              <h3 class="mt-2 text-2xl font-bold text-slate-900">Ruang akun yang lebih profesional</h3>
              <div class="mt-6 grid gap-4">
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="font-semibold text-slate-900">Akses lebih cepat</p>
                  <p class="mt-1 text-sm text-slate-500">Data akun yang rapi membantu pekerjaan front desk berjalan lebih efisien.</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="font-semibold text-slate-900">Keamanan lebih baik</p>
                  <p class="mt-1 text-sm text-slate-500">Informasi akun dan password terlindungi dengan baik.</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="font-semibold text-slate-900">Tampilan lebih selaras</p>
                  <p class="mt-1 text-sm text-slate-500">Halaman profil terasa konsisten dengan dashboard, bookings, dan reports receptionist.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template v-else>
      <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
          <NuxtLink to="/guest/dashboard" class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] text-amber-300 shadow-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div>
              <p class="text-lg font-bold text-slate-900">HotelKu</p>
              <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Guest Profile</p>
            </div>
          </NuxtLink>

          <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-600">
            <NuxtLink to="/guest/dashboard" class="transition hover:text-[#1b456f]">Dashboard</NuxtLink>
            <NuxtLink to="/guest/rooms" class="transition hover:text-[#1b456f]">Rooms</NuxtLink>
            <NuxtLink to="/guest/booking" class="transition hover:text-[#1b456f]">Booking</NuxtLink>
            <NuxtLink to="/guest/check-booking" class="transition hover:text-[#1b456f]">Check Booking</NuxtLink>
            <NuxtLink to="/profile" class="text-[#1b456f] font-semibold">Profil</NuxtLink>
            <button @click="handleLogout" class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">Logout</button>
          </div>
        </div>
      </nav>

      <section class="relative overflow-hidden bg-gradient-to-br from-[#07111f] via-[#0f2742] to-[#163b63] text-white">
        <div class="absolute inset-0 opacity-20">
          <div class="absolute left-6 top-8 h-48 w-48 rounded-full bg-white/10 blur-3xl"></div>
          <div class="absolute right-16 bottom-0 h-64 w-64 rounded-full bg-blue-300/20 blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-16">
          <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-100">Personal Guest Suite</p>
          <div class="mt-4 grid lg:grid-cols-[1.15fr_0.85fr] gap-8 items-center">
            <div>
              <h1 class="text-4xl md:text-5xl font-bold leading-tight">Profil tamu dengan tampilan yang lebih elegan dan nyaman.</h1>
              <p class="mt-4 max-w-2xl text-lg text-slate-200 leading-relaxed">Kelola data pribadi dan keamanan akun Anda dalam satu ruang eksklusif dengan nuansa navy premium khas HotelKu.</p>
              <div class="mt-8 grid sm:grid-cols-3 gap-4">
                <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Member</p>
                  <p class="mt-2 text-lg font-semibold">{{ authStore.user?.username || 'Guest' }}</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Status</p>
                  <p class="mt-2 text-lg font-semibold">Akun aktif</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Privacy</p>
                  <p class="mt-2 text-lg font-semibold">Terlindungi</p>
                </div>
              </div>
            </div>

            <div class="rounded-[2rem] border border-white/10 bg-white/95 p-8 shadow-2xl shadow-slate-900/20 text-slate-900 backdrop-blur">
              <div class="flex items-center gap-5">
                <div class="h-20 w-20 overflow-hidden rounded-[1.75rem] border border-slate-200 bg-slate-100 shadow-xl shadow-blue-200/40">
                  <img v-if="currentPhotoUrl" :src="currentPhotoUrl" alt="Foto profil guest" class="h-full w-full object-cover">
                  <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#07111f] via-[#0f2742] to-[#1b456f] text-3xl font-bold text-white">
                    {{ getInitial(authStore.user?.full_name, 'U') }}
                  </div>
                </div>
                <div>
                  <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Guest Identity</p>
                  <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ authStore.user?.full_name }}</h2>
                  <p class="mt-1 text-slate-500 break-all">{{ authStore.user?.email }}</p>
                  <span :class="roleBadgeClass" class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize">
                    {{ authStore.user?.role || 'guest' }}
                  </span>
                </div>
              </div>

              <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="text-sm text-slate-500">Telepon</p>
                  <p class="mt-2 font-semibold text-slate-900">{{ authStore.user?.phone || '-' }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="text-sm text-slate-500">Alamat</p>
                  <p class="mt-2 font-semibold text-slate-900 line-clamp-2">{{ authStore.user?.address || '-' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <div class="grid xl:grid-cols-[1.1fr_0.9fr] gap-8 items-start">
          <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
            <div class="mb-8 flex items-start justify-between gap-4">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Profile Details</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Perbarui informasi pribadi</h2>
                <p class="mt-3 text-slate-500 leading-relaxed">Pastikan data Anda selalu akurat agar proses reservasi, check-in, dan layanan hotel berjalan lebih lancar.</p>
              </div>
              <div class="hidden sm:flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-100 to-blue-50 text-[#1b456f]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>

            <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50/70 p-4">
              <p class="text-sm font-semibold text-[#1b456f]">Foto Profil</p>
              <div class="mt-3 flex items-center gap-4">
                <div class="h-20 w-20 overflow-hidden rounded-2xl border border-blue-200 bg-white">
                  <img v-if="currentPhotoUrl" :src="currentPhotoUrl" alt="Preview foto profil" class="h-full w-full object-cover">
                  <div v-else class="flex h-full w-full items-center justify-center bg-blue-100 text-xl font-bold text-[#1b456f]">
                    {{ getInitial(authStore.user?.full_name, 'U') }}
                  </div>
                </div>
                <div class="flex flex-wrap gap-2">
                  <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="handlePhotoChange">
                  <button type="button" @click="pickPhoto" class="rounded-xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] px-4 py-2 text-sm font-semibold text-white">
                    Pilih Foto
                  </button>
                  <button type="button" @click="uploadPhoto" :disabled="!selectedPhoto || uploadingPhoto" class="rounded-xl bg-blue-100 px-4 py-2 text-sm font-semibold text-[#1b456f] disabled:cursor-not-allowed disabled:opacity-50">
                    {{ uploadingPhoto ? 'Uploading...' : 'Upload' }}
                  </button>
                  <button type="button" @click="removePhoto" :disabled="!authStore.user?.photo_url || uploadingPhoto" class="rounded-xl bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Hapus
                  </button>
                </div>
              </div>
              <p class="mt-2 text-xs text-[#1b456f]">Format JPG/PNG/WEBP, maksimal 2MB.</p>
            </div>

            <form @submit.prevent="updateProfile" class="space-y-5">
              <div class="grid md:grid-cols-2 gap-5">
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
                  <input v-model="profileForm.username" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1b456f] focus:border-transparent transition">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                  <input v-model="profileForm.full_name" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1b456f] focus:border-transparent transition">
                </div>
              </div>

              <div class="grid md:grid-cols-2 gap-5">
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">No. Telepon</label>
                  <input v-model="profileForm.phone" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1b456f] focus:border-transparent transition">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                  <input :value="authStore.user?.email || ''" type="text" disabled class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3.5 text-slate-500 cursor-not-allowed">
                </div>
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
                <textarea v-model="profileForm.address" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1b456f] focus:border-transparent transition"></textarea>
              </div>

              <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
                Simpan Perubahan Profil
              </button>
            </form>
          </div>

          <div class="space-y-8">
            <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
              <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Security Center</p>
              <h3 class="mt-2 text-3xl font-bold text-slate-900">Jaga keamanan akun Anda</h3>
              <p class="mt-3 text-slate-500 leading-relaxed">Ubah password secara berkala agar akun Anda tetap aman selama menggunakan layanan HotelKu.</p>

              <form @submit.prevent="changePassword" class="mt-6 space-y-4">
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Password Saat Ini</label>
                  <input v-model="passwordForm.current_password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1b456f] focus:border-transparent transition">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Password Baru</label>
                  <input v-model="passwordForm.new_password" type="password" required minlength="6" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1b456f] focus:border-transparent transition">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
                  <input v-model="passwordForm.new_password_confirmation" type="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#1b456f] focus:border-transparent transition">
                </div>

                <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-[#0f2742] to-[#1b456f] px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-blue-200/40 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
                  Perbarui Password
                </button>
              </form>
            </div>

            <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
              <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Guest Benefits</p>
              <h3 class="mt-2 text-2xl font-bold text-slate-900">Ruang akun yang lebih personal</h3>
              <div class="mt-6 grid gap-4">
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="font-semibold text-slate-900">Reservasi lebih cepat</p>
                  <p class="mt-1 text-sm text-slate-500">Data tamu yang tersimpan membantu proses booking jadi lebih praktis.</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="font-semibold text-slate-900">Privasi lebih aman</p>
                  <p class="mt-1 text-sm text-slate-500">Informasi pribadi dan keamanan akun Anda tetap terlindungi.</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                  <p class="font-semibold text-slate-900">Pengalaman lebih elegan</p>
                  <p class="mt-1 text-sm text-slate-500">Semua pengaturan akun dirancang agar nyaman digunakan di desktop maupun mobile.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

definePageMeta({ middleware: 'auth' })

const authStore = useAuthStore()

const profileForm = reactive({
  username: '',
  full_name: '',
  phone: '',
  address: ''
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const selectedPhoto = ref(null)
const localPhotoPreview = ref('')
const uploadingPhoto = ref(false)
const photoInput = ref(null)

const currentPhotoUrl = computed(() => localPhotoPreview.value || authStore.user?.photo_url || '')

const roleBadgeClass = computed(() => {
  if (authStore.isGuest) return 'bg-blue-100 text-[#1b456f]'
  if (authStore.isReceptionist) return 'bg-slate-100 text-slate-700'
  return 'bg-slate-100 text-slate-700'
})

const syncProfileForm = () => {
  profileForm.username = authStore.user?.username || ''
  profileForm.full_name = authStore.user?.full_name || ''
  profileForm.phone = authStore.user?.phone || ''
  profileForm.address = authStore.user?.address || ''
  if (!selectedPhoto.value) {
    localPhotoPreview.value = ''
  }
}

const getInitial = (name, fallback) => {
  return (name || '').trim().charAt(0).toUpperCase() || fallback
}

const pickPhoto = () => {
  photoInput.value?.click()
}

const handlePhotoChange = (event) => {
  const target = event.target
  const file = target.files?.[0] || null
  selectedPhoto.value = file

  if (!file) {
    localPhotoPreview.value = ''
    return
  }

  localPhotoPreview.value = URL.createObjectURL(file)
}

const uploadPhoto = async () => {
  if (!selectedPhoto.value || !authStore.token) {
    return
  }

  try {
    uploadingPhoto.value = true
    const API_BASE = 'http://127.0.0.1:8000/api'
    const formData = new FormData()
    formData.append('photo', selectedPhoto.value)

    const response = await $fetch(`${API_BASE}/profile/photo`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: formData,
    })

    if (response.success) {
      alert('Foto profil berhasil diupload')
      selectedPhoto.value = null
      localPhotoPreview.value = ''
      if (photoInput.value) {
        photoInput.value.value = ''
      }
      await authStore.fetchProfile()
    }
  } catch (e) {
    alert(e?.data?.message || 'Gagal upload foto profil')
  } finally {
    uploadingPhoto.value = false
  }
}

const removePhoto = async () => {
  if (!authStore.token) {
    return
  }

  try {
    uploadingPhoto.value = true
    const API_BASE = 'http://127.0.0.1:8000/api'
    const response = await $fetch(`${API_BASE}/profile/photo`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${authStore.token}` },
    })

    if (response.success) {
      alert('Foto profil berhasil dihapus')
      selectedPhoto.value = null
      localPhotoPreview.value = ''
      if (photoInput.value) {
        photoInput.value.value = ''
      }
      await authStore.fetchProfile()
    }
  } catch (e) {
    alert(e?.data?.message || 'Gagal menghapus foto profil')
  } finally {
    uploadingPhoto.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

const updateProfile = async () => {
  try {
    const API_BASE = 'http://127.0.0.1:8000/api'
    const response = await $fetch(`${API_BASE}/profile`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: { ...profileForm }
    })

    if (response.success) {
      alert('Profil berhasil diupdate')
      await authStore.fetchProfile()
      syncProfileForm()
    }
  } catch (e) {
    alert(e?.data?.message || 'Gagal mengupdate profil')
  }
}

const changePassword = async () => {
  if (!passwordForm.current_password || !passwordForm.new_password || !passwordForm.new_password_confirmation) {
    alert('Semua field password wajib diisi')
    return
  }

  if (passwordForm.new_password.length < 6) {
    alert('Password baru minimal 6 karakter')
    return
  }

  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    alert('Konfirmasi password tidak cocok')
    return
  }

  try {
    const API_BASE = 'http://127.0.0.1:8000/api'
    const response = await $fetch(`${API_BASE}/profile/change-password`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: { ...passwordForm }
    })

    if (response.success) {
      alert('Password berhasil diubah')
      passwordForm.current_password = ''
      passwordForm.new_password = ''
      passwordForm.new_password_confirmation = ''
    }
  } catch (e) {
    alert(e?.data?.message || 'Gagal mengubah password')
  }
}

watch(
  () => authStore.user,
  () => {
    syncProfileForm()
  },
  { immediate: true }
)

onMounted(async () => {
  if (!authStore.user && authStore.token) {
    await authStore.fetchProfile()
  }
  syncProfileForm()
})
</script>
