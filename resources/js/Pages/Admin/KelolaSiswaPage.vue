<template>
  <div class="p-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Data Siswa</h1>
        <p class="text-gray-500">Modul administrasi untuk menambah, memperbarui, mencari dan menghapus siswa.</p>
      </div>
      <div class="flex gap-2">
        <input
          v-model="search"
          type="text"
          placeholder="Cari nama siswa, NIS, kelas atau orang tua"
          class="w-full lg:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
        />
        <button
          @click="fetchStudents()"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
        >
          Cari
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="bg-white shadow rounded-xl p-6 lg:col-span-1">
        <h2 class="text-lg font-semibold mb-4" v-text="isEditing ? 'Edit Siswa' : 'Tambah Siswa'"></h2>
        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">NIS</label>
            <input v-model="form.nis" type="text" :class="inputClass" placeholder="TP001" required />
            <p v-if="errors.nis" class="mt-1 text-sm text-red-600">{{ firstError(errors.nis) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input v-model="form.nama_lengkap" type="text" :class="inputClass" placeholder="Nama Siswa" required />
            <p v-if="errors.nama_lengkap" class="mt-1 text-sm text-red-600">{{ firstError(errors.nama_lengkap) }}</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
              <select v-model="form.jenis_kelamin" :class="inputClass" required>
                <option disabled value="">Pilih</option>
                <option
                  v-for="gender in genderOptions"
                  :key="gender"
                  :value="gender"
                >
                  {{ gender }}
                </option>
              </select>
              <p v-if="errors.jenis_kelamin" class="mt-1 text-sm text-red-600">{{ firstError(errors.jenis_kelamin) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
              <input v-model="form.tanggal_lahir" type="date" :class="inputClass" />
              <p v-if="errors.tanggal_lahir" class="mt-1 text-sm text-red-600">{{ firstError(errors.tanggal_lahir) }}</p>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Kelas</label>
            <input v-model="form.kelas" type="text" :class="inputClass" placeholder="Kelas 5" />
            <p v-if="errors.kelas" class="mt-1 text-sm text-red-600">{{ firstError(errors.kelas) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Alamat</label>
            <textarea v-model="form.alamat" rows="2" :class="inputClass" placeholder="Alamat lengkap"></textarea>
            <p v-if="errors.alamat" class="mt-1 text-sm text-red-600">{{ firstError(errors.alamat) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select v-model="form.status" :class="inputClass" required>
              <option disabled value="">Pilih</option>
              <option
                v-for="status in statusOptions"
                :key="status"
                :value="status"
              >
                {{ status }}
              </option>
            </select>
            <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ firstError(errors.status) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Orang Tua (ID User)</label>
            <input
              v-model="form.orang_tua_id"
              type="number"
              min="1"
              :class="inputClass"
              placeholder="Masukkan ID User"
              required
            />
            <p v-if="errors.orang_tua_id" class="mt-1 text-sm text-red-600">{{ firstError(errors.orang_tua_id) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Kontak Orang Tua</label>
            <input v-model="form.kontak_orang_tua" type="text" :class="inputClass" placeholder="Nomor telepon" />
            <p v-if="errors.kontak_orang_tua" class="mt-1 text-sm text-red-600">{{ firstError(errors.kontak_orang_tua) }}</p>
          </div>
          <div class="flex gap-3">
            <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
              {{ isEditing ? 'Perbarui' : 'Simpan' }}
            </button>
            <button
              v-if="isEditing"
              type="button"
              class="px-4 py-2 border border-gray-300 rounded-lg"
              @click="resetForm"
            >
              Batal
            </button>
          </div>
        </form>
      </div>

      <div class="lg:col-span-2 bg-white shadow rounded-xl">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="isLoading">
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Memuat data siswa...</td>
              </tr>
              <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold text-gray-800">{{ student.nis }}</td>
                <td class="px-4 py-3">
                  <p class="font-medium text-gray-900">{{ student.nama_lengkap }}</p>
                  <p class="text-sm text-gray-500">{{ formatTanggal(student.tanggal_lahir) }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ student.kelas || '-' }}</td>
                <td class="px-4 py-3">
                  <p class="text-gray-900">{{ student.orang_tua?.nama || '-' }}</p>
                  <p class="text-sm text-gray-500">{{ student.orang_tua?.email }}</p>
                </td>
                <td class="px-4 py-3">
                  <span
                    class="px-2 py-1 rounded-full text-xs font-semibold"
                    :class="statusClass(student.status)"
                  >
                    {{ student.status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                  <button
                    class="px-3 py-1 text-sm text-blue-600 hover:underline"
                    @click="editStudent(student)"
                  >
                    Edit
                  </button>
                  <button
                    class="px-3 py-1 text-sm text-red-600 hover:underline"
                    @click="deleteStudent(student.id)"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="!isLoading && students.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada data siswa.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t">
          <p class="text-sm text-gray-500">
            Menampilkan halaman {{ pagination.current_page }} dari {{ pagination.last_page }} ({{ pagination.total }} siswa)
          </p>
          <div class="space-x-2">
            <button
              class="px-4 py-2 border rounded-lg"
              :disabled="pagination.current_page === 1"
              @click="fetchStudents(pagination.current_page - 1)"
            >
              Sebelumnya
            </button>
            <button
              class="px-4 py-2 border rounded-lg"
              :disabled="pagination.current_page === pagination.last_page"
              @click="fetchStudents(pagination.current_page + 1)"
            >
              Selanjutnya
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import axios from 'axios';

const students = ref([]);
const pagination = reactive({ current_page: 1, last_page: 1, total: 0 });
const isLoading = ref(false);
const search = ref('');
const errors = reactive({});

const genderOptions = ['Laki-laki', 'Perempuan'];
const statusOptions = ['Aktif', 'Cuti', 'Lulus'];
const inputClass =
  'w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500';

const form = reactive({
  id: null,
  nis: '',
  nama_lengkap: '',
  jenis_kelamin: '',
  tanggal_lahir: '',
  kelas: '',
  alamat: '',
  status: '',
  orang_tua_id: '',
  kontak_orang_tua: ''
});

const isEditing = computed(() => Boolean(form.id));

const fetchStudents = async (page = 1) => {
  isLoading.value = true;
  try {
    const response = await axios.get('/api/admin/siswa', {
      params: {
        page,
        keyword: search.value
      }
    });
    students.value = response.data.data;
    pagination.current_page = response.data.current_page;
    pagination.last_page = response.data.last_page;
    pagination.total = response.data.total;
  } catch (error) {
    console.error('Gagal memuat data siswa', error);
  } finally {
    isLoading.value = false;
  }
};

const submitForm = async () => {
  Object.keys(errors).forEach((key) => (errors[key] = null));
  try {
    const payload = { ...form };
    delete payload.id;
    const request = form.id
      ? axios.put(`/api/admin/siswa/${form.id}`, payload)
      : axios.post('/api/admin/siswa', payload);

    await request;
    resetForm();
    fetchStudents(pagination.current_page);
  } catch (error) {
    if (error.response?.status === 422) {
      const serverErrors = error.response.data.errors || {};
      Object.assign(errors, serverErrors);
      if (!Object.keys(serverErrors).length && error.response.data.message) {
        alert(error.response.data.message);
      }
    } else {
      console.error('Gagal menyimpan data siswa', error);
    }
  }
};

const editStudent = (student) => {
  Object.assign(form, {
    id: student.id,
    nis: student.nis,
    nama_lengkap: student.nama_lengkap,
    jenis_kelamin: student.jenis_kelamin,
    tanggal_lahir: student.tanggal_lahir,
    kelas: student.kelas,
    alamat: student.alamat,
    status: student.status,
    orang_tua_id: student.orang_tua?.id,
    kontak_orang_tua: student.kontak_orang_tua
  });
};

const deleteStudent = async (id) => {
  if (!confirm('Yakin ingin menghapus data siswa ini?')) return;
  try {
    await axios.delete(`/api/admin/siswa/${id}`);
    if (students.value.length === 1 && pagination.current_page > 1) {
      fetchStudents(pagination.current_page - 1);
    } else {
      fetchStudents(pagination.current_page);
    }
  } catch (error) {
    console.error('Gagal menghapus data siswa', error);
  }
};

const resetForm = () => {
  Object.assign(form, {
    id: null,
    nis: '',
    nama_lengkap: '',
    jenis_kelamin: '',
    tanggal_lahir: '',
    kelas: '',
    alamat: '',
    status: '',
    orang_tua_id: '',
    kontak_orang_tua: ''
  });
  Object.keys(errors).forEach((key) => (errors[key] = null));
};

const statusClass = (status) => {
  switch (status) {
    case 'Aktif':
      return 'bg-green-100 text-green-700';
    case 'Cuti':
      return 'bg-yellow-100 text-yellow-700';
    case 'Lulus':
      return 'bg-blue-100 text-blue-700';
    default:
      return 'bg-gray-100 text-gray-600';
  }
};

const formatTanggal = (tanggal) => {
  if (!tanggal) return '-';
  return new Date(tanggal).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  });
};

const firstError = (value) => (Array.isArray(value) ? value[0] : value);

let searchTimeout = null;
watch(search, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => fetchStudents(), 400);
});

onMounted(() => {
  fetchStudents();
});
</script>

