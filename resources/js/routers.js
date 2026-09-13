import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        name: 'router-dashboard',
        component: () => import('@/pages/pageDashboard.vue'),
        meta: { title: 'Bảng điều khiển' },
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
