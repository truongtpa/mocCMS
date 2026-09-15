<template>
    <AppLayout
        :menu-items="menu"
        :current-path="$route.path"
        :link-component="RouterLink"
        :user="user"
        brand-icon="bi-tree-fill"
        brand-text="Mộc CMS"
        @logout="dangXuat"
    >
        <router-view />

        <template #footer>
            <strong>Mộc CMS - Hệ thống quản trị nội dung</strong>
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
                role: 'Quản trị viên',
            },
            menu: [
                {
                    type: 'header',
                    text: 'Tổng quan',
                },
                {
                    type: 'item',
                    text: 'Bảng điều khiển',
                    href: '/',
                    icon: 'bi-speedometer',
                },
                {
                    type: 'header',
                    text: 'Nội dung',
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
