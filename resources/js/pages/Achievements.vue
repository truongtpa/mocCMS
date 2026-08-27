<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="card border-0" style="border: 1px solid #e9ecef !important; border-radius: 12px !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important; overflow: hidden; max-width: 1200px; margin: 0 auto;">
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
                                <button 
                                    v-if="authStore.hasPermission('StudentPortalController.addAchievement')"
                                    class="btn btn-success px-3 shadow-sm" 
                                    @click="openAddModal"
                                >
                                    <i class="fas fa-plus mr-1"></i>
                                    Khai báo thành tích mới
                                </button>
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

                            <div v-else class="table-responsive">
                                <table class="table table-hover border">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 45%;">Tên giải thưởng / Thành tích</th>
                                            <th style="width: 15%;">Năm nhận</th>
                                            <th style="width: 20%;">Cấp khen thưởng</th>
                                            <th style="width: 15%;" class="text-center">Minh chứng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, idx) in list" :key="item.id">
                                            <td>{{ idx + 1 }}</td>
                                            <td class="font-weight-bold text-dark">{{ item.ten_giai_thuong }}</td>
                                            <td>{{ item.nam_nhan }}</td>
                                            <td>
                                                <span class="badge" :class="getBadgeClass(item.cap_khen_thuong)">
                                                    {{ item.cap_khen_thuong }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a 
                                                    v-if="item.file_minh_chung" 
                                                    :href="item.file_minh_chung" 
                                                    target="_blank" 
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <i class="fas fa-file-pdf mr-1"></i> Xem file
                                                </a>
                                                <span v-else class="text-muted small">Không có</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
                            <label class="font-weight-bold small text-muted">Cấp khen thưởng <span class="text-danger">*</span></label>
                            <select v-model="form.cap_khen_thuong" class="form-control" required>
                                <option value="" disabled>-- Chọn cấp khen thưởng --</option>
                                <option value="Cấp lớp/Khoa">Cấp lớp/Khoa</option>
                                <option value="Cấp trường">Cấp trường</option>
                                <option value="Cấp Tỉnh/Thành phố">Cấp Tỉnh/Thành phố</option>
                                <option value="Cấp Quốc gia">Cấp Quốc gia</option>
                                <option value="Cấp Quốc tế">Cấp Quốc tế</option>
                            </select>
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
