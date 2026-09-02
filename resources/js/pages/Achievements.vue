<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="row m-0">
                <div class="col-12 p-0">
                    <div class="card border-0" style="border: 1px solid #cbd5e1 !important; border-radius: 8px !important; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important; overflow: hidden; background: #ffffff;">
                        <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #11998e, #38ef7d); border-radius: 12px 12px 0 0 !important; border-bottom: none !important;">
                            <h3 class="card-title float-none font-weight-bold mb-0">
                                <i class="fas fa-trophy mr-2"></i>
                                GIẢI THƯỞNG & THÀNH TÍCH
                            </h3>
                        </div>

                        <div class="card-body p-4">
                            <!-- Action buttons -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="text-primary font-weight-bold mb-0">Danh sách thành tích của bạn</h5>
                                <LTEButton 
                                    v-if="authStore.hasPermission('StudentPortalController.addAchievement')"
                                    variant="success"
                                    icon="fas fa-plus"
                                    text="Khai báo thành tích mới"
                                    class="btn-sm text-xs font-weight-bold px-3 shadow-sm"
                                    @click="openAddModal"
                                />
                            </div>

                            <!-- Achievements Table -->
                            <div v-if="loading" class="text-center my-5">
                                <LoadingSpinner />
                                <p class="mt-2 text-muted">Đang tải danh sách thành tích...</p>
                            </div>

                            <div v-else-if="list.length === 0" class="text-center py-5 border rounded bg-light">
                                <i class="fas fa-award fa-3x text-muted mb-3"></i>
                                <p class="lead text-muted mb-0">Bạn chưa khai báo giải thưởng hoặc thành tích nào.</p>
                                <span class="small text-muted">Bấm nút "Khai báo thành tích mới" để thêm thông tin.</span>
                            </div>

                            <AppTable
                                v-else
                                :columns="[
                                    { key: 'idx', label: '#', width: '45px', align: 'center' },
                                    { key: 'ten_giai_thuong', label: 'Tên giải thưởng / Thành tích', width: '45%' },
                                    { key: 'nam_nhan', label: 'Năm nhận', width: '15%' },
                                    { key: 'cap_khen_thuong', label: 'Cấp khen thưởng', width: '20%' },
                                    { key: 'file_minh_chung', label: 'Minh chứng', width: '15%', align: 'center' }
                                ]"
                                :items="list"
                                empty-text="Bạn chưa khai báo giải thưởng hoặc thành tích nào."
                            >
                                <template #col-idx="{ index }">
                                    <span class="text-muted">{{ index + 1 }}</span>
                                </template>
                                <template #col-ten_giai_thuong="{ item }">
                                    <span class="font-weight-bold text-dark">{{ item.ten_giai_thuong }}</span>
                                </template>
                                <template #col-nam_nhan="{ item }">
                                    <span>{{ item.nam_nhan }}</span>
                                </template>
                                <template #col-cap_khen_thuong="{ item }">
                                    <AppBadge :variant="getBadgeVariant(item.cap_khen_thuong)">
                                        {{ item.cap_khen_thuong }}
                                    </AppBadge>
                                </template>
                                <template #col-file_minh_chung="{ item }">
                                    <a 
                                        v-if="item.file_minh_chung" 
                                        :href="item.file_minh_chung" 
                                        target="_blank" 
                                        class="btn btn-xs btn-outline-primary"
                                    >
                                        <i class="fas fa-file-pdf mr-1"></i> Xem file
                                    </a>
                                    <span v-else class="text-muted small">Không có</span>
                                </template>
                            </AppTable>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Adding Achievement -->
            <LTEModal ref="addModal" @close="resetForm">
                <form @submit.prevent="submitForm">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold small text-muted">Tên giải thưởng / Thành tích <span class="text-danger">*</span></label>
                            <input 
                                v-model="form.ten_giai_thuong" 
                                type="text" 
                                class="form-control" 
                                placeholder="Ví dụ: Giải nhất Olympic Tin học sinh viên" 
                                required
                            />
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="font-weight-bold small text-muted">Năm nhận <span class="text-danger">*</span></label>
                            <input 
                                v-model="form.nam_nhan" 
                                type="number" 
                                class="form-control" 
                                placeholder="Ví dụ: 2026" 
                                :max="new Date().getFullYear()"
                                required
                            />
                        </div>
                        <div class="col-sm-6 mb-3">
                            <LTESelect2Option 
                                v-model="form.cap_khen_thuong" 
                                :init-value="form.cap_khen_thuong"
                                label="Cấp khen thưởng *" 
                                placeholder="-- Chọn cấp khen thưởng --" 
                                :data="[
                                    { value: 'Cấp lớp/Khoa', text: 'Cấp lớp/Khoa' },
                                    { value: 'Cấp trường', text: 'Cấp trường' },
                                    { value: 'Cấp Tỉnh/Thành phố', text: 'Cấp Tỉnh/Thành phố' },
                                    { value: 'Cấp Quốc gia', text: 'Cấp Quốc gia' },
                                    { value: 'Cấp Quốc tế', text: 'Cấp Quốc tế' }
                                ]" 
                                :multiple="false" 
                                :close-on-select="true" 
                                :allow-clear="false" 
                                :enable-data-watch="true" 
                            />
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold small text-muted">Link file minh chứng (hoặc upload)</label>
                            <input 
                                v-model="form.file_minh_chung" 
                                type="text" 
                                class="form-control" 
                                placeholder="http://example.com/minh-chung.pdf"
                            />
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
const addModal = ref(null);

const form = reactive({
    ten_giai_thuong: '',
    nam_nhan: new Date().getFullYear(),
    cap_khen_thuong: '',
    file_minh_chung: ''
});

// Load achievements
const fetchAchievements = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('StudentPortalController.getAchievementsList'));
        if (response.data.status === 200) {
            list.value = response.data.data;
        }
    } catch (err) {
        console.error('Error fetching achievements:', err);
    } finally {
        loading.value = false;
    }
};

// Open add modal
const openAddModal = async () => {
    resetForm();
    addModal.value.$data.title = 'Khai báo thành tích / Giải thưởng mới';
    addModal.value.$data.save = 'Lưu thành tích';
    
    const result = await addModal.value.openModal();
    if (result) {
        await submitForm();
    }
};

// Reset form fields
const resetForm = () => {
    form.ten_giai_thuong = '';
    form.nam_nhan = new Date().getFullYear();
    form.cap_khen_thuong = '';
    form.file_minh_chung = '';
};

// Submit form to Laravel backend
const submitForm = async () => {
    if (!form.ten_giai_thuong || !form.nam_nhan || !form.cap_khen_thuong) {
        if (window.func && window.func.toastError) {
            window.func.toastError('Vui lòng điền đầy đủ các thông tin bắt buộc');
        }
        return;
    }

    try {
        const response = await axios.post(route('StudentPortalController.addAchievement'), form);
        if (response.data.status === 200) {
            if (window.func && window.func.toastSuccess) {
                window.func.toastSuccess('Khai báo thành tích thành công!');
            }
            fetchAchievements();
        }
    } catch (err) {
        console.error('Error adding achievement:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError('Lưu thành tích thất bại');
        }
    }
};

// Helper style
const getBadgeVariant = (cap) => {
    switch (cap) {
        case 'Cấp Quốc tế': return 'danger';
        case 'Cấp Quốc gia': return 'warning';
        case 'Cấp Tỉnh/Thành phố': return 'info';
        case 'Cấp trường': return 'primary';
        default: return 'secondary';
    }
};

const getBadgeClass = (cap) => {
    switch (cap) {
        case 'Cấp Quốc tế': return 'badge-danger';
        case 'Cấp Quốc gia': return 'badge-warning text-dark';
        case 'Cấp Tỉnh/Thành phố': return 'badge-info';
        case 'Cấp trường': return 'badge-primary';
        default: return 'badge-secondary';
    }
};

onMounted(() => {
    fetchAchievements();
});
</script>

<style scoped>
.table th {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
