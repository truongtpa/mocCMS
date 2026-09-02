<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <!-- Loading State -->
                    <div v-if="loading" class="text-center my-5">
                        <LoadingSpinner />
                        <p class="mt-2 text-muted">Đang tải thông tin cá nhân...</p>
                    </div>

                    <!-- Main Profile Card -->
                    <div v-else class="card border-0" style="border: 1px solid #e9ecef !important; border-radius: 12px !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important; overflow: hidden; max-width: 1200px; margin: 0 auto;">
                        <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #1f4068, #162447); border-radius: 12px 12px 0 0 !important; border-bottom: none !important;">
                            <h3 class="card-title float-none font-weight-bold mb-0">THÔNG TIN CÁ NHÂN</h3>
                        </div>

                        <div class="card-body p-4">
                            <!-- Basic Fixed Information -->
                            <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2">Thông tin định danh</h5>
                            <div class="row mb-4">
                                <div class="col-sm-6 mb-3">
                                    <label class="text-muted small">Họ và tên</label>
                                    <div class="h6 font-weight-bold text-dark">{{ profile.ho_ten }}</div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="text-muted small">
                                        {{ profile.user_type === 'sinh_vien' ? 'Mã số sinh viên (MSSV)' : 'Mã số giảng viên (MSGV)' }}
                                    </label>
                                    <div class="h6 font-weight-bold text-dark">
                                        {{ profile.user_type === 'sinh_vien' ? profile.mssv : (profile.email && profile.email.includes('@') ? profile.email.split('@')[0] : ('GV_' + profile.id)) }}
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="text-muted small">Email cá nhân</label>
                                    <div class="h6 font-weight-bold text-dark">{{ profile.email }}</div>
                                </div>
                                <div v-if="profile.id_don_vi" class="col-sm-6 mb-3">
                                    <label class="text-muted small">Đơn vị công tác</label>
                                    <div class="h6 font-weight-bold text-dark">Đơn vị {{ profile.id_don_vi }}</div>
                                </div>
                            </div>

                            <form @submit.prevent="saveProfile">
                                <!-- Dynamic Grouped EAV Attributes -->
                                <div 
                                    v-for="(groupAttrs, groupName) in groupedAttributes" 
                                    :key="groupName" 
                                    class="mb-4"
                                >
                                    <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2">
                                        {{ groupName }}
                                    </h5>
                                    <div class="row">
                                        <div 
                                            v-for="attr in groupAttrs" 
                                            :key="attr.id" 
                                            :class="[getColClass(attr.col_span || (['textarea', 'file', 'image'].includes(attr.kieu_du_lieu) ? 12 : 6)), 'mb-3']"
                                        >
                                            <label class="font-weight-bold small text-muted mb-1.5">{{ attr.ten_truong }}</label>
                                            
                                            <!-- Textarea -->
                                            <textarea 
                                                v-if="attr.kieu_du_lieu === 'textarea'"
                                                v-model="attr.value"
                                                :disabled="!isEditable(attr)"
                                                :readonly="!isEditable(attr)"
                                                class="custom-input"
                                                rows="3"
                                                :style="!isEditable(attr) ? 'height: auto; background-color: #f1f5f9 !important; cursor: not-allowed !important;' : 'height: auto;'"
                                                :placeholder="`Nhập ${attr.ten_truong.toLowerCase()}...`"
                                            ></textarea>

                                            <!-- Date Picker -->
                                            <input 
                                                v-else-if="attr.kieu_du_lieu === 'date'"
                                                v-model="attr.value" 
                                                :disabled="!isEditable(attr)"
                                                :readonly="!isEditable(attr)"
                                                type="date" 
                                                class="custom-input"
                                                :style="!isEditable(attr) ? 'background-color: #f1f5f9 !important; cursor: not-allowed !important;' : ''"
                                            />

                                            <!-- Datetime Picker -->
                                            <input 
                                                v-else-if="attr.kieu_du_lieu === 'datetime'"
                                                v-model="attr.value" 
                                                :disabled="!isEditable(attr)"
                                                :readonly="!isEditable(attr)"
                                                type="datetime-local" 
                                                class="custom-input"
                                                :style="!isEditable(attr) ? 'background-color: #f1f5f9 !important; cursor: not-allowed !important;' : ''"
                                            />

                                            <!-- Number Input -->
                                            <input 
                                                v-else-if="attr.kieu_du_lieu === 'number'"
                                                v-model="attr.value" 
                                                :disabled="!isEditable(attr)"
                                                :readonly="!isEditable(attr)"
                                                type="number" 
                                                class="custom-input"
                                                :style="!isEditable(attr) ? 'background-color: #f1f5f9 !important; cursor: not-allowed !important;' : ''"
                                                :placeholder="`Nhập ${attr.ten_truong.toLowerCase()}...`"
                                            />

                                            <!-- Select Dropdown -->
                                            <select
                                                v-else-if="['select', 'multiselect'].includes(attr.kieu_du_lieu)"
                                                v-model="attr.value"
                                                :disabled="!isEditable(attr)"
                                                class="custom-input custom-select"
                                                :style="!isEditable(attr) ? 'background-color: #f1f5f9 !important; cursor: not-allowed !important;' : ''"
                                            >
                                                <option value="">-- Chọn {{ attr.ten_truong }} --</option>
                                                <option 
                                                    v-for="opt in parseOptions(attr)" 
                                                    :key="opt.value" 
                                                    :value="opt.value"
                                                >
                                                    {{ opt.text }}
                                                </option>
                                            </select>

                                            <!-- Image Input / Upload Preview Card -->
                                            <div v-else-if="attr.kieu_du_lieu === 'image'" class="p-3 border rounded" :style="!isEditable(attr) ? 'background-color: #f1f5f9 !important;' : 'background-color: #f8fafc;'">
                                                <div v-if="attr.value" class="d-flex align-items-center justify-content-between mb-2 p-2 bg-white rounded border">
                                                    <div class="d-flex align-items-center">
                                                        <img :src="attr.value" class="rounded border mr-2" style="width: 50px; height: 50px; object-fit: cover;" />
                                                        <span class="small text-truncate font-weight-bold" style="max-width: 250px;">{{ getFileName(attr.value) }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <a :href="attr.value" target="_blank" class="btn btn-sm btn-outline-primary mr-1" title="Xem ảnh">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <IconButton v-if="isEditable(attr)" variant="red" icon="fas fa-trash-alt" title="Xóa" @click="clearFile(attr)" />
                                                    </div>
                                                </div>
                                                <input 
                                                    v-if="isEditable(attr)"
                                                    type="file" 
                                                    accept="image/*"
                                                    @change="onFileSelected($event, attr)"
                                                    class="form-control-file small"
                                                />
                                            </div>

                                            <!-- File Upload / Download Preview Card -->
                                            <div v-else-if="attr.kieu_du_lieu === 'file'" class="p-3 border rounded" :style="!isEditable(attr) ? 'background-color: #f1f5f9 !important;' : 'background-color: #f8fafc;'">
                                                <div v-if="attr.value" class="d-flex align-items-center justify-content-between mb-2 p-2 bg-white rounded border">
                                                    <div class="d-flex align-items-center">
                                                        <i :class="getFileIconClass(attr.value) + ' fa-2x mr-2'"></i>
                                                        <span class="small text-truncate font-weight-bold" style="max-width: 250px;">{{ getFileName(attr.value) }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <a :href="attr.value" target="_blank" download class="btn btn-sm btn-outline-primary mr-1" title="Tải tệp">
                                                            <i class="fas fa-download"></i> Tải về
                                                        </a>
                                                        <IconButton v-if="isEditable(attr)" variant="red" icon="fas fa-trash-alt" title="Xóa" @click="clearFile(attr)" />
                                                    </div>
                                                </div>
                                                <input 
                                                    v-if="isEditable(attr)"
                                                    type="file" 
                                                    @change="onFileSelected($event, attr)"
                                                    class="form-control-file small"
                                                />
                                            </div>

                                            <!-- Boolean Checkbox -->
                                            <div v-else-if="attr.kieu_du_lieu === 'boolean'" class="form-check pt-2">
                                                <input 
                                                    type="checkbox" 
                                                    v-model="attr.value" 
                                                    :disabled="!isEditable(attr)"
                                                    :id="`check_${attr.id}`"
                                                    class="form-check-input"
                                                />
                                                <label :for="`check_${attr.id}`" class="form-check-label font-weight-bold small text-dark">
                                                    Kích hoạt / Đạt
                                                </label>
                                            </div>

                                            <!-- Color Picker -->
                                            <input 
                                                v-else-if="attr.kieu_du_lieu === 'color'"
                                                v-model="attr.value" 
                                                :disabled="!isEditable(attr)"
                                                type="color" 
                                                class="form-control form-control-color"
                                                style="width: 60px; height: 38px;"
                                            />

                                            <!-- Default Text Input -->
                                            <input 
                                                v-else
                                                v-model="attr.value" 
                                                :disabled="!isEditable(attr)"
                                                :readonly="!isEditable(attr)"
                                                :type="attr.kieu_du_lieu === 'email' ? 'email' : (attr.kieu_du_lieu === 'url' ? 'url' : 'text')" 
                                                class="custom-input"
                                                :style="!isEditable(attr) ? 'background-color: #f1f5f9 !important; cursor: not-allowed !important;' : ''"
                                                :placeholder="`Nhập ${attr.ten_truong.toLowerCase()}...`"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Default empty message if no dynamic attrs -->
                                <div v-if="attributes.length === 0" class="col-12 text-center my-3 text-muted small">
                                    Không có thông tin bổ sung động nào được cấu hình từ hệ đào tạo.
                                </div>

                                <div v-if="authStore.hasPermission('StudentPortalController.updateProfileData')" class="text-right mt-4 border-top pt-3">
                                    <LTEButton
                                        type="submit"
                                        variant="primary"
                                        :icon="saving ? 'fas fa-spinner fa-spin' : 'fas fa-save'"
                                        text="Lưu thay đổi"
                                        class="btn-sm text-xs font-weight-bold px-3 shadow-sm"
                                        :is-disabled="saving"
                                    />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </LTEContentWrapper>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/store/auth';
import LTEButton from '@/components/controls/LTEButton.vue';
import IconButton from '@/components/controls/IconButton.vue';

const authStore = useAuthStore();

const loading = ref(true);
const saving = ref(false);
const syncingApi = ref(false);

const syncApiData = async () => {
    syncingApi.value = true;
    try {
        const res = await axios.post(route('StudentPortalController.forceSyncStudentApi'));
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) {
                window.func.toastSuccess(res.data.message);
            } else {
                alert(res.data.message);
            }
            await fetchProfile();
        }
    } catch (err) {
        const msg = err.response?.data?.message || 'Có lỗi khi đồng bộ dữ liệu từ API Đào Tạo';
        if (window.func && window.func.toastError) {
            window.func.toastError(msg);
        } else {
            alert(msg);
        }
    } finally {
        syncingApi.value = false;
    }
};
const profile = ref({});
const attributes = ref([]);

const isEditable = (attr) => {
    return attr && attr.cho_phep_chinh_sua !== false && 
           attr.cho_phep_chinh_sua !== 0 && 
           attr.cho_phep_chinh_sua !== '0' && 
           attr.cho_phep_chinh_sua !== 'false';
};

const getColClass = (colSpan) => {
    const span = parseInt(colSpan || 6);
    if (span === 12) return 'col-12';
    if (span === 6) return 'col-12 col-md-6';
    if (span === 4) return 'col-12 col-md-4';
    if (span === 3) return 'col-12 col-md-3';
    return 'col-12 col-md-6';
};

const groupedAttributes = computed(() => {
    const groups = {};
    attributes.value.forEach(attr => {
        const groupName = attr.phan_nhom || 'Thông tin bổ sung';
        if (!groups[groupName]) {
            groups[groupName] = [];
        }
        groups[groupName].push(attr);
    });
    
    // Sort attributes inside each group by `thu_tu` asc, then `id` asc
    Object.keys(groups).forEach(key => {
        groups[key].sort((a, b) => (a.thu_tu || 0) - (b.thu_tu || 0) || a.id - b.id);
    });

    // Sort group order based on minimum thu_tu of fields within the group
    const sortedGroupKeys = Object.keys(groups).sort((g1, g2) => {
        const min1 = Math.min(...groups[g1].map(a => a.thu_tu || 0));
        const min2 = Math.min(...groups[g2].map(a => a.thu_tu || 0));
        return min1 - min2;
    });

    const sortedGroups = {};
    sortedGroupKeys.forEach(k => {
        sortedGroups[k] = groups[k];
    });
    
    return sortedGroups;
});

const parseOptions = (attr) => {
    if (attr.ref_options && attr.ref_options.length > 0) {
        return attr.ref_options.map(o => typeof o === 'object' ? { value: o.value, text: o.text } : { value: o, text: o });
    }
    if (attr.ma_truong === 'gioi_tinh' && !attr.lua_chon) {
        return [{ value: 'Nam', text: 'Nam' }, { value: 'Nữ', text: 'Nữ' }, { value: 'Khác', text: 'Khác' }];
    }
    try {
        if (!attr.lua_chon) return [];
        const parsed = JSON.parse(attr.lua_chon);
        return parsed.map(o => typeof o === 'object' ? { value: o.value || o.text, text: o.text || o.value } : { value: o, text: o });
    } catch (e) {
        if (typeof attr.lua_chon === 'string') {
            return attr.lua_chon.split(',').map(s => {
                const trimmed = s.trim();
                return { value: trimmed, text: trimmed };
            });
        }
        return [];
    }
};

const onFileSelected = (event, attr) => {
    const file = event.target.files[0];
    if (file) {
        attr.fileObj = file;
        attr.value = URL.createObjectURL(file);
    }
};

const clearFile = (attr) => {
    attr.fileObj = null;
    attr.value = '';
};

const getFileName = (urlStr) => {
    if (!urlStr) return '';
    const parts = urlStr.split('/');
    return parts[parts.length - 1];
};

const getFileIconClass = (urlStr) => {
    if (!urlStr) return 'fas fa-file';
    const ext = urlStr.split('.').pop().toLowerCase();
    if (['pdf'].includes(ext)) return 'fas fa-file-pdf text-danger';
    if (['doc', 'docx'].includes(ext)) return 'fas fa-file-word text-primary';
    if (['xls', 'xlsx'].includes(ext)) return 'fas fa-file-excel text-success';
    if (['zip', 'rar', '7z'].includes(ext)) return 'fas fa-file-archive text-warning';
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'fas fa-file-image text-info';
    return 'fas fa-file-alt text-secondary';
};

// Fetch user data and extended attributes
const fetchProfileData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('StudentPortalController.getProfileData'));
        if (response.data.status === 200) {
            profile.value = response.data.data.profile;
            attributes.value = response.data.data.attributes;
        }
    } catch (err) {
        console.error('Error loading profile:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError('Không thể tải thông tin cá nhân');
        }
    } finally {
        loading.value = false;
    }
};

// Save edited EAV values
const saveProfile = async () => {
    saving.value = true;
    try {
        const formData = new FormData();
        const payload = attributes.value.map(attr => {
            if (attr.fileObj) {
                formData.append(`file_${attr.id}`, attr.fileObj);
            }
            return {
                truong_id: attr.id,
                gia_tri: attr.value || ''
            };
        });
        
        formData.append('attributes', JSON.stringify(payload));

        const response = await axios.post(route('StudentPortalController.updateProfileData'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.data.status === 200) {
            if (window.func && window.func.toastSuccess) {
                window.func.toastSuccess('Cập nhật thông tin thành công!');
            }
            fetchProfileData();
        }
    } catch (err) {
        console.error('Error saving profile:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError('Cập nhật thông tin thất bại');
        }
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    fetchProfileData();
});
</script>

<style scoped>
/* Custom Premium Input Fields */
.custom-input {
    display: block;
    width: 100%;
    height: calc(1.5em + 1.25rem + 2px);
    padding: 0.625rem 1rem;
    font-size: 0.95rem;
    font-weight: 500;
    line-height: 1.5;
    color: #2d3748;
    background-color: #ffffff;
    background-clip: padding-box;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.custom-input:hover:not([readonly]) {
    border-color: #cbd5e0;
}

.custom-input:focus:not([readonly]) {
    color: #1a202c;
    background-color: #ffffff;
    border-color: #3182ce;
    outline: 0;
    box-shadow: 0 0 0 4px rgba(49, 130, 206, 0.15);
}

/* Custom Read-only Input Styling */
.custom-input[readonly] {
    background-color: #ffffff !important; /* Fixed background: white, no gray! */
    color: #718096 !important;
    border-color: #e2e8f0 !important;
    box-shadow: none !important;
    cursor: not-allowed;
}

/* Custom Select Styling */
.custom-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23718096' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1.25rem;
    padding-right: 2.5rem;
}
</style>
