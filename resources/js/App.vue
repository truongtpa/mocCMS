<template>
    <div class="wrapper">
        <LTENavbar />
        <LTESidebar />

        <Suspense>
            <template #default>
                <router-view v-slot="{ Component, route }">
                    <keep-alive v-if="route.meta.keepAlive">
                        <component :is="Component" />
                    </keep-alive>
                    <component v-else :is="Component" />
                </router-view>
            </template>
            <template #fallback> </template>
        </Suspense>

        <LTEFooter />
    </div>

    <!-- Global Toast Container -->
    <Toast />
</template>

<script>
import LTEFooter from '@/components/themes/LTEFooter.vue';
import LTENavbar from '@/components/themes/LTENavbar.vue';
import LTESidebar from '@/components/themes/LTESidebar.vue';

export default {
    name: 'App',
    components: {
        LTENavbar,
        LTESidebar,
        LTEFooter,
    }
};
</script>

<style>
@import '@/assets/css/style.css';
</style>
