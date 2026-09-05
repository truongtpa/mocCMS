<template>
    <LTEContentWrapper>
        <template #content>
            <div class="container-fluid p-0">
                <div class="bg-white rounded-xl border border-slate-200 p-3.5 mb-4 shadow-sm d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <IconButton 
                            variant="slate" 
                            icon="fas fa-arrow-left" 
                            title="Quay lại" 
                            @click="$router.push({ name: 'router-portal-doi-tuong-dong' })" 
                        />
                        <div>
                            <h4 class="font-weight-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 1.1rem;">
                                IMPORT DỮ LIỆU ĐỐI TƯỢNG
                            </h4>
                            <p class="text-muted text-xs mb-0 mt-0.5" v-if="currentType">
                                Loại đối tượng: <strong class="text-dark">{{ currentType.ten_loai }}</strong> (Mã: <code>{{ currentType.ma_loai }}</code>)
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label class="text-xs font-weight-bold text-slate-700 uppercase mb-0 d-none d-md-inline">Loại đối tượng:</label>
                        <LTESelect2Option
                            v-model="selectedTypeId"
                            :init-value="selectedTypeId"
                            :data="types.map(t => ({ value: t.id, text: `${t.ten_loai} (${t.records_count} bản ghi)` }))"
                            :multiple="false"
                            :close-on-select="true"
                            :allow-clear="false"
                            :enable-data-watch="true"
                            @update:model-value="onTypeSelectChange"
                            style="width: 220px;"
                        />
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-4" style="border: 1px solid #e2e8f0 !important; border-radius: 12px !important;">
                    <div class="card-header bg-white border-bottom py-2.5 px-4 d-flex align-items-center justify-content-between" style="display: flex !important; justify-content: space-between !important; align-items: center !important;">
                        <h6 class="font-weight-bold mb-0 text-dark text-xs uppercase tracking-wider d-flex align-items-center">
                            <i class="fas fa-cog text-secondary mr-2"></i> BƯỚC 1: CHỌN TỆP EXCEL & CẤU HÌNH QUY TẮC IMPORT
                        </h6>
                        <div class="ml-auto">
                            <LTEButton
                                variant="outline-primary"
                                icon="fas fa-download"
                                text="Tải File Mẫu Excel (.xlsx)"
                                class="btn-sm text-xs font-weight-bold px-2.5 shadow-sm"
                                @click="downloadTemplate"
                            />
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">1. Chọn tệp dữ liệu (Excel / CSV)</label>
                                <div 
                                    class="border-2 border-dashed border-slate-300 hover:border-slate-400 rounded-xl p-4 text-center bg-slate-50/50 hover:bg-slate-100/50 transition-all cursor-pointer relative d-flex flex-column align-items-center justify-content-center"
                                    style="min-height: 160px;"
                                    @click="$refs.fileInput.click()"
                                >
                                    <input ref="fileInput" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="onFileSelect" />
                                    <div v-if="!selectedFile" class="py-2">
                                        <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 d-flex align-items-center justify-content-center mx-auto mb-2 text-lg">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <h6 class="font-weight-bold text-dark text-sm mb-1">Kéo thả tệp vào đây hoặc nhấp để chọn</h6>
                                        <p class="text-xs text-muted mb-0">Hỗ trợ: <code>.xlsx</code>, <code>.xls</code>, <code>.csv</code> (Tối đa 10MB)</p>
                                    </div>
                                    <div v-else class="d-flex align-items-center gap-3 py-2">
                                        <i class="fas fa-file-excel text-secondary" style="font-size: 2.2rem;"></i>
                                        <div class="text-left">
                                            <h6 class="font-weight-bold text-dark mb-0 text-sm">{{ selectedFile.name }}</h6>
                                            <span class="text-xs text-muted">{{ (selectedFile.size / 1024).toFixed(1) }} KB</span>
                                        </div>
                                        <button @click.stop="selectedFile = null" class="ml-3 text-slate-400 hover:text-slate-700 p-1 border-0 bg-transparent">
                                            <i class="fas fa-times text-slate-500"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">2. Quy tắc xử lý khi trùng mã đối tượng</label>
                                <div class="space-y-2">
                                    <label :class="['border p-2.5 rounded-xl cursor-pointer transition-all d-flex align-items-start gap-2.5 mb-0', importMode === 'upsert' ? 'bg-slate-100 border-slate-400' : 'bg-white border-slate-200']">
                                        <input type="radio" v-model="importMode" value="upsert" @change="onModeChange" class="mt-1" />
                                        <div>
                                            <span class="font-weight-bold text-dark text-xs block">Thêm mới & Cập nhật (Upsert - Khuyên dùng)</span>
                                            <span class="text-[11px] text-muted block leading-tight mt-0.5">Tự động thêm mới nếu chưa có trong DB, cập nhật dữ liệu nếu mã đã tồn tại.</span>
                                        </div>
                                    </label>

                                    <label :class="['border p-2.5 rounded-xl cursor-pointer transition-all d-flex align-items-start gap-2.5 mb-0', importMode === 'insert_new' ? 'bg-slate-100 border-slate-400' : 'bg-white border-slate-200']">
                                        <input type="radio" v-model="importMode" value="insert_new" @change="onModeChange" class="mt-1" />
                                        <div>
                                            <span class="font-weight-bold text-dark text-xs block">Chỉ Thêm mới (Skip Existing)</span>
                                            <span class="text-[11px] text-muted block leading-tight mt-0.5">Chỉ thêm các bản ghi mới, tự động bỏ qua nếu mã đã tồn tại trong DB.</span>
                                        </div>
                                    </label>

                                    <label :class="['border p-2.5 rounded-xl cursor-pointer transition-all d-flex align-items-start gap-2.5 mb-0', importMode === 'update_existing' ? 'bg-slate-100 border-slate-400' : 'bg-white border-slate-200']">
                                        <input type="radio" v-model="importMode" value="update_existing" @change="onModeChange" class="mt-1" />
                                        <div>
                                            <span class="font-weight-bold text-dark text-xs block">Chỉ Cập nhật (Update Only)</span>
                                            <span class="text-[11px] text-muted block leading-tight mt-0.5">Cập nhật dữ liệu cho các mã đã tồn tại, bỏ qua nếu chưa có trong DB.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <LTEButton
                                variant="primary"
                                :icon="previewLoading ? 'fas fa-spinner fa-spin' : 'fas fa-search'"
                                :text="previewLoading ? 'Đang đọc tệp...' : 'Kiểm tra dữ liệu xem trước'"
                                class="btn-sm text-xs font-weight-bold px-3 shadow-sm"
                                :is-disabled="!selectedFile || previewLoading"
                                @click="runPreviewImport"
                            />
                        </div>
                    </div>
                </div>

                <div v-if="previewData.total_rows > 0" class="card border-0 shadow-sm rounded-xl overflow-hidden mb-4" style="border: 1px solid #e2e8f0 !important; border-radius: 12px !important;">
                    <div class="card-header bg-white border-bottom py-2.5 px-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <h6 class="font-weight-bold mb-0 text-dark text-xs uppercase tracking-wider d-flex align-items-center">
                                <i class="fas fa-list text-secondary mr-2"></i> BƯỚC 2: KẾT QUẢ PHÂN TÍCH XEM TRƯỚC
                            </h6>

                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge badge-light border px-2.5 py-1.5 rounded font-weight-bold text-xs">
                                    Tổng: {{ previewData.total_rows }}
                                </span>
                                <span class="badge badge-primary px-2.5 py-1.5 rounded font-weight-bold text-xs">
                                    Thêm mới: {{ previewData.new_count }}
                                </span>
                                <span class="badge badge-success px-2.5 py-1.5 rounded font-weight-bold text-xs">
                                    Cập nhật: {{ previewData.update_count }}
                                </span>
                                <span class="badge badge-warning px-2.5 py-1.5 rounded font-weight-bold text-xs">
                                    Bỏ qua: {{ previewData.skip_count || 0 }}
                                </span>
                                <span class="badge badge-danger px-2.5 py-1.5 rounded font-weight-bold text-xs">
                                    Lỗi: {{ previewData.error_count }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                            <div class="btn-group btn-group-toggle">
                                <button 
                                    @click="activeFilter = 'all'" 
                                    :class="['btn btn-xs px-3 font-weight-bold', activeFilter === 'all' ? 'btn-dark' : 'btn-outline-secondary']"
                                >
                                    Tất cả ({{ previewData.total_rows }})
                                </button>
                                <button 
                                    @click="activeFilter = 'new'" 
                                    :class="['btn btn-xs px-3 font-weight-bold', activeFilter === 'new' ? 'btn-primary' : 'btn-outline-secondary']"
                                >
                                    Thêm mới ({{ previewData.new_count }})
                                </button>
                                <button 
                                    @click="activeFilter = 'update'" 
                                    :class="['btn btn-xs px-3 font-weight-bold', activeFilter === 'update' ? 'btn-success' : 'btn-outline-secondary']"
                                >
                                    Cập nhật ({{ previewData.update_count }})
                                </button>
                                <button 
                                    @click="activeFilter = 'error'" 
                                    :class="['btn btn-xs px-3 font-weight-bold', activeFilter === 'error' ? 'btn-danger' : 'btn-outline-secondary']"
                                >
                                    Lỗi / Bỏ qua ({{ (previewData.error_count || 0) + (previewData.skip_count || 0) }})
                                </button>
                            </div>

                            <div v-if="previewData.error_count > 0" class="d-flex align-items-center gap-2 bg-light px-3 py-1.5 rounded border">
                                <input type="checkbox" id="auto_clear_err" v-model="autoClearErrors" class="w-4 h-4 cursor-pointer" />
                                <label for="auto_clear_err" class="text-xs font-bold text-dark mb-0 cursor-pointer">
                                    Tự động bỏ trống các ô lỗi thuộc tính & vẫn import hàng đó
                                </label>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-lg">
                            <table class="table table-hover table-striped app-table mb-0 text-xs" style="white-space: nowrap;">
                                <thead style="background-color: #007bff !important;">
                                    <tr>
                                        <th style="width: 45px; white-space: nowrap;" class="py-2 px-3 text-white font-weight-bold text-center">#</th>
                                        <th style="width: 120px; white-space: nowrap;" class="py-2 px-3 text-white font-weight-bold text-center">Trạng thái</th>
                                        <th style="white-space: nowrap;" class="py-2 px-3 text-white font-weight-bold">Mã đối tượng</th>
                                        <th style="white-space: nowrap;" class="py-2 px-3 text-white font-weight-bold">Tên hiển thị</th>
                                        <th v-if="currentType && ['giang_vien', 'sinh_vien'].includes(currentType.ma_loai)" style="white-space: nowrap;" class="py-2 px-3 text-white font-weight-bold">Email</th>
                                        <th v-for="f in previewData.fields" :key="f.id" style="white-space: nowrap;" class="py-2 px-3 text-white font-weight-bold">{{ f.ten_truong }}</th>
                                        <th style="white-space: nowrap; min-width: 180px;" class="py-2 px-3 text-white font-weight-bold">Ghi chú / Lỗi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr 
                                        v-for="r in filteredRows" 
                                        :key="r.row_index" 
                                        :class="r.action_type === 'error' ? 'bg-light' : ''"
                                    >
                                        <td class="text-center font-weight-bold text-muted py-2 px-3">{{ r.row_index }}</td>
                                        <td class="text-center py-2 px-3">
                                            <AppBadge v-if="r.action_type === 'new'" variant="primary">Thêm mới</AppBadge>
                                            <AppBadge v-else-if="r.action_type === 'update'" variant="success">Cập nhật</AppBadge>
                                            <AppBadge v-else-if="r.action_type === 'skip'" variant="secondary">Bỏ qua</AppBadge>
                                            <AppBadge v-else variant="danger">Lỗi</AppBadge>
                                        </td>
                                        <td class="font-weight-bold text-dark py-2 px-3">{{ r.ma_doi_tuong || '-' }}</td>
                                        <td class="font-weight-bold py-2 px-3">{{ r.ten_hien_thi || '-' }}</td>
                                        <td v-if="currentType && ['giang_vien', 'sinh_vien'].includes(currentType.ma_loai)" class="py-2 px-3">{{ r.email || '-' }}</td>
                                        <td v-for="f in previewData.fields" :key="f.id" class="py-2 px-3">
                                            <span>{{ r.attributes[f.ma_truong] !== undefined && r.attributes[f.ma_truong] !== '' ? r.attributes[f.ma_truong] : '-' }}</span>
                                        </td>
                                        <td class="py-2 px-3" style="white-space: normal; min-width: 200px;">
                                            <div v-if="r.errors && r.errors.length > 0" class="text-danger font-weight-bold text-xs">
                                                <div v-for="(err, i) in r.errors" :key="i">• {{ err }}</div>
                                            </div>
                                            <span v-else-if="r.action_type === 'skip'" class="text-muted text-xs">Bỏ qua theo quy tắc</span>
                                            <span v-else class="text-success font-weight-bold text-xs"><i class="fas fa-check mr-1"></i> Hợp lệ</span>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredRows.length === 0">
                                        <td :colspan="6 + (previewData.fields ? previewData.fields.length : 0)" class="text-center py-4 text-muted">
                                            Không có hàng dữ liệu nào khớp với bộ lọc.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 d-flex align-items-center justify-content-between">
                        <LTEButton 
                            variant="outline-secondary" 
                            icon="fas fa-times" 
                            text="Hủy thao tác" 
                            class="btn-sm text-xs font-weight-bold px-2.5" 
                            @click="$router.push({ name: 'router-portal-doi-tuong-dong' })" 
                        />

                        <div class="d-flex align-items-center gap-2">
                            <LTEButton
                                variant="light"
                                icon="fas fa-sync-alt"
                                text="Tải lại xem trước"
                                class="btn-sm text-xs font-weight-bold px-3 border"
                                @click="runPreviewImport"
                            />
                            <LTEButton
                                variant="success"
                                :icon="processLoading ? 'fas fa-spinner fa-spin' : 'fas fa-check'"
                                :text="processLoading ? 'Đang thực hiện...' : `XÁC NHẬN IMPORT (${previewData.new_count + previewData.update_count} bản ghi)`"
                                class="btn-sm text-xs font-weight-bold px-3 shadow-sm"
                                :is-disabled="processLoading || (previewData.new_count === 0 && previewData.update_count === 0)"
                                @click="executeProcessImport"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </LTEContentWrapper>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '@/store/auth'
import LTESelect2Option from '@/components/controls/LTESelect2Option.vue'
import LTEButton from '@/components/controls/LTEButton.vue'
import IconButton from '@/components/controls/IconButton.vue'

export default {
    name: 'DynamicObjectImport',
    components: { LTESelect2Option, LTEButton, IconButton },
    data() {
        return {
            types: [],
            selectedTypeId: null,
            currentType: null,
            selectedFile: null,
            importMode: 'upsert', // upsert | insert_new | update_existing
            autoClearErrors: false,
            previewLoading: false,
            processLoading: false,
            activeFilter: 'all', // all | new | update | error
            previewData: {
                total_rows: 0,
                new_count: 0,
                update_count: 0,
                skip_count: 0,
                error_count: 0,
                fields: [],
                rows: []
            }
        }
    },
    computed: {
        filteredRows() {
            if (!this.previewData.rows) return [];
            if (this.activeFilter === 'new') return this.previewData.rows.filter(r => r.action_type === 'new');
            if (this.activeFilter === 'update') return this.previewData.rows.filter(r => r.action_type === 'update');
            if (this.activeFilter === 'error') return this.previewData.rows.filter(r => r.action_type === 'error' || r.action_type === 'skip');
            return this.previewData.rows;
        }
    },
    mounted() {
        this.fetchTypes();
    },
    methods: {
        async fetchTypes() {
            try {
                const response = await axios.get(route('DynamicObjectController.getTypes'));
                if (response.data.status === 200) {
                    this.types = response.data.data;
                    const queryTypeId = this.$route.query.loai_doi_tuong_id;
                    if (queryTypeId) {
                        this.selectedTypeId = parseInt(queryTypeId);
                    } else if (this.types.length > 0) {
                        this.selectedTypeId = this.types[0].id;
                    }
                    this.updateCurrentType();
                }
            } catch (err) {
                console.error('Error fetching types:', err);
            }
        },

        onTypeSelectChange() {
            this.updateCurrentType();
            this.previewData = { total_rows: 0, new_count: 0, update_count: 0, skip_count: 0, error_count: 0, fields: [], rows: [] };
        },

        updateCurrentType() {
            this.currentType = this.types.find(t => t.id === this.selectedTypeId) || null;
        },

        downloadTemplate() {
            if (!this.selectedTypeId) return;
            const url = route('DynamicObjectController.exportImportTemplate', { loai_doi_tuong_id: this.selectedTypeId });
            window.open(url, '_blank');
        },

        onFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file;
            }
        },

        onModeChange() {
            if (this.selectedFile && this.previewData.total_rows > 0) {
                this.runPreviewImport();
            }
        },

        async runPreviewImport() {
            if (!this.selectedFile) {
                if (window.func && window.func.toastError) {
                    window.func.toastError('Vui lòng chọn tệp Excel hoặc CSV để import!');
                }
                return;
            }
            this.previewLoading = true;
            try {
                const formData = new FormData();
                formData.append('file', this.selectedFile);
                formData.append('loai_doi_tuong_id', this.selectedTypeId);
                formData.append('mode', this.importMode);

                const response = await axios.post(route('DynamicObjectController.putPreviewImport'), formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (response.data.status === 200) {
                    this.previewData = response.data.data;
                }
            } catch (err) {
                console.error('Error previewing import:', err);
                const msg = err.response?.data?.message || 'Không thể đọc dữ liệu từ tệp import!';
                if (window.func && window.func.toastError) {
                    window.func.toastError(msg);
                }
            } finally {
                this.previewLoading = false;
            }
        },

        async executeProcessImport() {
            if (!this.previewData.rows || this.previewData.rows.length === 0) return;
            
            this.processLoading = true;
            try {
                const payload = {
                    loai_doi_tuong_id: this.selectedTypeId,
                    mode: this.importMode,
                    auto_clear_errors: this.autoClearErrors,
                    rows: this.previewData.rows
                };

                const response = await axios.post(route('DynamicObjectController.putProcessImport'), payload);
                if (response.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(response.data.message);
                    }
                    this.$router.push({
                        name: 'router-portal-doi-tuong-dong',
                        query: { loai_doi_tuong_id: this.selectedTypeId, tab: 'records' }
                    });
                }
            } catch (err) {
                console.error('Error processing import:', err);
                const msg = err.response?.data?.message || 'Xử lý Import thất bại!';
                if (window.func && window.func.toastError) {
                    window.func.toastError(msg);
                }
            } finally {
                this.processLoading = false;
            }
        }
    }
}
</script>
