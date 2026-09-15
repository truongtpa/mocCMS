<template>
    <AppLayout
        :menu-items="menu"
        :current-path="$route.path"
        :link-component="RouterLink"
        :user="user"
        logo="/favicon.ico"
        brand-text="mocCMS"
        floating
        sidebar-theme="auto"
        @logout="dangXuat"
    >
        <router-view />

        <template #footer>
            <strong>mocCMS</strong>
        </template>
    </AppLayout>
</template>

<script>
import { RouterLink } from 'vue-router'

export default {
    name: 'App',
    data() {
        return {
            RouterLink,
            user: {
                name: 'mocCMS',
                image: '/favicon.ico',
            },
            menu: [
                {
                    type: 'item',
                    text: 'Bảng điều khiển',
                    href: '/',
                    icon: 'bi-speedometer',
                },
                {
                    type: 'item',
                    text: 'Bài viết',
                    href: '/bai-viet',
                    icon: 'bi-newspaper',
                },
            ],
        }
    },
    methods: {
        dangXuat() {
            // TODO: nối vào route đăng xuất khi có phần xác thực
        },
    },
}
</script>
