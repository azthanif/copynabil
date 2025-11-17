// resources/js/router.js (CONTOH)

import { createRouter, createWebHistory } from 'vue-router';
import LandingPage from './Pages/LandingPage.vue';
import LoginPage from './Pages/LoginPage.vue'; 
import AuthenticatedLayout from './layouts/AuthenticatedLayout.vue';
import DashboardOrangTua from './Pages/DashboardOrangTua.vue';
import JadwalPage from './Pages/JadwalPage.vue';
import CatatanGuruPage from './Pages/CatatanGuruPage.vue';
import KelolaSiswaPage from './Pages/Admin/KelolaSiswaPage.vue';

const routes = [
    {
        path: '/',
        name: 'landing',
        component: LandingPage 
    },
    {
        path: '/login',
        name: 'login',
        component: LoginPage,
    },
    {
        path: '/dashboard',
        component: AuthenticatedLayout, // Gunakan Layout sebagai induk
        meta: { requiresAuth: true },
        children: [
            // Halaman-halaman ini akan dimuat di dalam <router-view> AuthenticatedLayout
            {
                path: '', // URL /dashboard
                name: 'dashboard',
                component: DashboardOrangTua 
            },
            {
                path: '/dashboard-jadwal', // URL /dashboard-jadwal
                name: 'dashboard.jadwal',
                component: JadwalPage 
            },
            {
                path: '/dashboard-catatan', // URL /dashboard-catatan
                name: 'dashboard.catatan',
                component: CatatanGuruPage
            }
        ]
    },
    {
        path: '/admin',
        component: AuthenticatedLayout,
        meta: { requiresAuth: true, requiresAdmin: true },
        children: [
            {
                path: 'siswa',
                name: 'admin.siswa',
                component: KelolaSiswaPage
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
router.beforeEach((to, from, next) => {
    // Cek apakah halaman yang dituju butuh login (memiliki meta requiresAuth)
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // Cek apakah punya token
        const token = localStorage.getItem('token');

        // Jika tidak punya token, tendang ke halaman login
        if (!token) {
            next({ name: 'login' });
        } else {
            const needsAdmin = to.matched.some(record => record.meta.requiresAdmin);
            if (needsAdmin) {
                const role = localStorage.getItem('userRole');
                if (role !== 'Admin') {
                    next({ name: 'dashboard' });
                    return;
                }
            }

            // Jika punya token dan peran sesuai, silakan lanjut
            next();
        }
    } else {
        // Jika halaman tidak butuh login (misal: landing page), silakan lewat
        next();
    }
});
export default router;