<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="row m-0">
                <div class="col-12 p-0">
                    <div class="card border-0" style="border: 1px solid #cbd5e1 !important; border-radius: 8px !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important; overflow: hidden; background: #ffffff;">
                        <!-- Light Mode Card Header matching Permissions.vue -->
                        <div class="card-header py-2 px-3 bg-light border-bottom" style="background-color: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important;">
                            <h6 class="card-title font-weight-bold mb-0 text-xs text-uppercase tracking-wider text-dark d-flex align-items-center">
                                <i class="fas fa-cogs mr-2 text-primary"></i>
                                CÀI ĐẶT HỆ THỐNG
                            </h6>
                        </div>

                        <div class="card-body p-2.5">
                            <!-- Top Toolbar using Shared Control Components -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2.5" style="gap: 8px;">
                                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                    <!-- Search Input using LTEInput -->
                                    <LTEInput 
                                        v-model="tableSearchQuery" 
                                        placeholder="Tìm kiếm cài đặt theo tên, mô tả..." 
                                        :visible-label="false" 
                                        input-class="form-control form-control-sm text-xs"
                                        style="width: 250px;"
                                    />

                                    <!-- Category Filter using LTESelect2Option -->
                                    <LTESelect2Option 
                                        v-model="selectedCategoryFilter"
                                        :init-value="selectedCategoryFilter"
                                        placeholder="-- Tất cả danh mục --" 
                                        :data="categorySelectOptions" 
                                        :multiple="false"
                                        :close-on-select="true"
                                        :allow-clear="true"
                                        :enable-data-watch="true"
                                        style="width: 180px;"
                                    />

                                    <!-- Refresh Button using IconButton -->
                                    <IconButton 
                                        variant="slate" 
                                        icon="fas fa-sync-alt" 
                                        title="Làm mới" 
                                        @click="fetchSettings"
                                    />
                                </div>

                                <!-- Add Button using LTEButton -->
                                <LTEButton 
                                    v-if="authStore.hasPermission('PhanQuyenController.putCaiDat')"
                                    variant="success"
                                    icon="fas fa-plus"
                                    text="Thêm Cài đặt mới"
                                    class="btn-sm text-xs font-weight-bold px-2.5 shadow-sm"
                                    @click="openSettingModal"
                                />
                            </div>

                            <div v-if="loading" class="text-center py-4">
                                <LoadingSpinner />
                                <p class="text-muted mt-2 text-xs">Đang tải danh sách cài đặt...</p>
                            </div>

                            <!-- AppTable Component matching Permissions.vue Tab 3 -->
                            <AppTable v-else>
                                <template #thead>
                                    <thead style="background-color: #007bff !important;">
                                        <tr>
                                            <th style="width: 45px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">#</th>
                                            <th style="min-width: 260px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">Mô tả cài đặt</th>
                                            <th style="min-width: 240px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">Tên biến cài đặt</th>
                                            <th style="width: 140px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Giá trị</th>
                                            <th style="width: 120px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Kiểu dữ liệu</th>
                                            <th style="width: 100px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Thao tác</th>
                                        </tr>
                                    </thead>
                                </template>

                                <template #tbody>
                                    <tbody>
                                        <template v-for="(groupItems, groupName) in filteredGroupedSettings" :key="groupName">
                                            <!-- Group Category Header Row (colspan=6) -->
                                            <tr style="background-color: #eff6ff; border-left: 4px solid #2563eb;">
                                                <td colspan="6" class="py-1 px-3 text-primary font-weight-bold text-xs">
                                                    <i class="fas fa-cubes mr-1.5"></i> {{ groupName === 'default' ? 'Khác / Chưa gán danh mục' : groupName }} ({{ groupItems.length }} cài đặt)
                                                </td>
                                            </tr>

                                            <tr v-for="(item, idx) in groupItems" :key="item.id">
                                                <td class="py-1 px-2 text-xs text-muted align-middle">{{ idx + 1 }}</td>
                                                <td class="py-1 px-2 text-xs align-middle">
                                                    <span class="font-weight-bold text-dark d-block">{{ item.mo_ta || item.tieu_de || '-' }}</span>
                                                </td>
                                                <td class="py-1 px-2 text-xs align-middle font-mono font-weight-bold text-secondary">
                                                    {{ item.khoa }}
                                                </td>
                                                <td class="py-1 px-2 text-xs text-center align-middle">
                                                    <!-- Boolean Badge (Green for 1/true, Red for 0/false) -->
                                                    <span v-if="item.kieu_du_lieu === 'Boolean'" 
                                                          class="badge px-2.5 py-1 text-xs font-weight-bold" 
                                                          :class="String(item.gia_tri) === '1' || String(item.gia_tri).toLowerCase() === 'true' ? 'badge-success' : 'badge-danger'"
                                                    >
                                                        {{ item.gia_tri }}
                                                    </span>

                                                    <!-- Text / Number / JSON Badge (Teal/Cyan) -->
                                                    <span v-else class="badge badge-info px-2.5 py-1 text-xs font-weight-bold">
                                                        {{ item.gia_tri || '(trống)' }}
                                                    </span>
                                                </td>
                                                <td class="py-1 px-2 text-xs text-center text-muted align-middle">
                                                    {{ item.kieu_du_lieu || 'Text' }}
                                                </td>
                                                <td class="text-center py-1 px-2 align-middle">
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        <IconButton 
                                                            v-if="authStore.hasPermission('PhanQuyenController.putCaiDat')"
                                                            variant="amber"
                                                            icon="fas fa-edit"
                                                            title="Sửa Cài đặt"
                                                            @click="openSettingModal(item)"
                                                        />
                                                        <IconButton 
                                                            v-if="authStore.hasPermission('PhanQuyenController.deleteCaiDat')"
                                                            variant="red"
                                                            icon="fas fa-trash-alt"
                                                            title="Xóa Cài đặt"
                                                            @click="deleteSetting(item)"
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <tr v-if="Object.keys(filteredGroupedSettings).length === 0">
                                            <td colspan="6" class="py-3 text-center text-muted italic text-xs">
                                                Không tìm thấy biến cài đặt nào phù hợp.
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </AppTable>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LTEModal Component matching Screenshot 2 -->
            <LTEModal ref="settingModal" @close="resetSettingForm">
                <form @submit.prevent="submitSettingForm">
                    <!-- Danh mục sử dụng LTESuggestion -->
                    <div class="form-group mb-2">
                        <label class="font-weight-bold small text-muted text-xs mb-1">Danh mục</label>
                        <LTESuggestion 
                            v-model="settingForm.danh_muc" 
                            :search="searchCategorySuggestions" 
                            :free-text="true" 
                            :template="item => `<span class='text-xs text-dark'>${item.label}</span>`" 
                            :template-show="item => item?.label || item?.id || String(item)" 
                            input-class="form-control form-control-sm text-xs"
                            placeholder="Nhập hoặc chọn danh mục..." 
                        />
                    </div>

                    <!-- Tên biến cài đặt using LTEInput -->
                    <LTEInput 
                        v-model="settingForm.khoa" 
                        label="Tên biến cài đặt *" 
                        placeholder="Nhập tên biến cài đặt ...." 
                        input-class="form-control form-control-sm text-xs font-mono" 
                        class="mb-2"
                        required
                    />

                    <!-- Mô tả cài đặt using LTEInput -->
                    <LTEInput 
                        v-model="settingForm.mo_ta" 
                        label="Mô tả cài đặt" 
                        placeholder="Nhập mô tả cho biến cài đặt này...." 
                        input-class="form-control form-control-sm text-xs" 
                        class="mb-2"
                    />

                    <!-- Kiểu dữ liệu using LTESelect2Option -->
                    <LTESelect2Option 
                        v-model="settingForm.kieu_du_lieu"
                        :init-value="settingForm.kieu_du_lieu"
                        label="Kiểu dữ liệu" 
                        placeholder="Chọn kiểu dữ liệu..." 
                        :data="dataTypeOptions" 
                        :multiple="false"
                        :close-on-select="true"
                        :allow-clear="false"
                        :enable-data-watch="true"
                        class="mb-2"
                    />

                    <!-- Giá trị chuỗi using LTETextArea -->
                    <LTETextArea 
                        v-model="settingForm.gia_tri" 
                        label="Giá trị chuỗi" 
                        placeholder="Nhập chuỗi ...." 
                        textarea-class="form-control form-control-sm text-xs font-mono" 
                        :rows="3"
                        class="mb-2"
                    />
                </form>
            </LTEModal>
        </template>
    </LTEContentWrapper>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';
import AppTable from '@/components/controls/AppTable.vue';
import IconButton from '@/components/controls/IconButton.vue';
import LTEButton from '@/components/controls/LTEButton.vue';
import LTEModal from '@/components/controls/LTEModal.vue';
import LTEContentWrapper from '@/components/controls/LTEContentWrapper.vue';
import LoadingSpinner from '@/components/controls/LoadingSpinner.vue';
import LTESuggestion from '@/components/controls/LTESuggestion.vue';
import LTEInput from '@/components/controls/LTEInput.vue';
import LTESelect2Option from '@/components/controls/LTESelect2Option.vue';
import LTETextArea from '@/components/controls/LTETextArea.vue';

const authStore = useAuthStore();
const loading = ref(false);
const settingsList = ref([]);
const settingModal = ref(null);
const tableSearchQuery = ref('');
const selectedCategoryFilter = ref('');

const dataTypeOptions = [
    { value: 'Text', text: 'Text' },
    { value: 'Number', text: 'Number' },
    { value: 'Boolean', text: 'Boolean' },
    { value: 'JSON', text: 'JSON' }
];

const settingForm = reactive({
    id: null,
    danh_muc: '',
    khoa: '',
    mo_ta: '',
    kieu_du_lieu: 'Text',
    gia_tri: ''
});

// Auto Suggestions cho Danh mục dùng LTESuggestion
const existingCategories = computed(() => {
    const set = new Set();
    (settingsList.value || []).forEach(item => {
        if (item.danh_muc && item.danh_muc.trim()) {
            set.add(item.danh_muc.trim());
        }
    });
    return Array.from(set);
});

const categorySelectOptions = computed(() => {
    return existingCategories.value.map(cat => ({
        value: cat,
        text: cat
    }));
});

const searchCategorySuggestions = async (query) => {
    const categories = existingCategories.value;
    const items = categories.map(cat => ({ id: cat, value: cat, label: cat }));
    if (!query) return items;
    const q = query.toLowerCase();
    return items.filter(item => item.label.toLowerCase().includes(q));
};

// Filtered & Grouped Settings
const filteredGroupedSettings = computed(() => {
    const query = (tableSearchQuery.value || '').toLowerCase().trim();
    const catFilter = selectedCategoryFilter.value;

    const filtered = (settingsList.value || []).filter(item => {
        const matchesQuery = !query || 
            (item.khoa && item.khoa.toLowerCase().includes(query)) ||
            (item.mo_ta && item.mo_ta.toLowerCase().includes(query)) ||
            (item.tieu_de && item.tieu_de.toLowerCase().includes(query)) ||
            (item.danh_muc && item.danh_muc.toLowerCase().includes(query));

        const matchesCat = !catFilter || (item.danh_muc && item.danh_muc.trim() === catFilter);

        return matchesQuery && matchesCat;
    });

    const groups = {};
    filtered.forEach(item => {
        const cat = item.danh_muc && item.danh_muc.trim() ? item.danh_muc.trim() : 'default';
        if (!groups[cat]) {
            groups[cat] = [];
        }
        groups[cat].push(item);
    });
    return groups;
});

const fetchSettings = async () => {
    loading.value = true;
    try {
        const res = await axios.get(route('PhanQuyenController.getCaiDat'));
        if (res.data.status === 200) {
            settingsList.value = res.data.data || [];
        }
    } catch (err) {
        console.error('Error fetching settings:', err);
    } finally {
        loading.value = false;
    }
};

const openSettingModal = async (item = null) => {
    if (item) {
        settingForm.id = item.id;
        settingForm.danh_muc = item.danh_muc || '';
        settingForm.khoa = item.khoa || '';
        settingForm.mo_ta = item.mo_ta || item.tieu_de || '';
        settingForm.kieu_du_lieu = item.kieu_du_lieu || 'Text';
        settingForm.gia_tri = item.gia_tri || '';
        if (settingModal.value) {
            settingModal.value.$data.title = 'Chỉnh sửa thông tin';
            settingModal.value.$data.save = 'Lưu thông tin';
        }
    } else {
        resetSettingForm();
        if (settingModal.value) {
            settingModal.value.$data.title = 'Thêm mới thông tin';
            settingModal.value.$data.save = 'Lưu thông tin';
        }
    }
    if (settingModal.value) {
        const res = await settingModal.value.openModal();
        if (res) {
            submitSettingForm();
        }
    }
};

const resetSettingForm = () => {
    settingForm.id = null;
    settingForm.danh_muc = '';
    settingForm.khoa = '';
    settingForm.mo_ta = '';
    settingForm.kieu_du_lieu = 'Text';
    settingForm.gia_tri = '';
};

const submitSettingForm = async () => {
    try {
        const res = await axios.post(route('PhanQuyenController.putCaiDat'), settingForm);
        if (res.data.status === 200) {
            if (settingModal.value) settingModal.value.closeModal();
            if (window.func && window.func.toastSuccess) window.func.toastSuccess(res.data.message);
            fetchSettings();
        }
    } catch (err) {
        console.error('Error submitting setting form:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError(err.response?.data?.message || 'Lưu cài đặt thất bại');
        }
    }
};

const deleteSetting = async (item) => {
    if (!confirm(`Bạn có chắc chắn muốn xóa biến cài đặt "${item.khoa}"?`)) return;
    try {
        const res = await axios.delete(route('PhanQuyenController.deleteCaiDat', { id: item.id }));
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) window.func.toastSuccess('Xóa cài đặt thành công!');
            fetchSettings();
        }
    } catch (err) {
        console.error('Error deleting setting:', err);
        if (window.func && window.func.toastError) window.func.toastError('Xóa cài đặt thất bại');
    }
};

onMounted(() => {
    fetchSettings();
});
</script>
