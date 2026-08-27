<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <img
                :src="`/asset/admin/themes/images/logo.png`"
                class="brand-image img-circle elevation-3"
                style="opacity: 0.8"
            />
            <span class="brand-text font-weight-light"> QUẢN LÝ ĐÀO TẠO </span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul
                    class="nav nav-pills nav-sidebar flex-column"
                    data-widget="treeview"
                    role="menu"
                    data-accordion="true"
                >
                    <li class="nav-header">CHỨC NĂNG</li>
                    <li v-if="checkPermission('router-portal-ho-so')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-ho-so' }" class="nav-link" @click="closeSidebar()">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>Thông tin cá nhân</p>
                        </router-link>
                    </li>
                    <li v-if="checkPermission('router-portal-thanh-tich')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-thanh-tich' }" class="nav-link" @click="closeSidebar()">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Giải thưởng & Thành tích</p>
                        </router-link>
                    </li>
                    <li v-if="checkPermission('router-portal-dat-lich')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-dat-lich' }" class="nav-link" @click="closeSidebar()">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Đặt lịch Giảng viên</p>
                        </router-link>
                    </li>
                    <!-- <li class="nav-item">
            <a href="#/s" class="nav-link">
              <i class=" nav-icon fas fa-search" aria-hidden="true"></i>
              <p>Tìm kiếm<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class=" nav nav-treeview">
              <li class="nav-item ">
                <router-link :to="{ name: 'router-tim-kiem-sv' }" class="nav-link nav-link-custom">
                  <i class="nav-icon fas fa-circle-notch"></i>
                  <p class="nav-item-text">Sinh viên</p>
                </router-link>
              </li>
            </ul>
          </li> -->
                    <li v-if="checkPermission('router-tim-kiem-sv')" class="nav-item">
                        <router-link :to="{ name: 'router-tim-kiem-sv' }" class="nav-link" @click="closeSidebar()">
                            <i class="nav-icon fas fa-search"></i>
                            <p>Tìm kiếm sinh viên</p>
                        </router-link>
                    </li>
                    <li v-if="checkPermission('router-danh-sach-chuc-nang')" class="nav-item">
                        <router-link
                            :to="{ name: 'router-danh-sach-chuc-nang' }"
                            class="nav-link"
                            @click="closeSidebar()"
                        >
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Quản lý thông tin</p>
                        </router-link>
                    </li>
                    <li v-if="checkPermission('router-chuong-trinh-dt')" class="nav-item">
                        <router-link :to="{ name: 'router-chuong-trinh-dt' }" class="nav-link" @click="closeSidebar()">
                            <i class="nav-icon fa fa-book"></i>
                            <p>Chương trình đào tạo</p>
                        </router-link>
                    </li>
                    <li v-if="checkPermission('router-hknh')" class="nav-item">
                        <router-link :to="{ name: 'router-hknh' }" class="nav-link" @click="closeSidebar()">
                            <i class="nav-icon fa fa-graduation-cap" aria-hidden="true"></i>
                            <p>Quản lý học kỳ</p>
                        </router-link>
                    </li>

                    <li v-if="checkPermission('router-van-bang-chung-chi-dot')" class="nav-item">
                        <router-link
                            :to="{ name: 'router-van-bang-chung-chi-dot' }"
                            class="nav-link"
                            @click="closeSidebar()"
                        >
                            <i class="nav-icon fa fa-user-graduate" aria-hidden="true"></i>
                            <p>Văn bằng chứng chỉ</p>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <router-link
                            :to="{ name: 'router-template-xuat-bao-cao' }"
                            class="nav-link"
                            @click="closeSidebar()"
                        >
                            <i class="nav-icon fa fa-chart-pie" aria-hidden="true"></i>
                            <p>Báo cáo Thống kê</p>
                        </router-link>
                    </li>
                    <li class="nav-header">KHÁC</li>
                    <li v-if="checkPermission('router-quyen')" class="nav-item">
                        <router-link :to="{ name: 'router-quyen' }" class="nav-link" @click="closeSidebar()">
                            <i class="nav-icon fas fa-user-lock"></i>
                            <p>Phân quyền</p>
                        </router-link>
                    </li>
                    <li v-if="checkPermission('router-tien-trinh-nen')" class="nav-item">
                        <router-link :to="{ name: 'router-tien-trinh-nen' }" class="nav-link" @click="closeSidebar()">
                            <i class="nav-icon fas fa-layer-group" aria-hidden="true"></i>
                            <p>Tiến trình</p>
                        </router-link>
                    </li>
                    <li v-if="checkPermission('router-bao-cao-thong-ke')" class="nav-item">
                        <a href="https://nb.daotao.vlute.edu.vn/" class="nav-link" target="_blank">
                            <i class="nav-icon fa fa-brain" aria-hidden="true"></i>
                            <p>Notebook</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="http://helpdesk.vlute.edu.vn/" class="nav-link" target="_blank">
                            <i class="nav-icon fa fa-question" aria-hidden="true"></i>
                            <p>Hướng dẫn</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<style scoped>
.sidebar {
    min-height: 100vh !important;
    height: auto !important;
    overflow-y: auto;
}
</style>

<script>
import { useAuthStore } from '@/store/auth'

export default {
    data() {
        return {
            authStore: useAuthStore(),
        }
    },

    methods: {
        closeSidebar() {
            const pushMenu = document.querySelector('[data-widget="pushmenu"]')
            if (pushMenu && document.body.classList.contains('sidebar-open')) {
                pushMenu.click()
            }
        },
        checkPermission(routeName) {
            return this.authStore.permissions.some((p) => p.route === routeName && p.is_read === true)
        },
        handleClickOutside(event) {
            const sidebar = document.querySelector('.main-sidebar')
            const toggleButton = document.querySelector('[data-widget="pushmenu"]')
            if (sidebar && !sidebar.contains(event.target) && toggleButton && !toggleButton.contains(event.target)) {
                this.closeSidebar()
            }
        },
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside)
        // console.log(this.authStore.permissions);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside)
    },
    computed: {
        quyen() {
            return this.authStore.permissions.find((p) => p.url === this.$route.path) || {}
        },
    },
}
</script>
