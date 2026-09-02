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
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon icon-emerald"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small font-weight-bold uppercase">Giải thưởng & Thành tích</span>
                                    <div class="info-box-number h3 font-weight-extrabold text-dark mb-0">{{ stats.achievements }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon icon-blue"><i class="fas fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small font-weight-bold uppercase">Lịch hẹn giảng viên</span>
                                    <div class="info-box-number h3 font-weight-extrabold text-dark mb-0">{{ stats.bookings }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon icon-amber"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small font-weight-bold uppercase">Yêu cầu chờ duyệt</span>
                                    <div class="info-box-number h3 font-weight-extrabold text-dark mb-0">{{ stats.pending }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main grid layout -->
                    <div class="row">
                        <!-- Left Column: Recent bookings -->
                        <div class="col-lg-8 mb-4">
                            <div class="card border-0 rounded-xl overflow-hidden h-100" style="border: 1px solid #e2e8f0 !important; border-radius: 12px !important; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04) !important;">
                                <div class="card-header bg-white border-bottom py-2.5 px-3">
                                    <div class="d-flex align-items-center justify-content-between w-100">
                                        <h6 class="font-weight-bold mb-0 text-dark text-xs uppercase tracking-wider d-flex align-items-center">
                                            <i class="fas fa-calendar-alt text-primary mr-2"></i> LỊCH HẸN GẦN ĐÂY
                                        </h6>
                                        <router-link :to="{ name: 'router-portal-dat-lich' }" class="btn btn-xs btn-primary px-2.5 rounded font-weight-bold text-white shadow-sm" style="color: #ffffff !important;">
                                            Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                                        </router-link>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div v-if="bookings.length === 0" class="text-center py-4 px-3">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 48px; height: 48px; background-color: #f1f5f9;">
                                            <i class="fas fa-calendar-alt text-primary" style="font-size: 1.25rem;"></i>
                                        </div>
                                        <p class="text-muted text-xs font-weight-medium mb-0">Bạn chưa đăng ký lịch hẹn nào.</p>
                                    </div>
                                    <AppTable
                                        v-else
                                        :columns="[
                                            { key: 'ten_giang_vien', label: 'Giảng viên' },
                                            { key: 'thoi_gian_bat_dau', label: 'Thời gian hẹn' },
                                            { key: 'noi_dung', label: 'Nội dung' },
                                            { key: 'trang_thai', label: 'Trạng thái', align: 'center' }
                                        ]"
                                        :items="bookings"
                                        empty-text="Bạn chưa đăng ký lịch hẹn nào."
                                    >
                                        <template #col-ten_giang_vien="{ item }">
                                            <span class="font-weight-bold text-dark">{{ item.ten_giang_vien }}</span>
                                        </template>
                                        <template #col-thoi_gian_bat_dau="{ item }">
                                            <span>{{ formatDatetime(item.thoi_gian_bat_dau) }}</span>
                                        </template>
                                        <template #col-noi_dung="{ item }">
                                            <div class="text-truncate" style="max-width: 250px;">{{ item.noi_dung }}</div>
                                        </template>
                                        <template #col-trang_thai="{ item }">
                                            <AppBadge :variant="getStatusVariant(item.trang_thai)">
                                                {{ getStatusLabel(item.trang_thai) }}
                                            </AppBadge>
                                        </template>
                                    </AppTable>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Quick Links & Actions -->
                        <div class="col-lg-4 mb-4">
                            <div class="card border-0 rounded-xl overflow-hidden h-100" style="border: 1px solid #e2e8f0 !important; border-radius: 12px !important; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04) !important;">
                                <div class="card-header bg-white border-bottom py-2.5 px-3">
                                    <h6 class="font-weight-bold mb-0 text-dark text-xs uppercase tracking-wider d-flex align-items-center">
                                        <i class="fas fa-bolt text-warning mr-2"></i> THAO TÁC NHANH
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <router-link :to="{ name: 'router-portal-dat-lich' }" class="btn btn-outline-primary btn-block text-left mb-2 py-2 px-3 d-flex align-items-center justify-content-between font-weight-bold" style="border-radius: 8px;">
                                        <span><i class="fas fa-calendar-plus mr-2"></i> Đặt lịch hẹn mới</span>
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </router-link>
                                    <router-link :to="{ name: 'router-portal-thanh-tich' }" class="btn btn-outline-success btn-block text-left mb-2 py-2 px-3 d-flex align-items-center justify-content-between font-weight-bold" style="border-radius: 8px;">
                                        <span><i class="fas fa-award mr-2"></i> Khai báo thành tích</span>
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </router-link>
                                    <router-link :to="{ name: 'router-portal-ho-so' }" class="btn btn-outline-secondary btn-block text-left py-2 px-3 d-flex align-items-center justify-content-between font-weight-bold" style="border-radius: 8px;">
                                        <span><i class="fas fa-user-edit mr-2"></i> Cập nhật thông tin</span>
                                        <i class="fas fa-chevron-right text-xs"></i>
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
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon icon-blue"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small font-weight-bold uppercase">Tổng số sinh viên</span>
                                    <div class="info-box-number h3 font-weight-extrabold text-dark mb-0">{{ stats.students }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon icon-amber"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small font-weight-bold uppercase">Yêu cầu hẹn mới</span>
                                    <div class="info-box-number h3 font-weight-extrabold text-dark mb-0">{{ stats.pending }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <span class="info-box-icon icon-emerald"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted small font-weight-bold uppercase">Lịch đã xác nhận</span>
                                    <div class="info-box-number h3 font-weight-extrabold text-dark mb-0">{{ stats.confirmed }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Management -->
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card border-0 rounded-xl overflow-hidden" style="border: 1px solid #e2e8f0 !important; border-radius: 12px !important; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04) !important;">
                                <div class="card-header bg-white border-bottom py-2.5 px-3">
                                    <h6 class="font-weight-bold mb-0 text-dark text-xs uppercase tracking-wider d-flex align-items-center">
                                        <i class="fas fa-tasks mr-2 text-primary"></i> YÊU CẦU ĐẶT LỊCH HẸN TỪ SINH VIÊN
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div v-if="bookings.length === 0" class="text-center py-4 px-3">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width: 48px; height: 48px; background-color: #f1f5f9;">
                                            <i class="fas fa-calendar-check text-primary" style="font-size: 1.25rem;"></i>
                                        </div>
                                        <p class="text-muted text-xs font-weight-medium mb-0">Không có yêu cầu đặt lịch hẹn nào đang chờ xử lý.</p>
                                    </div>
                                    <AppTable
                                        v-else
                                        :columns="[
                                            { key: 'mssv', label: 'Mã sinh viên' },
                                            { key: 'ten_sinh_vien', label: 'Họ và tên' },
                                            { key: 'thoi_gian_bat_dau', label: 'Thời gian đăng ký' },
                                            { key: 'noi_dung', label: 'Nội dung câu hỏi / trao đổi' }
                                        ]"
                                        :items="bookings"
                                        actions-width="250px"
                                        empty-text="Không có yêu cầu đặt lịch hẹn nào đang chờ xử lý."
                                    >
                                        <template #col-mssv="{ item }">
                                            <AppBadge variant="secondary">{{ item.mssv }}</AppBadge>
                                        </template>
                                        <template #col-ten_sinh_vien="{ item }">
                                            <span class="font-weight-bold text-dark">{{ item.ten_sinh_vien }}</span>
                                        </template>
                                        <template #col-thoi_gian_bat_dau="{ item }">
                                            <span>{{ formatDatetime(item.thoi_gian_bat_dau) }}</span>
                                        </template>
                                        <template #col-noi_dung="{ item }">
                                            <span>{{ item.noi_dung }}</span>
                                        </template>
                                        <template #actions="{ item }">
                                            <div v-if="item.trang_thai === 'cho_duyet'" class="d-inline-flex gap-1">
                                                <LTEButton 
                                                    variant="success" 
                                                    icon="fas fa-check" 
                                                    text="Đồng ý" 
                                                    class="btn-sm text-xs font-weight-bold" 
                                                    @click="updateStatus(item.id, 'da_xac_nhan')" 
                                                />
                                                <LTEButton 
                                                    variant="danger" 
                                                    icon="fas fa-times" 
                                                    text="Từ chối" 
                                                    class="btn-sm text-xs font-weight-bold ml-1" 
                                                    @click="updateStatus(item.id, 'tu_choi')" 
                                                />
                                            </div>
                                            <AppBadge v-else :variant="getStatusVariant(item.trang_thai)">
                                                {{ getStatusLabel(item.trang_thai) }}
                                            </AppBadge>
                                        </template>
                                    </AppTable>
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
import LTEButton from '@/components/controls/LTEButton.vue';

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

const getStatusVariant = (status) => {
    switch (status) {
        case 'da_xac_nhan': return 'success';
        case 'tu_choi': return 'danger';
        default: return 'warning';
    }
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
.info-box {
    display: flex;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    min-height: 82px;
    padding: 0.9rem 1.15rem;
    align-items: center;
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03), 0 2px 6px -2px rgba(0, 0, 0, 0.02) !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.info-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.08), 0 4px 10px -3px rgba(0, 0, 0, 0.03) !important;
    border-color: #cbd5e1 !important;
}

.info-box-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    width: 48px;
    height: 48px;
    font-size: 18px;
    flex-shrink: 0;
}

.icon-blue {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}
.icon-amber {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}
.icon-emerald {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.info-box-content {
    padding-left: 0.85rem;
    flex: 1;
}

.uppercase {
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
</style>
