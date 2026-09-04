import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth'

const routes = [
    {
        path: '/ho-so',
        children: [
            {
                path: '',
                name: 'router-portal-ho-so',
                meta: { title: 'Thông tin cá nhân', permission: 'StudentPortalController.getProfileData' },
                component: () => import('@/pages/Profile.vue')
            },
            {
                path: 'bang-dieu-khien',
                name: 'router-admin',
                meta: { title: 'Bảng điều khiển', permission: 'StudentPortalController.getDashboardStats' },
                component: () => import('@/pages/Dashboard.vue')
            },
            {
                path: 'thanh-tich',
                name: 'router-portal-thanh-tich',
                meta: { title: 'Giải thưởng & Thành tích', permission: 'StudentPortalController.getAchievementsList' },
                component: () => import('@/pages/Achievements.vue')
            },
            {
                path: 'dat-lich',
                name: 'router-portal-dat-lich',
                meta: { title: 'Đặt lịch Giảng viên', permission: 'StudentPortalController.getBookingsList' },
                component: () => import('@/pages/Booking.vue')
            },
            {
                path: 'doi-tuong-dong',
                name: 'router-portal-doi-tuong-dong',
                meta: { title: 'Quản lý Đối tượng & Thuộc tính', permission: 'DynamicObjectController.getTypes' },
                component: () => import('@/pages/DynamicObjects.vue')
            },
            {
                path: 'doi-tuong-dong/import',
                name: 'router-portal-doi-tuong-dong-import',
                meta: { title: 'Import Dữ liệu Đối tượng', permission: 'DynamicObjectController.saveRecord' },
                component: () => import('@/pages/DynamicObjectImport.vue')
            },
            {
                path: 'phan-quyen',
                name: 'router-portal-phan-quyen',
                meta: { title: 'Quản lý Phân quyền (RBAC)', permission: 'PhanQuyenController.getMaTranQuyen' },
                component: () => import('@/pages/Permissions.vue')
            },
            {
                path: 'cai-dat',
                name: 'router-portal-cai-dat',
                meta: { title: 'Cài đặt hệ thống', permission: 'PhanQuyenController.getCaiDat' },
                component: () => import('@/pages/CaiDat.vue')
            },
            {
                path: 'nhat-ky',
                name: 'router-portal-nhat-ky',
                meta: { title: 'Nhật ký & Sao lưu hệ thống', permission: 'DynamicObjectController.getDsNhatKy' },
                component: () => import('@/pages/NhatKyVaSaoLuu.vue')
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(''),
    routes,
})

export function buildTitle(route) {
    return route.meta.title || ''
}

router.beforeEach(async (to, from, next) => {
    document.title = buildTitle(to) ? `${buildTitle(to)} - Hệ thống quản lý Đào tạo VLUTE` : 'Hệ thống quản lý Đào tạo VLUTE'

    const authStore = useAuthStore()
    authStore.setRouterContext({
        name: to.name,
        path: to.fullPath,
    })

    // Fetch user permissions details dynamically
    await fetchInitialAuthData()
    authStore.setRouterName(to.name)

    // Check route permission
    const requiredPermission = to.meta.permission
    if (requiredPermission && !authStore.hasPermission(requiredPermission)) {
        if (window.func && window.func.toastError) {
            window.func.toastError('Bạn không có quyền truy cập trang này!')
        }
        if (to.name !== 'router-admin') {
            return next({ name: 'router-admin' })
        }
    }

    next()
})

let initialAuthFetched = false

async function fetchInitialAuthData() {
    if (initialAuthFetched) {
        return
    }
    try {
        const response = await axios.get(route('TaiKhoanController.taiKhoanChiTiet'))
        if (response.data.status === 200) {
            const rData = response.data.data
            useAuthStore().setUser(rData.info, rData.ds_quyen)
            initialAuthFetched = true
        } else {
            console.error('Failed to load authorization data')
        }
    } catch (err) {
        console.error('Error fetching auth data:', err)
    }
}

export default router
