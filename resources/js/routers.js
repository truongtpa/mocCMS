import {createRouter, createWebHistory} from "vue-router";

const routes = [
    {
        path: "/",
        children: [
            {
                path: "home",
                name: "router-home",
                component: () => import("@/pages/pageHome.vue"),
                meta: {title: 'Hệ thống quản lý văn bản'}
            },
            {
                path: "quyen",
                name: "router-adminquyen",
                component: () => import("@/pages/pageQuyen.vue"),
                meta: {title: 'Nhóm quyền'}
            },
            {
                path: "nhom-quyen",
                name: "router-adminquyennhom",
                component: () => import("@/pages/pageQuyenNhom.vue"),
                meta: {title: 'Nhóm quyền tài khoản'}
            },
            {
                path: "nhom-quyen/:id(\\d+)",
                name: "router-adminquyennhomct",
                component: () => import("@/pages/pageQuyenNhomCT.vue"),
                meta: {title: 'Quyền của nhóm'}
            },
        ]
    },
    {
        path: "/",
        redirect: '/'
    }
]

const router = createRouter({
    history: createWebHistory(''),
    routes,
});

router.beforeEach((to, from, next) => {
    document.title = buildTitle(to)
    next()
})

function buildTitle(route) {
    let title = route.meta.title || 'Hệ thống quản lý văn bản';
    if (route.matched.length > 1) {
        const parent = route.matched[0].meta.title;
        title = `${title}`;
    }
    return title;
}

export default router;
