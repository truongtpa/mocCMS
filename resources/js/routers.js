import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        name: 'router-dashboard',
        component: () => import('@/pages/pageDashboard.vue'),
        meta: { title: 'Bảng điều khiển' },
    },
    {
        path: '/bai-viet',
        name: 'router-baiviet',
        component: () => import('@/pages/pageBaiViet.vue'),
        meta: { title: 'Bài viết' },
    },
    {
        path: '/quyen',
        name: 'router-quyen',
        component: () => import('@/pages/pageQuyen.vue'),
        meta: { title: 'Nhóm quyền' },
    },
    {
        path: '/quyen-nhom',
        name: 'router-quyennhom',
        component: () => import('@/pages/pageQuyenNhom.vue'),
        meta: { title: 'Nhóm quyền tài khoản' },
    },
    {
        path: '/quyen-nhom/:id(\\d+)',
        name: 'router-quyennhomct',
        component: () => import('@/pages/pageQuyenNhomCT.vue'),
        meta: { title: 'Chi tiết nhóm quyền' },
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/',
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.afterEach((to) => {
    document.title = to.meta.title ? `${to.meta.title} - mocCMS` : 'mocCMS'
})

export default router
