<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
      
      <div>
        <img class="mx-auto h-20 w-auto" src="/public/images/Logo.png" alt="Terminal Pintar Logo">
        <h1 class="mt-4 text-center text-3xl font-bold text-[#78AE4E]">
          Terminal Pintar
        </h1>
      </div>

      <div class="bg-white shadow-xl rounded-lg p-8 space-y-6">
        <h2 class="text-center text-2xl font-semibold text-gray-900">
          Masuk ke Akun Anda
        </h2>

        <form @submit.prevent="handleLogin" class="space-y-6">
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email atau username
            </label>
            <div class="mt-1">
              <input 
                v-model="form.email"
                id="email" 
                name="email" 
                type="email" 
                autocomplete="email" 
                required 
                placeholder="Masukkan email atau username"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500"
              >
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <div class="mt-1">
              <input 
                v-model="form.password"
                id="password" 
                name="password" 
                type="password" 
                autocomplete="current-password" 
                required 
                placeholder="Masukkan Password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500"
              >
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input 
                v-model="form.remember"
                id="remember-me" 
                name="remember-me" 
                type="checkbox" 
                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
              >
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Ingat Saya
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-[#78AE4E] hover:text-green-500">
                Lupa Password?
              </a>
            </div>
          </div>

          <div>
            <button 
              type="submit" 
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#78AE4E] hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Login
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router'; // Untuk redirect setelah login

// Gunakan useRouter untuk mengarahkan pengguna
const router = useRouter();

// Simpan data form dalam state reaktif
const form = reactive({
  email: '',
  password: '',
  remember: false
});

// Fungsi yang akan dipanggil saat form disubmit
const handleLogin = async () => {
  try {
    // 1. Kirim request login
    const response = await axios.post('/api/login', {
      email: form.email,
      password: form.password
    });

    // 2. Ambil token dan peran dari response server
    const token = response.data.access_token;
    const userRole = response.data.user?.role?.nama_role || '';

    // 3. SIMPAN token ke localStorage (PENTING!)
    localStorage.setItem('token', token);
    localStorage.setItem('userRole', userRole);

    // 4. Set default header Axios agar request berikutnya membawa token ini
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    // 5. Redirect ke halaman dashboard
    router.push('/dashboard'); 

  } catch (error) {
    console.error('Login gagal:', error);
    alert('Login Gagal! Periksa email dan password Anda.');
  }
};
</script>

<style scoped>
/* Tailwind CSS sudah menangani semua styling di template */
</style>