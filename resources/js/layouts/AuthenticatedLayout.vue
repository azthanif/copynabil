<template>
  <div class="flex h-screen bg-gray-100">

    <aside class="w-64 shrink-0 bg-white shadow-lg">
      <div class="flex flex-col h-full">

        <div class="h-20 flex items-center justify-center border-b">
          <img class="h-10 w-auto" src="/public/images/Logo.png" alt="Terminal Pintar Logo">
          <span class="ml-2 text-xl font-bold text-[#78AE4E]">Terminal Pintar</span>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-2">

          <div>
            <button @click="toggleMenu('menuUtama')"
              class="w-full flex justify-between items-center text-left text-gray-600 hover:bg-gray-50 p-2 rounded-lg">
              <span class="flex items-center">
                <Bars3Icon class="w-5 h-5 mr-3" />
                <span class="font-medium">Menu Utama</span>
              </span>
              <ChevronDownIcon :class="menus.menuUtama ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" />
            </button>

            <div v-if="menus.menuUtama" class="mt-1 pl-6 space-y-1">
              <router-link to="/dashboard" class="flex items-center text-gray-600 hover:text-green-600 p-2 rounded-lg"
                active-class="bg-green-100 text-[#78AE4E] font-semibold">
                <HomeIcon class="w-5 h-5 mr-3" />
                Beranda
              </router-link>
            </div>
          </div>

          <div>
            <button @click="toggleMenu('pengawasan')"
              class="w-full flex justify-between items-center text-left text-gray-600 hover:bg-gray-50 p-2 rounded-lg">
              <span class="flex items-center">
                <MagnifyingGlassCircleIcon class="w-5 h-5 mr-3" />
                <span class="font-medium">Pengawasan</span>
              </span>
              <ChevronDownIcon :class="menus.pengawasan ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" />
            </button>

            <div v-if="menus.pengawasan" class="mt-1 pl-6 space-y-1">
              <router-link to="/dashboard-jadwal"
                class="flex items-center text-gray-600 hover:text-green-600 p-2 rounded-lg"
                active-class="bg-green-100 text-[#78AE4E] font-semibold">
                <CalendarIcon class="w-5 h-5 mr-3" />
                Jadwal
              </router-link>
              <router-link to="/dashboard-catatan"
                class="flex items-center text-gray-600 hover:text-green-600 p-2 rounded-lg"
                active-class="bg-green-100 text-[#78AE4E] font-semibold">
                <DocumentTextIcon class="w-5 h-5 mr-3" />
                Catatan Guru
              </router-link>
            </div>
          </div>

          <div v-if="userRole === 'Admin'">
            <button @click="toggleMenu('kelolaAdmin')"
              class="w-full flex justify-between items-center text-left text-gray-600 hover:bg-gray-50 p-2 rounded-lg">
              <span class="flex items-center">
                <UserGroupIcon class="w-5 h-5 mr-3" />
                <span class="font-medium">Kelola Data</span>
              </span>
              <ChevronDownIcon :class="menus.kelolaAdmin ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" />
            </button>

            <div v-if="menus.kelolaAdmin" class="mt-1 pl-6 space-y-1">
              <router-link to="/admin/siswa"
                class="flex items-center text-gray-600 hover:text-green-600 p-2 rounded-lg"
                active-class="bg-green-100 text-[#78AE4E] font-semibold">
                <DocumentTextIcon class="w-5 h-5 mr-3" />
                Kelola Siswa
              </router-link>
            </div>
          </div>

        </nav>
      </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">

      <header class="h-20 bg-white border-b flex items-center justify-end px-6">
        <div class="flex items-center space-x-4">

          <div class="relative">

            <button @click="isNotifOpen = !isNotifOpen" class="text-gray-500 hover:text-gray-700 relative notif-button">
              <BellIcon class="w-6 h-6" />
              <span class="absolute -top-1 -right-1 flex h-4 w-4">
                <span
                  class="relative inline-flex rounded-full h-4 w-4 bg-red-500 justify-center items-center text-white text-xs">
                  {{ notifications.length }}
                </span>
              </span>
            </button>

            <transition enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95">
              <div v-if="isNotifOpen" ref="notifMenu"
                class="absolute top-full right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border z-50">
                <div class="flex justify-between items-center p-4 border-b">
                  <h3 class="text-lg font-semibold text-gray-900">Notifikasi</h3>
                  <button class="text-sm text-[#78AE4E] font-medium hover:text-opacity-80">
                    Tandai semua telah dibaca
                  </button>
                </div>

                <div class="py-2 max-h-96 overflow-y-auto">
                  <a v-for="notif in notifications" :key="notif.id" href="#"
                    class="flex items-center px-4 py-3 hover:bg-gray-50">
                    <div class="shrink-0">
                      <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                      <p class="text-sm font-medium text-gray-900">{{ notif.title }}</p>
                      <p class="text-sm text-gray-600 truncate">{{ notif.body }}</p>
                    </div>
                  </a>

                  <div v-if="notifications.length === 0" class="px-4 py-3 text-center text-gray-500">
                    Tidak ada notifikasi baru.
                  </div>
                </div>

              </div>
            </transition>
          </div>

          <div class="relative">

            <button @click="isProfileOpen = !isProfileOpen" class="text-gray-500 hover:text-gray-700 profile-button">
              <UserCircleIcon class="w-8 h-8" />
            </button>

            <transition enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95">
              <div v-if="isProfileOpen" ref="profileMenu"
                class="absolute top-full right-0 mt-3 w-80 bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.15)] border border-gray-200 z-50 p-6">

                <!-- Avatar -->
                <div class="flex flex-col items-center text-center">
                  <UserCircleIcon class="w-16 h-16 text-gray-400 mb-3" />

                  <div class="text-lg font-semibold text-gray-900">
                    Ibu Rezky Nur Amalia
                  </div>
                  <div class="text-sm text-gray-500 mt-1">
                    Amel@gmail.com
                  </div>
                </div>

                <!-- Tombol Logout -->
                <div class="mt-6">
                  <button @click="confirmLogout"
                    class="w-full py-2.5 border border-red-400 text-red-500 rounded-lg hover:bg-red-50 transition font-medium">
                    Keluar
                  </button>
                </div>

              </div>
            </transition>

          </div>

        </div>

      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
        <router-view></router-view>
      </main>
    </div>

  </div>
  <div v-if="showLogoutModal"
    class="fixed inset-0 z-60 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-80 p-6 transform transition-all scale-100">
      <div class="flex justify-center mb-4">
        <ExclamationCircleIcon class="w-20 h-20 text-red-500" />
      </div>
      <h3 class="text-xl font-bold text-gray-900 text-center mb-6">
        Yakin ingin keluar?
      </h3>
      <div class="flex justify-center gap-4">
        <button @click="showLogoutModal = false"
          class="px-6 py-2 rounded-lg bg-blue-100 text-blue-600 font-semibold hover:bg-blue-200 transition-colors">
          Tidak
        </button>
        <button @click="handleLogout"
          class="px-8 py-2 rounded-lg bg-red-400 text-white font-semibold hover:bg-red-500 transition-colors shadow-md">
          Ya
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';

// Impor ikon dari Heroicons (pastikan Anda sudah npm install @heroicons/vue)
import {
  Bars3Icon,
  HomeIcon,
  MagnifyingGlassCircleIcon,
  CalendarIcon,
  DocumentTextIcon,
  BellIcon,
  UserCircleIcon,
  ChevronDownIcon,
  ExclamationCircleIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline';

const isProfileOpen = ref(false);
const profileMenu = ref(null);
const isNotifOpen = ref(false);
const notifMenu = ref(null);
const router = useRouter();
// State untuk mengontrol menu dropdown
const menus = reactive({
  menuUtama: true, // Default terbuka
  pengawasan: true, // Default terbuka
  kelolaAdmin: true
});
const userRole = ref(localStorage.getItem('userRole') || '');

const handleClickOutside = (event) => {
  if (profileMenu.value && !profileMenu.value.contains(event.target)) {
    // Cek jika target BUKAN tombol profil itu sendiri
    if (!event.target.closest('.profile-button')) {
      isProfileOpen.value = false;
    }
  }
  if (notifMenu.value && !notifMenu.value.contains(event.target)) {
    if (!event.target.closest('.notif-button')) {
      isNotifOpen.value = false;
    }
  }
};

const notifications = ref([
  { id: 1, title: 'Pesan Baru', body: 'Pesan Baru dari Ibu Ica, Guru Bahasa Inggris' },
  { id: 2, title: 'Pesan Baru', body: 'Pesan Baru dari Bapak Fakhri, Guru Bahasa Jepang' },
]);

document.addEventListener('click', handleClickOutside);


onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);

});

const showLogoutModal = ref(false);

const handleLogout = async () => {
  try {
    await axios.post('/api/logout');
  } catch (error) {
    console.error('Logout error:', error);
  } finally {
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];
    localStorage.removeItem('userRole');
    router.push('/login');
  }
};

const toggleMenu = (menu) => {
  menus[menu] = !menus[menu];
};

const confirmLogout = () => {
  isProfileOpen.value = false; // Tutup dropdown profil
  showLogoutModal.value = true; // Buka modal konfirmasi
};

</script>

<style scoped>
/* Menambahkan style khusus untuk active-class router-link */
.router-link-exact-active {
  background-color: #f0fdf4;
  /* bg-green-50 */
  color: #16a34a;
  /* text-green-700 */
  font-weight: 600;
  /* font-semibold */
}
</style>