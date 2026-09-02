<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="row m-0">
                <div class="col-12 p-0">
                    <div class="card border-0" style="border: 1px solid #cbd5e1 !important; border-radius: 8px !important; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important; overflow: hidden; background: #ffffff;">
                        <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #2b5876, #4e4376); border-radius: 12px 12px 0 0 !important; border-bottom: none !important;">
                            <h3 class="card-title float-none font-weight-bold mb-0">
                                <i class="fas fa-calendar-check mr-2"></i>
                                ĐẶT LỊCH HẸN GIẢNG VIÊN
                            </h3>
                        </div>

                        <div class="card-body p-4">
                            <!-- Action header -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="text-primary font-weight-bold mb-0">Danh sách lịch hẹn của bạn</h5>
                                <LTEButton 
                                    v-if="authStore.hasPermission('StudentPortalController.createBooking')"
                                    variant="info"
                                    icon="fas fa-calendar-plus"
                                    text="Đặt lịch hẹn mới"
                                    class="btn-sm text-xs font-weight-bold px-3 shadow-sm text-white"
                                    @click="openBookingModal"
                                />
                            </div>

                            <!-- Bookings list -->
                            <div v-if="loading" class="text-center my-5">
                                <LoadingSpinner />
                                <p class="mt-2 text-muted">Đang tải danh sách lịch hẹn...</p>
                            </div>

                            <div v-else-if="list.length === 0" class="text-center py-5 border rounded bg-light">
                                <i class="far fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="lead text-muted mb-0">Bạn chưa đặt lịch hẹn nào với giảng viên.</p>
                                <span class="small text-muted">Bấm nút "Đặt lịch hẹn mới" để kết nối với giảng viên.</span>
                            </div>

                            <AppTable
                                v-else
                                :columns="[
                                    { key: 'idx', label: '#', width: '45px', align: 'center' },
                                    { key: 'ten_giang_vien', label: 'Giảng viên', width: '25%' },
                                    { key: 'thoi_gian_bat_dau', label: 'Thời gian hẹn', width: '20%' },
                                    { key: 'noi_dung', label: 'Nội dung cuộc gặp', width: '35%' },
                                    { key: 'trang_thai', label: 'Trạng thái', width: '15%', align: 'center' }
                                ]"
                                :items="list"
                                empty-text="Bạn chưa đặt lịch hẹn nào với giảng viên."
                            >
                                <template #col-idx="{ index }">
                                    <span class="text-muted">{{ index + 1 }}</span>
                                </template>
                                <template #col-ten_giang_vien="{ item }">
                                    <span class="font-weight-bold text-dark">{{ item.ten_giang_vien }}</span>
                                </template>
                                <template #col-thoi_gian_bat_dau="{ item }">
                                    <span>{{ formatDatetime(item.thoi_gian_bat_dau) }}</span>
                                </template>
                                <template #col-noi_dung="{ item }">
                                    <span>{{ item.noi_dung }}</span>
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
            </div>

            <!-- Booking Modal -->
            <LTEModal ref="bookingModal" @close="resetForm">
                <form @submit.prevent="submitBooking">
                    <div class="row">
                        <!-- Lecturer Select -->
                        <div class="col-12 mb-3">
                            <LTESelect2Option 
                                v-model="form.giang_vien_id" 
                                :init-value="form.giang_vien_id"
                                label="Chọn Giảng viên *" 
                                placeholder="-- Chọn giảng viên muốn đặt lịch --" 
                                :data="lecturers.map(gv => ({ value: gv.id, text: `${gv.ho_ten} (${gv.email})` }))" 
                                :multiple="false" 
                                :close-on-select="true" 
                                :allow-clear="false" 
                                :enable-data-watch="true" 
                            />
                        </div>

                        <!-- Date/Time Select -->
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold small text-muted">Thời gian bắt đầu hẹn <span class="text-danger">*</span></label>
                            <input 
                                v-model="form.thoi_gian_bat_dau" 
                                type="datetime-local" 
                                class="form-control" 
                                required
                            />
                        </div>

                        <!-- Topic / Contents -->
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold small text-muted">Nội dung cuộc hẹn / Câu hỏi cần tư vấn <span class="text-danger">*</span></label>
                            <textarea 
                                v-model="form.noi_dung" 
                                class="form-control" 
                                rows="4" 
                                placeholder="Ghi rõ nội dung chi tiết cần gặp mặt hoặc trao đổi" 
                                required
                            ></textarea>
                        </div>
                    </div>
                </form>
            </LTEModal>
        </template>
    </LTEContentWrapper>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';
import LTESelect2Option from '@/components/controls/LTESelect2Option.vue';
import LTEButton from '@/components/controls/LTEButton.vue';
import IconButton from '@/components/controls/IconButton.vue';

const authStore = useAuthStore();
const loading = ref(true);
const list = ref([]);
const lecturers = ref([]);
const bookingModal = ref(null);

const form = reactive({
    giang_vien_id: '',
    thoi_gian_bat_dau: '',
    noi_dung: ''
});

// Load student bookings list
const fetchBookings = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('StudentPortalController.getBookingsList'));
        if (response.data.status === 200) {
            list.value = response.data.data;
        }
    } catch (err) {
        console.error('Error fetching bookings:', err);
    } finally {
        loading.value = false;
    }
};

// Load lecturers options for booking selection
const fetchLecturers = async () => {
    try {
        const response = await axios.get(route('StudentPortalController.getBookingLecturers'));
        if (response.data.status === 200) {
            lecturers.value = response.data.data;
        }
    } catch (err) {
        console.error('Error loading lecturers:', err);
    }
};

// Open booking modal
const openBookingModal = async () => {
    resetForm();
    await fetchLecturers();
    bookingModal.value.$data.title = 'Đăng ký lịch hẹn với Giảng viên';
    bookingModal.value.$data.save = 'Xác nhận đặt lịch';
    
    const result = await bookingModal.value.openModal();
    if (result) {
        await submitBooking();
    }
};

// Reset form fields
const resetForm = () => {
    form.giang_vien_id = '';
    form.thoi_gian_bat_dau = '';
    form.noi_dung = '';
};

// Send booking request to backend
const submitBooking = async () => {
    if (!form.giang_vien_id || !form.thoi_gian_bat_dau || !form.noi_dung) {
        if (window.func && window.func.toastError) {
            window.func.toastError('Vui lòng nhập đầy đủ các thông tin bắt buộc');
        }
        return;
    }

    try {
        const response = await axios.post(route('StudentPortalController.createBooking'), form);
        if (response.data.status === 200) {
            if (window.func && window.func.toastSuccess) {
                window.func.toastSuccess('Gửi yêu cầu đặt lịch thành công!');
            }
            fetchBookings();
        }
    } catch (err) {
        console.error('Error creating booking:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError('Đăng ký đặt lịch thất bại');
        }
    }
};

// Formatting utilities
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
        case 'da_xac_nhan': return 'Đã xác nhận';
        case 'tu_choi': return 'Từ chối';
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
    fetchBookings();
});
</script>

<style scoped>
.table th {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
