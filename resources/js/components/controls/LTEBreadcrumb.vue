<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li v-for="(crumb, index) in breadcrumbs" :key="index" class="breadcrumb-item" :class="{ active: !crumb.route }">
                <router-link v-if="crumb.route" :to="crumb.route">
                    {{ crumb.label }}
                </router-link>
                <span v-else>{{ crumb.label }}</span>
            </li>
        </ol>
    </nav>
</template>

<script>
export default {
    props: {
        level1: String,
        level2: String,
        level3: String,
        level4: String,
    },
    data() {
        return {
        };
    },
    watch: {
    },
    computed: {
        breadcrumbs() {
            const routeName = this.$route.name;
            const breadcrumbs = [];

            switch (routeName) {
                case 'router-chuong-trinh-dt':
                    breadcrumbs.push({ label: 'Niên khóa' });
                    break;

                case 'router-nganh-dt':
                    breadcrumbs.push({ label: this.level1, route: { name: 'router-chuong-trinh-dt' } });
                    breadcrumbs.push({ label: 'Ngành đào tạo' });
                    break;

                case 'router-nganh-dt-chuyen-nganh':
                    breadcrumbs.push({ label: this.level1, route: { name: 'router-chuong-trinh-dt' } });
                    breadcrumbs.push({ label: this.level2, route: { name: 'router-nganh-dt' } });
                    breadcrumbs.push({ label: 'Chuyên ngành đào tạo' });
                    break;

                case 'router-chuong-trinh-dt-hoc-phan':
                case 'router-chuong-trinh-dt-thong-ke':
                    breadcrumbs.push({ label: this.level1, route: { name: 'router-chuong-trinh-dt' } });
                    breadcrumbs.push({ label: this.level2, route: { name: 'router-nganh-dt' } });
                    breadcrumbs.push({ label: this.level3, route: { name: 'router-nganh-dt-chuyen-nganh' } });
                    breadcrumbs.push({ label: 'Chương trình đào tạo chi tiết' });
                    break;
            }

            return breadcrumbs;
        },
    },
    methods: {

    },
};
</script>

<style scoped>
.breadcrumb {
    background: transparent;
    padding: 0px 15px 8px 15px;
    margin-bottom: 0px;
}

.breadcrumb-item{
    padding-left: 0px;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}
.breadcrumb-item.active {
    color: #6c757d;
    padding-left: 0px;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #6c757d; /* Màu xám nhẹ */
    padding: 0 8px;
}


</style>
