<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="row m-0">
                <div class="col-12 p-0">
                    <div class="card border-0" style="border: 1px solid #cbd5e1 !important; border-radius: 8px !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important; overflow: hidden; background: #ffffff;">
                        <div class="card-header py-2 px-3 bg-light border-bottom d-flex align-items-center justify-content-between" style="background-color: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; display: flex !important; justify-content: space-between !important; align-items: center !important;">
                            <h6 class="font-weight-bold mb-0 text-xs text-uppercase tracking-wider text-dark d-flex align-items-center">
                                <i class="fas fa-key mr-2 text-primary"></i>
                                QUẢN LÝ API KEY
                            </h6>
                            <div class="ml-auto">
                                <LTEButton 
                                    v-if="authStore.hasPermission('ApiKeyController.putApiKey')"
                                    variant="success"
                                    icon="fas fa-plus"
                                    text="Tạo API Key mới"
                                    class="btn-sm text-xs font-weight-bold px-2.5 shadow-sm"
                                    @click="themMoi"
                                />
                            </div>
                        </div>

                        <div class="card-body p-2.5">
                            <div v-if="loading" class="text-center py-4">
                                <LoadingSpinner />
                                <p class="text-muted mt-2 text-xs">Đang tải danh sách API Key...</p>
                            </div>

                            <AppTable v-else>
                                <template #thead>
                                    <thead style="background-color: #007bff !important;">
                                        <tr>
                                            <th style="width: 45px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">#</th>
                                            <th style="min-width: 180px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">Tên ứng dụng</th>
                                            <th style="min-width: 280px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">API Key</th>
                                            <th style="min-width: 200px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">Mô tả</th>
                                            <th style="width: 120px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Trạng thái</th>
                                            <th style="width: 150px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">Ngày tạo</th>
                                            <th style="width: 100px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Thao tác</th>
                                        </tr>
                                    </thead>
                                </template>

                                <template #tbody>
                                    <tbody>
                                        <tr v-if="listApiKey.length === 0">
                                            <td colspan="7" class="text-center py-3 text-muted text-xs">Chưa có API Key nào được tạo.</td>
                                        </tr>
                                        <tr v-for="(item, idx) in listApiKey" :key="item.id">
                                            <td class="py-1.5 px-2 text-xs text-muted align-middle">{{ idx + 1 }}</td>
                                            <td class="py-1.5 px-2 text-xs align-middle font-weight-bold text-dark">{{ item.ten_ung_dung }}</td>
                                            <td class="py-1.5 px-2 text-xs align-middle">
                                                <div class="d-flex align-items-center">
                                                    <code class="font-mono bg-light px-2 py-1 rounded text-primary text-xs mr-2">{{ item.api_key }}</code>
                                                    <button class="btn btn-xs btn-outline-secondary" title="Sao chép" @click="copyKey(item.api_key)">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-1.5 px-2 text-xs align-middle text-muted">{{ item.mo_ta || '-' }}</td>
                                            <td class="py-1.5 px-2 text-xs text-center align-middle">
                                                <span v-if="item.trang_thai == 1" class="badge badge-success px-2 py-1">Hoạt động</span>
                                                <span v-else class="badge badge-secondary px-2 py-1">Khóa</span>
                                            </td>
                                            <td class="py-1.5 px-2 text-xs align-middle text-muted">{{ formatDate(item.ngay_tao) }}</td>
                                            <td class="py-1.5 px-2 text-xs text-center align-middle">
                                                <i v-if="authStore.hasPermission('ApiKeyController.putApiKey')" @click="chinhSua(item)" class="fas fa-edit icon-edit mr-2" style="cursor: pointer;" title="Sửa"></i>
                                                <i v-if="authStore.hasPermission('ApiKeyController.deleteApiKey')" @click="xoaApiKey(item)" class="fas fa-trash icon-delete" style="cursor: pointer;" title="Xóa"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </AppTable>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </LTEContentWrapper>

    <LTEModal ref="mdApiKey">
        <LTEInput 
            v-model="apiKeyForm.ten_ung_dung" 
            label="Tên ứng dụng / Đơn vị tích hợp *" 
            placeholder="Ví dụ: App Mobile Sinh Vien, Cong Dao Tao..." 
            input-class="form-control form-control-sm text-xs" 
            class="mb-3"
            required
        />

        <LTETextArea 
            v-model="apiKeyForm.mo_ta" 
            label="Mô tả mục đích" 
            placeholder="Mô tả ngắn gọn về ứng dụng kết nối..." 
            textarea-class="form-control form-control-sm text-xs" 
            :rows="3"
            class="mb-3"
        />

        <LTESelect2Option 
            v-model="apiKeyForm.trang_thai"
            :init-value="apiKeyForm.trang_thai"
            label="Trạng thái" 
            placeholder="Chọn trạng thái..." 
            :data="[
                { value: 1, text: 'Hoạt động' },
                { value: 0, text: 'Tắt / Khóa' }
            ]" 
            :multiple="false"
            :close-on-select="true"
            :allow-clear="false"
            :enable-data-watch="true"
            class="mb-3"
        />
    </LTEModal>

    <YesNoModal ref="confirmModal" />
</template>

<script>
import LTEContentWrapper from '@/components/controls/LTEContentWrapper.vue'
import AppTable from '@/components/controls/AppTable.vue'
import LTEButton from '@/components/controls/LTEButton.vue'
import LTEInput from '@/components/controls/LTEInput.vue'
import LTETextArea from '@/components/controls/LTETextArea.vue'
import LTESelect2Option from '@/components/controls/LTESelect2Option.vue'
import LTEModal from '@/components/controls/LTEModal.vue'
import YesNoModal from '@/components/controls/YesNoModal.vue'
import LoadingSpinner from '@/components/controls/LoadingSpinner.vue'
import { useAuthStore } from '@/store/auth'

export default {
    name: 'ApiKeyPage',
    components: {
        LTEContentWrapper,
        AppTable,
        LTEButton,
        LTEInput,
        LTETextArea,
        LTESelect2Option,
        LTEModal,
        YesNoModal,
        LoadingSpinner
    },
    data() {
        return {
            authStore: useAuthStore(),
            loading: false,
            listApiKey: [],
            apiKeyForm: {
                id: null,
                ten_ung_dung: '',
                mo_ta: '',
                trang_thai: 1
            }
        }
    },
    mounted() {
        this.getDsApiKey()
    },
    methods: {
        getDsApiKey() {
            this.loading = true
            this.$axios.get(route('ApiKeyController.getDsApiKey')).then((res) => {
                this.loading = false
                if (res.data.status === 200) {
                    this.listApiKey = res.data.data
                } else {
                    this.$func.toastError(res.data)
                }
            })
        },
        async themMoi() {
            this.apiKeyForm = { id: null, ten_ung_dung: '', mo_ta: '', trang_thai: 1 }
            this.$refs.mdApiKey.$data.title = 'Tạo API Key mới'
            this.$refs.mdApiKey.$data.save = 'Lưu thông tin'
            const res = await this.$refs.mdApiKey.openModal()
            if (!res) return
            await this.putApiKey()
        },
        async chinhSua(item) {
            this.apiKeyForm = {
                id: item.id,
                ten_ung_dung: item.ten_ung_dung,
                mo_ta: item.mo_ta || '',
                trang_thai: item.trang_thai
            }
            this.$refs.mdApiKey.$data.title = 'Cập nhật API Key'
            this.$refs.mdApiKey.$data.save = 'Lưu thay đổi'
            const res = await this.$refs.mdApiKey.openModal()
            if (!res) return
            await this.putApiKey()
        },
        async putApiKey() {
            await this.$axios.post(route('ApiKeyController.putApiKey'), this.apiKeyForm).then((res) => {
                if (res.data.status === 200) {
                    this.$func.toastSuccess(res.data.message)
                    this.getDsApiKey()
                    this.$refs.mdApiKey.closeModal()
                } else {
                    this.$func.toastError(res.data)
                }
            })
        },
        async xoaApiKey(item) {
            const confirmed = await this.$refs.confirmModal.openModal('Bạn có chắc chắn muốn xóa API Key này?')
            if (!confirmed) return

            this.$axios.delete(route('ApiKeyController.deleteApiKey', { id: item.id })).then((res) => {
                if (res.data.status === 200) {
                    this.$func.toastSuccess(res.data.message)
                    this.getDsApiKey()
                } else {
                    this.$func.toastError(res.data)
                }
            })
        },
        copyKey(key) {
            navigator.clipboard.writeText(key)
            this.$func.toastSuccess('Đã sao chép API Key vào bộ nhớ tạm')
        },
        formatDate(dateStr) {
            if (!dateStr) return '-'
            return this.$func ? this.$func.formatDate(dateStr) : dateStr
        }
    }
}
</script>

<style scoped>
.icon-edit {
    color: #f59e0b;
}
.icon-delete {
    color: #ef4444;
}
</style>
