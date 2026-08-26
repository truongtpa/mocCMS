<template>
    <div class="wrapper">
        <LTENavbar />
        <LTESidebar />
        <router-view :key="reloadKey"></router-view>
        <LTEFooter />
    </div>
</template>

<script>
import LTESidebar from '@/components/themes/LTESidebar.vue';
import LTEFooter from '@/components/themes/LTEFooter.vue';
import LTENavbar from '@/components/themes/LTENavbar.vue';

export default {
    name: 'App',
    components: {
        LTENavbar,
        LTESidebar,
        LTEFooter
    },
    data() {
        return {
            reloadKey: 0
        }
    },
    methods: {
        reloadPage() {
            this.reloadKey += 1;
        }
    }
}
</script>

<style>
</style>
