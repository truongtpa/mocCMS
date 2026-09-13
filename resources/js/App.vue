<template>
    <LteDashboardLayout
        :menu-items="menu"
        :current-path="$route.path"
        :link-component="RouterLink"
        :user="user"
        logo="/favicon.ico"
        brand-text="mocCMS"
        footer-right-text=""
    >
        <router-view />

        <template #user-body></template>
        <template #user-footer></template>
        <template #footer>
            <strong>mocCMS</strong>
        </template>
    </LteDashboardLayout>
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
            ],
        }
    }
}
</script>
