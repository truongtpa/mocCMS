<template>
    <LTEContentWrapper>
        <template #content>
            <!-- Loading State -->
            <div v-if="loading" class="text-center my-5">
                <LoadingSpinner />
                <p class="mt-2 text-muted">Đang tải bảng điều khiển...</p>
            </div>

            <div v-else>


                <!-- ========================================== -->
                <!-- STUDENT PORTAL DASHBOARD -->
                <!-- ========================================== -->
                <div v-if="userType === 'sinh_vien'">
                    <!-- Info Blocks -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="info-box shadow-sm border-0 bg-white hover-up">
                                <span class="info-box-icon bg-success text-white"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small uppercase">Giải thưởng & Thành tích</span>
                                    <span class="info-box-number h3 font-weight-bold text-dark mb-0">{{ stats.achievements }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="info-box shadow-sm border-0 bg-white hover-up">
                                <span class="info-box-icon bg-info text-white"><i class="fas fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small uppercase">Lịch hẹn giảng viên</span>
                                    <span class="info-box-number h3 font-weight-bold text-dark mb-0">{{ stats.bookings }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="info-box shadow-sm border-0 bg-white hover-up">
                                <span class="info-box-icon bg-warning text-white"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small uppercase">Yêu cầu chờ duyệt</span>
                                    <span class="info-box-number h3 font-weight-bold text-dark mb-0">{{ stats.pending }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main grid layout -->
                    <div class="row">
                        <!-- Left Column: Recent bookings -->
                        <div class="col-lg-8 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom py-3">
                                    <div class="d-flex align-items-center justify-content-between w-100">
                                        <h5 class="font-weight-bold mb-0 text-dark" style="font-size: 1.1rem;">
                                            <i class="fas fa-calendar-alt text-primary mr-2"></i> Lịch hẹn gần đây
                                        </h5>
                                        <router-link :to="{ name: 'router-portal-dat-lich' }" class="btn btn-sm btn-primary px-3 rounded-pill font-weight-bold text-white shadow-sm" style="color: #ffffff !important;">
                                            Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                                        </router-link>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div v-if="bookings.length === 0" class="text-center py-5">
                                        <i class="far fa-calendar-alt fa-3x text-muted mb-2"></i>
                                        <p class="text-muted">Bạn chưa đăng ký lịch hẹn nào.</p>
                                    </div>
                                    <div v-else class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th class="border-top-0">Giảng viên</th>
                                                    <th class="border-top-0">Thời gian hẹn</th>
                                                    <th class="border-top-0">Nội dung</th>
                                                    <th class="border-top-0 text-center">Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="item in bookings" :key="item.id">
                                                    <td class="font-weight-bold">{{ item.ten_giang_vien }}</td>
                                                    <td>{{ formatDatetime(item.thoi_gian_bat_dau) }}</td>
                                                    <td class="text-truncate" style="max-width: 250px;">{{ item.noi_dung }}</td>
                                                    <td class="text-center">
                                                        <span class="badge" :class="getStatusClass(item.trang_thai)">
                                                            {{ getStatusLabel(item.trang_thai) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Quick Links & Actions -->
                        <div class="col-lg-4 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom-0 py-3">
                                    <h5 class="card-title font-weight-bold mb-0 text-dark">Tiện ích nhanh</h5>
                                </div>
                                <div class="card-body">
                                    <router-link 
                                        :to="{ name: 'router-portal-ho-so' }" 
                                        class="btn btn-block btn-outline-primary text-left py-3 px-3 mb-3 d-flex align-items-center justify-content-between rounded-lg hover-shadow"
                                    >
                                        <div>
                                            <h6 class="font-weight-bold mb-1"><i class="fas fa-user-cog mr-2"></i> Thông tin cá nhân</h6>
                                            <small class="text-muted">Xem và cập nhật hồ sơ cá nhân EAV</small>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </router-link>

                                    <router-link 
                                        :to="{ name: 'router-portal-thanh-tich' }" 
                                        class="btn btn-block btn-outline-success text-left py-3 px-3 mb-3 d-flex align-items-center justify-content-between rounded-lg hover-shadow"
                                    >
                                        <div>
                                            <h6 class="font-weight-bold mb-1"><i class="fas fa-award mr-2"></i> Khai báo giải thưởng</h6>
                                            <small class="text-muted">Đăng ký thành tích & giải thưởng đạt được</small>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </router-link>

                                    <router-link 
                                        :to="{ name: 'router-portal-dat-lich' }" 
                                        class="btn btn-block btn-outline-info text-left py-3 px-3 d-flex align-items-center justify-content-between rounded-lg hover-shadow"
                                    >
                                        <div>
                                            <h6 class="font-weight-bold mb-1 text-info"><i class="fas fa-calendar-plus mr-2 text-info"></i> Đặt lịch hẹn giảng viên</h6>
                                            <small class="text-muted">Đăng ký lịch tư vấn hoặc trao đổi bài học</small>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- LECTURER / ADMIN DASHBOARD -->
                <!-- ========================================== -->
                <div v-else>
                    <!-- Info Blocks -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="info-box shadow-sm border-0 bg-white hover-up">
                                <span class="info-box-icon bg-primary text-white"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small uppercase">Tổng số sinh viên</span>
                                    <span class="info-box-number h3 font-weight-bold text-dark mb-0">{{ stats.students }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="info-box shadow-sm border-0 bg-white hover-up">
                                <span class="info-box-icon bg-warning text-white"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small uppercase">Yêu cầu hẹn mới</span>
                                    <span class="info-box-number h3 font-weight-bold text-dark mb-0">{{ stats.pending }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="info-box shadow-sm border-0 bg-white hover-up">
                                <span class="info-box-icon bg-success text-white"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small uppercase">Lịch đã xác nhận</span>
                                    <span class="info-box-number h3 font-weight-bold text-dark mb-0">{{ stats.confirmed }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Management -->
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom-0 py-3">
                                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                                        <i class="fas fa-tasks mr-2 text-warning"></i>
                                        Yêu cầu đặt lịch hẹn từ Sinh viên
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div v-if="bookings.length === 0" class="text-center py-5">
                                        <i class="far fa-check-square fa-3x text-success mb-2"></i>
                                        <p class="text-muted mb-0">Không có yêu cầu đặt lịch hẹn nào đang chờ xử lý.</p>
                                    </div>
                                    <div v-else class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th>Mã sinh viên</th>
                                                    <th>Họ và tên</th>
                                                    <th>Thời gian đăng ký</th>
                                                    <th>Nội dung câu hỏi / trao đổi</th>
                                                    <th class="text-center" style="width: 250px;">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="item in bookings" :key="item.id">
                                                    <td>{{ item.mssv }}</td>
                                                    <td class="font-weight-bold">{{ item.ten_sinh_vien }}</td>
                                                    <td>{{ formatDatetime(item.thoi_gian_bat_dau) }}</td>
                                                    <td>{{ item.noi_dung }}</td>
                                                    <td class="text-center">
                                                        <div v-if="item.trang_thai === 'cho_duyet'">
                                                            <button 
                                                                class="btn btn-sm btn-success mr-2 shadow-sm"
                                                                @click="updateStatus(item.id, 'da_xac_nhan')"
                                                            >
                                                                <i class="fas fa-check mr-1"></i> Đồng ý
                                                            </button>
                                                            <button 
                                                                class="btn btn-sm btn-danger shadow-sm"
                                                                @click="updateStatus(item.id, 'tu_choi')"
                                                            >
                                                                <i class="fas fa-times mr-1"></i> Từ chối
                                                            </button>
                                                        </div>
                                                        <span v-else class="badge" :class="getStatusClass(item.trang_thai)">
                                                            {{ getStatusLabel(item.trang_thai) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </LTEContentWrapper>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';

const authStore = useAuthStore();
const loading = ref(true);
const userType = ref('sinh_vien');
const stats = ref({});
const bookings = ref([]);

// Fetch dashboard stats & details
const loadStats = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('StudentPortalController.getDashboardStats'));
        if (response.data.status === 200) {
            userType.value = response.data.data.user_type;
            stats.value = response.data.data.stats;
            bookings.value = response.data.data.bookings;
        }
    } catch (err) {
        console.error('Error loading dashboard stats:', err);
    } finally {
        loading.value = false;
    }
};

// Approve/Reject Lecturer Booking status
const updateStatus = async (id, status) => {
    try {
        const response = await axios.post(route('StudentPortalController.updateBookingStatus'), {
            id: id,
            trang_thai: status
        });
        if (response.data.status === 200) {
            if (window.func && window.func.toastSuccess) {
                window.func.toastSuccess('Cập nhật lịch hẹn thành công!');
            }
            loadStats();
        }
    } catch (err) {
        console.error('Error updating booking status:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError('Không thể cập nhật lịch hẹn');
        }
    }
};

// Formatting helpers
const formatDatetime = (dtStr) => {
    if (!dtStr) return '';
    const date = new Date(dtStr);
    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'da_xac_nhan': return 'Đã đồng ý';
        case 'tu_choi': return 'Đã từ chối';
        default: return 'Chờ duyệt';
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case 'da_xac_nhan': return 'badge-success';
        case 'tu_choi': return 'badge-danger';
        default: return 'badge-warning text-dark';
    }
};

onMounted(() => {
    loadStats();
});
</script>

<style scoped>
.hover-up {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.hover-up:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1) !important;
}

.hover-shadow {
    transition: all 0.2s ease-in-out;
}
.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    text-decoration: none;
}

.opacity-5 {
    opacity: 0.15;
}

.info-box {
    display: flex;
    border-radius: 8px;
    background: #fff;
    min-height: 90px;
    padding: 15px;
    align-items: center;
}

.info-box-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    width: 60px;
    height: 60px;
    font-size: 24px;
}

.info-box-content {
    padding-left: 15px;
    flex: 1;
}

.uppercase {
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
