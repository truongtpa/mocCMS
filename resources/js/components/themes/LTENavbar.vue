<template>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Home</a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">

            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown" role="button">
                    <span class="avatar-tron avatar-sm">{{ chuCaiDau }}</span>
                    <span class="d-none d-sm-inline ml-2">{{ __tttk.ho_ten }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <li class="user-header bg-primary">
                        <span class="avatar-tron avatar-lg mb-2">{{ chuCaiDau }}</span>
                        <p class="mb-0">
                            <b>{{ __tttk.ho_ten }}</b>
                            <small v-if="__tttk.ten_don_vi" class="d-block text-truncate">
                                <i class="fas fa-building mr-1"></i>{{ __tttk.ten_don_vi }}
                            </small>
                            <small v-if="__tttk.email" class="d-block text-truncate">
                                <i class="fas fa-envelope mr-1"></i>{{ __tttk.email }}
                            </small>
                        </p>
                    </li>
                    <li class="user-footer d-flex justify-content-between">
                        <a class="btn btn-sm btn-default btn-flat" :href="route('changePassword')">
                            <i class="fas fa-key mr-1"></i>Đổi mật khẩu
                        </a>
                        <a href="#" class="btn btn-sm btn-danger btn-flat" @click.prevent="dangXuat">
                            <i class="fas fa-sign-out-alt mr-1"></i>Đăng xuất
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
/* Avatar chữ cái đầu, dùng thay ảnh đại diện */
.avatar-tron {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 600;
    line-height: 1;
    flex-shrink: 0;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 13px;
    background-color: #e9ecef;
    color: #495057;
}

.avatar-lg {
    width: 64px;
    height: 64px;
    font-size: 24px;
    background-color: rgba(255, 255, 255, .25);
    color: #fff;
}

/* Đủ rộng để hai nút ở đáy nằm cùng một hàng */
.user-menu .dropdown-menu {
    min-width: 270px;
}

.user-menu .dropdown-menu > .user-footer .btn {
    white-space: nowrap;
}

/* Bỏ chiều cao cố định để header co theo nội dung */
.user-menu .dropdown-menu > .user-header {
    height: auto;
    padding: 15px;
}

.user-menu .dropdown-menu > .user-header p {
    margin-top: 0;
}

.user-menu .dropdown-menu > .user-header small {
    font-size: 12px;
    opacity: .85;
}
</style>

<script>

export default {
    data() {
        return {
            __tttk: __tttk
        };
    },
    computed: {
        chuCaiDau() {
            const tu = (this.__tttk?.ho_ten || '').trim().split(/\s+/).filter(Boolean);
            if (!tu.length) {
                return '?';
            }

            return tu[tu.length - 1].charAt(0).toUpperCase();
        },
    },
    methods: {
        async dangXuat() {
            localStorage.removeItem('quyen');
            window.location.href = route('dangXuat');
        },
    },



};
</script>
