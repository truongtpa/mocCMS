<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="row justify-content-center">
                <div class="col-12 px-2">
                    <div class="card border-0" style="border: 1px solid #cbd5e1 !important; border-radius: 8px !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important; overflow: hidden; background: #ffffff;">
                        <!-- Light Mode Card Header -->
                        <div class="card-header text-center py-2.5 px-3 bg-light border-bottom" style="background-color: #f8fafc !important; border-bottom: 2px solid #e2e8f0 !important;">
                            <h4 class="card-title float-none font-weight-bold mb-0 text-md text-dark">
                                <i class="fas fa-user-shield mr-2 text-primary"></i>
                                QUẢN LÝ VÀ PHÂN QUYỀN HỆ THỐNG (RBAC)
                            </h4>
                        </div>

                        <div class="card-body p-2.5">
                            <!-- Nav Tabs -->
                            <ul class="nav nav-tabs custom-tabs mb-2" id="rbacTab" role="tablist">
                                <li class="nav-item">
                                    <button 
                                        class="nav-link font-weight-bold py-1.5 px-3 text-xs" 
                                        :class="{ active: activeTab === 'matrix' }" 
                                        @click="switchTab('matrix')"
                                    >
                                        <i class="fas fa-th-list mr-1"></i> Ma trận Phân quyền
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button 
                                        class="nav-link font-weight-bold py-1.5 px-3 text-xs" 
                                        :class="{ active: activeTab === 'roles' }" 
                                        @click="switchTab('roles')"
                                    >
                                        <i class="fas fa-user-tag mr-1"></i> Danh sách Vai trò
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button 
                                        class="nav-link font-weight-bold py-1.5 px-3 text-xs" 
                                        :class="{ active: activeTab === 'permissions' }" 
                                        @click="switchTab('permissions')"
                                    >
                                        <i class="fas fa-key mr-1"></i> Danh sách Quyền hạn
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button 
                                        class="nav-link font-weight-bold py-1.5 px-3 text-xs" 
                                        :class="{ active: activeTab === 'users' }" 
                                        @click="switchTab('users')"
                                    >
                                        <i class="fas fa-users-cog mr-1"></i> Gán Vai trò Người dùng
                                    </button>
                                </li>
                            </ul>

                            <!-- TAB 1: MA TRẬN PHÂN QUYỀN -->
                            <div v-if="activeTab === 'matrix'">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                        <!-- <i class="fas fa-sliders-h text-primary mr-1"></i> Ma trận Phân quyền theo Nhóm Controller -->
                                    </h6>
                                    <button class="btn btn-outline-primary btn-sm px-2.5 shadow-sm" @click="fetchMatrix">
                                        <i class="fas fa-sync-alt mr-1"></i> Tải lại ma trận
                                    </button>
                                </div>

                                <div v-if="loadingMatrix" class="text-center py-4">
                                    <LoadingSpinner />
                                    <p class="mt-2 text-muted text-xs">Đang tải dữ liệu ma trận phân quyền...</p>
                                </div>

                                <div v-else class="table-responsive border rounded" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                                    <table class="table table-bordered table-hover align-middle mb-0 position-relative table-sm">
                                        <!-- Vibrant Header with High Contrast White Text -->
                                        <thead class="sticky-top" style="z-index: 10; background: linear-gradient(135deg, #1e4068, #162447); color: #ffffff;">
                                            <tr>
                                                <th style="min-width: 280px;" class="align-middle py-2.5 px-3 text-white font-weight-bold border-0">TÊN QUYỀN</th>
                                                <th 
                                                    v-for="role in matrixData.ds_vai_tro" 
                                                    :key="role.id" 
                                                    class="text-center align-middle py-2.5 px-2 text-white border-0"
                                                    style="min-width: 130px;"
                                                >
                                                    <span 
                                                        class="badge px-2 py-1 font-weight-bold text-xs shadow-sm"
                                                        :class="getRoleBadgeClass(role.ma_vai_tro)"
                                                    >
                                                        {{ role.ten_vai_tro }}
                                                    </span>
                                                    <br>
                                                    <!-- <small class="text-white-50 text-xs font-weight-bold" style="color: rgba(255, 255, 255, 0.85) !important;">({{ role.ma_vai_tro }})</small> -->
                                                    <!-- Clean Check all column checkbox -->
                                                    <div class="mt-1 d-flex align-items-center justify-content-center">
                                                        <input 
                                                            type="checkbox" 
                                                            class="form-check-input position-static m-0"
                                                            style="width: 16px; height: 16px; cursor: pointer;"
                                                            :checked="isAllRoleChecked(role.id)"
                                                            :disabled="role.ma_vai_tro === 'admin'"
                                                            :title="'Tích/bỏ tất cả quyền cho vai trò ' + role.ten_vai_tro"
                                                            @change="toggleAllRolePermission(role.id, $event.target.checked)"
                                                        />
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="(groupPerms, ctrlName) in groupedMatrixPermissions" :key="ctrlName">
                                                <!-- Light Mode Group Subheader Row -->
                                                <tr class="group-header-row" style="background-color: #eff6ff; border-left: 4px solid #2563eb;">
                                                    <td class="py-1.5 px-3">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                <i class="fas fa-layer-group text-primary mr-1.5"></i>
                                                                <span class="font-weight-bold text-primary text-xs text-uppercase tracking-wide">{{ getControllerLabel(ctrlName) }}</span>
                                                                <span class="text-muted text-xs ml-1.5 font-weight-normal">({{ ctrlName }})</span>
                                                            </div>
                                                            <button 
                                                                class="btn btn-xs btn-link text-primary p-0 ml-2" 
                                                                title="Đổi tên nhãn mô tả nhóm này"
                                                                @click="editGroupLabel(ctrlName)"
                                                            >
                                                                <i class="fas fa-pencil-alt text-xs"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td 
                                                        v-for="role in matrixData.ds_vai_tro" 
                                                        :key="role.id" 
                                                        class="text-center align-middle py-1"
                                                        style="background-color: #f1f5f9;"
                                                    >
                                                        <!-- Clean Checkbox only without text -->
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <input 
                                                                type="checkbox" 
                                                                class="form-check-input position-static m-0"
                                                                style="width: 16px; height: 16px; cursor: pointer;"
                                                                :checked="isGroupCheckedForRole(ctrlName, role.id)"
                                                                :disabled="role.ma_vai_tro === 'admin'"
                                                                :title="'Tích tất cả quyền nhóm ' + getControllerLabel(ctrlName) + ' cho vai trò ' + role.ten_vai_tro"
                                                                @change="toggleGroupPermission(ctrlName, role.id, $event.target.checked)"
                                                            />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!-- Permissions in Group -->
                                                <tr v-for="perm in groupPerms" :key="perm.id">
                                                    <td class="py-1 px-3">
                                                        <div class="font-weight-bold text-dark text-xs">{{ perm.ten_quyen }}</div>
                                                        <code class="small text-primary" style="font-size: 0.75rem;">{{ perm.ma_quyen }}</code>
                                                    </td>
                                                    <td 
                                                        v-for="role in matrixData.ds_vai_tro" 
                                                        :key="role.id" 
                                                        class="text-center align-middle py-1"
                                                    >
                                                        <input 
                                                            type="checkbox" 
                                                            class="form-check-input position-static m-0"
                                                            style="width: 18px; height: 18px; cursor: pointer;"
                                                            :checked="isRoleHasPermission(role.id, perm.id)"
                                                            :disabled="role.ma_vai_tro === 'admin'"
                                                            @change="togglePermission(role.id, perm.id, $event.target.checked)"
                                                        />
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- TAB 2: QUẢN LÝ VAI TRÒ -->
                            <div v-if="activeTab === 'roles'">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold text-dark mb-0 text-sm">Danh sách Vai trò Hệ thống</h6>
                                    <button 
                                        v-if="authStore.hasPermission('PhanQuyenController.luuVaiTro')"
                                        class="btn btn-success btn-sm px-2.5 shadow-sm" 
                                        @click="openAddRoleModal"
                                    >
                                        <i class="fas fa-plus mr-1"></i> Thêm Vai trò mới
                                    </button>
                                </div>

                                <div v-if="loadingRoles" class="text-center py-4">
                                    <LoadingSpinner />
                                    <p class="mt-2 text-muted text-xs">Đang tải danh sách vai trò...</p>
                                </div>

                                <div v-else class="table-responsive border rounded" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead class="bg-light text-dark sticky-top border-bottom">
                                            <tr>
                                                <th style="width: 5%;" class="py-2 px-2">#</th>
                                                <th style="width: 30%;" class="py-2 px-2">Mã Vai trò</th>
                                                <th style="width: 45%;" class="py-2 px-2">Tên Vai trò</th>
                                                <th style="width: 20%;" class="text-center py-2 px-2">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(role, idx) in rolesList" :key="role.id">
                                                <td class="py-1.5 px-2 text-xs">{{ idx + 1 }}</td>
                                                <td class="py-1.5 px-2"><span class="badge badge-secondary px-2 py-1 text-xs">{{ role.ma_vai_tro }}</span></td>
                                                <td class="font-weight-bold text-dark py-1.5 px-2 text-xs">{{ role.ten_vai_tro }}</td>
                                                <td class="text-center py-1.5 px-2">
                                                    <button 
                                                        v-if="authStore.hasPermission('PhanQuyenController.luuVaiTro')"
                                                        class="btn btn-xs btn-outline-primary mr-1 px-2 py-0.5" 
                                                        @click="openEditRoleModal(role)"
                                                    >
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </button>
                                                    <button 
                                                        v-if="authStore.hasPermission('PhanQuyenController.xoaVaiTro')"
                                                        class="btn btn-xs btn-outline-danger px-2 py-0.5" 
                                                        :disabled="role.ma_vai_tro === 'admin'"
                                                        @click="deleteRole(role)"
                                                    >
                                                        <i class="fas fa-trash-alt"></i> Xóa
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- TAB 3: QUẢN LÝ QUYỀN HẠN -->
                            <div v-if="activeTab === 'permissions'">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold text-dark mb-0 text-sm">Danh sách Quyền hạn theo Controller</h6>
                                    <button 
                                        v-if="authStore.hasPermission('PhanQuyenController.luuQuyen')"
                                        class="btn btn-success btn-sm px-2.5 shadow-sm" 
                                        @click="openAddPermissionModal"
                                    >
                                        <i class="fas fa-plus mr-1"></i> Thêm Quyền hạn mới
                                    </button>
                                </div>

                                <div v-if="loadingPermissions" class="text-center py-4">
                                    <LoadingSpinner />
                                    <p class="mt-2 text-muted text-xs">Đang tải danh sách quyền hạn...</p>
                                </div>

                                <div v-else class="table-responsive border rounded" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead class="bg-light text-dark sticky-top border-bottom">
                                            <tr>
                                                <th style="width: 5%;" class="py-2 px-2">#</th>
                                                <th style="width: 40%;" class="py-2 px-2">Mã Quyền (Permission Key)</th>
                                                <th style="width: 40%;" class="py-2 px-2">Tên Quyền Mô Tả</th>
                                                <th style="width: 15%;" class="text-center py-2 px-2">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="(groupPerms, ctrlName) in groupedListPermissions" :key="ctrlName">
                                                <tr style="background-color: #eff6ff; border-left: 4px solid #2563eb;">
                                                    <td colspan="4" class="py-1 px-3 text-primary font-weight-bold text-xs">
                                                        <i class="fas fa-cubes mr-1.5"></i> {{ getControllerLabel(ctrlName) }} ({{ ctrlName }})
                                                    </td>
                                                </tr>
                                                <tr v-for="(perm, idx) in groupPerms" :key="perm.id">
                                                    <td class="py-1 px-2 text-xs">{{ idx + 1 }}</td>
                                                    <td class="py-1 px-2"><code style="font-size: 0.78rem;">{{ perm.ma_quyen }}</code></td>
                                                    <td class="font-weight-bold text-dark py-1 px-2 text-xs">{{ perm.ten_quyen }}</td>
                                                    <td class="text-center py-1 px-2">
                                                        <button 
                                                            v-if="authStore.hasPermission('PhanQuyenController.luuQuyen')"
                                                            class="btn btn-xs btn-outline-primary mr-1 px-2 py-0.5" 
                                                            @click="openEditPermissionModal(perm)"
                                                        >
                                                            <i class="fas fa-edit"></i> Sửa
                                                        </button>
                                                        <button 
                                                            v-if="authStore.hasPermission('PhanQuyenController.xoaQuyen')"
                                                            class="btn btn-xs btn-outline-danger px-2 py-0.5" 
                                                            @click="deletePermission(perm)"
                                                        >
                                                            <i class="fas fa-trash-alt"></i> Xóa
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- TAB 4: GÁN VAI TRÒ NGƯỜI DÙNG -->
                            <div v-if="activeTab === 'users'">
                                <div class="row align-items-center mb-2">
                                    <div class="col-md-3 mb-2 mb-md-0">
                                        <label class="small font-weight-bold text-muted mb-0.5 text-xs">Loại đối tượng</label>
                                        <select v-model="userType" class="form-control form-control-sm" @change="fetchUsers">
                                            <option value="giang_vien">Giảng viên (Cán bộ)</option>
                                            <option value="sinh_vien">Sinh viên</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <label class="small font-weight-bold text-muted mb-0.5 text-xs">Tìm kiếm theo tên / email</label>
                                        <div class="input-group input-group-sm">
                                            <input 
                                                v-model="userSearch" 
                                                type="text" 
                                                class="form-control" 
                                                placeholder="Nhập tên hoặc email..." 
                                                @keyup.enter="fetchUsers"
                                            />
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" @click="fetchUsers">
                                                    <i class="fas fa-search"></i> Tìm
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="loadingUsers" class="text-center py-4">
                                    <LoadingSpinner />
                                    <p class="mt-2 text-muted text-xs">Đang tải danh sách người dùng...</p>
                                </div>

                                <div v-else class="table-responsive border rounded" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead class="bg-light text-dark sticky-top border-bottom">
                                            <tr>
                                                <th style="width: 5%;" class="py-2 px-2">#</th>
                                                <th style="width: 25%;" class="py-2 px-2">Họ và tên</th>
                                                <th style="width: 30%;" class="py-2 px-2">Email</th>
                                                <th style="width: 25%;" class="py-2 px-2">Vai trò hiện tại</th>
                                                <th style="width: 15%;" class="text-center py-2 px-2">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(u, idx) in usersList" :key="u.user_id">
                                                <td class="py-1.5 px-2 text-xs">{{ idx + 1 }}</td>
                                                <td class="font-weight-bold text-dark py-1.5 px-2 text-xs">{{ u.ho_ten }}</td>
                                                <td class="py-1.5 px-2 text-xs">{{ u.email }}</td>
                                                <td class="py-1.5 px-2 text-xs">
                                                    <span 
                                                        v-for="r in u.roles" 
                                                        :key="r.vai_tro_id" 
                                                        class="badge badge-info mr-1 px-1.5 py-0.5 text-xs"
                                                    >
                                                        {{ r.ten_vai_tro }}
                                                    </span>
                                                    <span v-if="!u.roles || u.roles.length === 0" class="text-muted small">
                                                        (Chưa có vai trò)
                                                    </span>
                                                </td>
                                                <td class="text-center py-1.5 px-2">
                                                    <button 
                                                        v-if="authStore.hasPermission('PhanQuyenController.ganVaiTroNguoiDung')"
                                                        class="btn btn-xs btn-outline-success px-2 py-0.5" 
                                                        @click="openAssignRoleModal(u)"
                                                    >
                                                        <i class="fas fa-user-tag mr-1"></i> Gán vai trò
                                                    </button>
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

            <!-- MODAL QUẢN LÝ VAI TRÒ -->
            <LTEModal ref="roleModal" @close="resetRoleForm">
                <form @submit.prevent="submitRoleForm">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold small text-muted text-xs">Mã Vai trò <span class="text-danger">*</span></label>
                        <input 
                            v-model="roleForm.ma_vai_tro" 
                            type="text" 
                            class="form-control form-control-sm" 
                            placeholder="Ví dụ: quan_tri_vien" 
                            required
                        />
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold small text-muted text-xs">Tên Vai trò <span class="text-danger">*</span></label>
                        <input 
                            v-model="roleForm.ten_vai_tro" 
                            type="text" 
                            class="form-control form-control-sm" 
                            placeholder="Ví dụ: Quản trị viên Khoa" 
                            required
                        />
                    </div>
                </form>
            </LTEModal>

            <!-- MODAL QUẢN LÝ QUYỀN HẠN -->
            <LTEModal ref="permissionModal" @close="resetPermissionForm">
                <form @submit.prevent="submitPermissionForm">
                    <div class="form-group mb-2">
                        <label class="font-weight-bold small text-muted text-xs">Mã Quyền (Permission Key) <span class="text-danger">*</span></label>
                        <input 
                            v-model="permissionForm.ma_quyen" 
                            type="text" 
                            class="form-control form-control-sm" 
                            placeholder="Ví dụ: StudentPortalController.addAchievement" 
                            required
                        />
                        <small class="text-muted">Định dạng chuẩn: <code>ControllerName.methodName</code></small>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold small text-muted text-xs">Tên Quyền Mô Tả <span class="text-danger">*</span></label>
                        <input 
                            v-model="permissionForm.ten_quyen" 
                            type="text" 
                            class="form-control form-control-sm" 
                            placeholder="Ví dụ: Khai báo thành tích mới" 
                            required
                        />
                    </div>
                </form>
            </LTEModal>

            <!-- MODAL GÁN VAI TRÒ CHO NGƯỜI DÙNG -->
            <LTEModal ref="assignUserModal" @close="resetAssignForm">
                <form @submit.prevent="submitAssignForm">
                    <div class="mb-2">
                        <label class="small text-muted font-weight-bold text-xs">Người dùng:</label>
                        <div class="font-weight-bold text-primary text-sm">{{ currentUserObj?.ho_ten }} ({{ currentUserObj?.email }})</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold small text-muted text-xs">Chọn các vai trò áp dụng:</label>
                        <div v-for="r in rolesList" :key="r.id" class="custom-control custom-checkbox mb-1">
                            <input 
                                :id="'user_role_' + r.id" 
                                v-model="selectedUserRoleIds" 
                                :value="r.id" 
                                type="checkbox" 
                                class="custom-control-input"
                            />
                            <label :for="'user_role_' + r.id" class="custom-control-label font-weight-normal text-xs">
                                <strong>{{ r.ten_vai_tro }}</strong> <span class="text-muted">({{ r.ma_vai_tro }})</span>
                            </label>
                        </div>
                    </div>
                </form>
            </LTEModal>
        </template>
    </LTEContentWrapper>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';

const authStore = useAuthStore();
const activeTab = ref('matrix');

// Loading states
const loadingMatrix = ref(false);
const loadingRoles = ref(false);
const loadingPermissions = ref(false);
const loadingUsers = ref(false);

// Data lists
const matrixData = ref({ ds_vai_tro: [], ds_quyen: [], mapping: {} });
const rolesList = ref([]);
const permissionsList = ref([]);
const usersList = ref([]);

// User filter
const userType = ref('giang_vien');
const userSearch = ref('');
const currentUserObj = ref(null);
const selectedUserRoleIds = ref([]);

// Modals
const roleModal = ref(null);
const permissionModal = ref(null);
const assignUserModal = ref(null);

// Forms
const roleForm = reactive({ id: null, ma_vai_tro: '', ten_vai_tro: '' });
const permissionForm = reactive({ id: null, ma_quyen: '', ten_quyen: '' });

// Custom Group Labels (editable by user)
const customGroupLabels = reactive({
    StudentPortalController: 'Cổng thông tin Đào tạo & Sinh viên',
    PhanQuyenController: 'Quản lý Phân quyền Hệ thống (RBAC)',
    DynamicObjectController: 'Quản lý Đối tượng & Thuộc tính Động'
});

// Load saved custom labels from localStorage on init
try {
    const saved = localStorage.getItem('rbac_custom_group_labels');
    if (saved) {
        Object.assign(customGroupLabels, JSON.parse(saved));
    }
} catch (e) {
    console.error('Error loading custom group labels:', e);
}

const getControllerLabel = (name) => {
    return customGroupLabels[name] || name;
};

const editGroupLabel = (ctrlName) => {
    const current = getControllerLabel(ctrlName);
    const newName = prompt(`Nhập nhãn mô tả tùy chỉnh cho nhóm ${ctrlName}:`, current);
    if (newName !== null && newName.trim() !== '') {
        customGroupLabels[ctrlName] = newName.trim();
        localStorage.setItem('rbac_custom_group_labels', JSON.stringify(customGroupLabels));
    }
};

const getRoleBadgeClass = (maVaiTro) => {
    switch (maVaiTro) {
        case 'admin': return 'badge-danger';
        case 'giang_vien': return 'badge-primary';
        case 'sinh_vien': return 'badge-success';
        default: return 'badge-info';
    }
};

// Group permissions by Controller for Matrix tab
const groupedMatrixPermissions = computed(() => {
    const groups = {};
    (matrixData.value.ds_quyen || []).forEach(perm => {
        const parts = perm.ma_quyen.split('.');
        const controller = parts.length > 1 ? parts[0] : 'Khác';
        if (!groups[controller]) {
            groups[controller] = [];
        }
        groups[controller].push(perm);
    });
    return groups;
});

// Group permissions by Controller for Permissions tab
const groupedListPermissions = computed(() => {
    const groups = {};
    (permissionsList.value || []).forEach(perm => {
        const parts = perm.ma_quyen.split('.');
        const controller = parts.length > 1 ? parts[0] : 'Khác';
        if (!groups[controller]) {
            groups[controller] = [];
        }
        groups[controller].push(perm);
    });
    return groups;
});

const switchTab = (tabName) => {
    activeTab.value = tabName;
    if (tabName === 'matrix') fetchMatrix();
    else if (tabName === 'roles') fetchRoles();
    else if (tabName === 'permissions') fetchPermissions();
    else if (tabName === 'users') fetchUsers();
};

// 1. Matrix logic
const fetchMatrix = async () => {
    loadingMatrix.value = true;
    try {
        const res = await axios.get(route('PhanQuyenController.getMaTranQuyen'));
        if (res.data.status === 200) {
            matrixData.value = res.data.data;
        }
    } catch (err) {
        console.error('Error fetching matrix:', err);
    } finally {
        loadingMatrix.value = false;
    }
};

const isRoleHasPermission = (roleId, permId) => {
    const permList = matrixData.value.mapping[roleId] || [];
    return permList.includes(permId);
};

const togglePermission = async (roleId, permId, isChecked) => {
    let currentPerms = [...(matrixData.value.mapping[roleId] || [])];
    if (isChecked) {
        if (!currentPerms.includes(permId)) currentPerms.push(permId);
    } else {
        currentPerms = currentPerms.filter(id => id !== permId);
    }
    matrixData.value.mapping[roleId] = currentPerms;

    try {
        const res = await axios.post(route('PhanQuyenController.capNhatQuyenVaiTro'), {
            vai_tro_id: roleId,
            quyen_ids: currentPerms
        });
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) {
                window.func.toastSuccess('Cập nhật phân quyền thành công!');
            }
        }
    } catch (err) {
        console.error('Error updating role permissions:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError('Cập nhật phân quyền thất bại!');
        }
    }
};

// Check all per role column
const isAllRoleChecked = (roleId) => {
    const allPerms = matrixData.value.ds_quyen || [];
    if (allPerms.length === 0) return false;
    const currentPerms = matrixData.value.mapping[roleId] || [];
    return allPerms.every(p => currentPerms.includes(p.id));
};

const toggleAllRolePermission = async (roleId, isChecked) => {
    const allPerms = matrixData.value.ds_quyen || [];
    const allIds = allPerms.map(p => p.id);
    const newPerms = isChecked ? allIds : [];

    matrixData.value.mapping[roleId] = newPerms;

    try {
        const res = await axios.post(route('PhanQuyenController.capNhatQuyenVaiTro'), {
            vai_tro_id: roleId,
            quyen_ids: newPerms
        });
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) {
                window.func.toastSuccess(`Đã ${isChecked ? 'cấp tất cả' : 'bỏ chọn tất cả'} quyền cho vai trò!`);
            }
        }
    } catch (err) {
        console.error('Error toggling all role permissions:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError('Cập nhật quyền thất bại!');
        }
    }
};

// Check all per Controller group for role
const isGroupCheckedForRole = (ctrlName, roleId) => {
    const groupPerms = groupedMatrixPermissions.value[ctrlName] || [];
    if (groupPerms.length === 0) return false;
    const currentPerms = matrixData.value.mapping[roleId] || [];
    return groupPerms.every(p => currentPerms.includes(p.id));
};

const toggleGroupPermission = async (ctrlName, roleId, isChecked) => {
    const groupPerms = groupedMatrixPermissions.value[ctrlName] || [];
    const groupPermIds = groupPerms.map(p => p.id);
    let currentPerms = [...(matrixData.value.mapping[roleId] || [])];

    if (isChecked) {
        groupPermIds.forEach(id => {
            if (!currentPerms.includes(id)) currentPerms.push(id);
        });
    } else {
        currentPerms = currentPerms.filter(id => !groupPermIds.includes(id));
    }

    matrixData.value.mapping[roleId] = currentPerms;

    try {
        const res = await axios.post(route('PhanQuyenController.capNhatQuyenVaiTro'), {
            vai_tro_id: roleId,
            quyen_ids: currentPerms
        });
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) {
                window.func.toastSuccess(`Đã ${isChecked ? 'cấp tất cả' : 'bỏ chọn tất cả'} quyền trong nhóm cho vai trò!`);
            }
        }
    } catch (err) {
        console.error('Error updating group permissions:', err);
        if (window.func && window.func.toastError) {
            window.func.toastError('Cập nhật quyền nhóm thất bại!');
        }
    }
};

// 2. Roles logic
const fetchRoles = async () => {
    loadingRoles.value = true;
    try {
        const res = await axios.get(route('PhanQuyenController.getDanhSachVaiTro'));
        if (res.data.status === 200) rolesList.value = res.data.data;
    } catch (err) {
        console.error('Error fetching roles:', err);
    } finally {
        loadingRoles.value = false;
    }
};

const openAddRoleModal = async () => {
    resetRoleForm();
    roleModal.value.$data.title = 'Thêm Vai trò Mới';
    roleModal.value.$data.save = 'Lưu Vai trò';
    const res = await roleModal.value.openModal();
    if (res) submitRoleForm();
};

const openEditRoleModal = async (role) => {
    roleForm.id = role.id;
    roleForm.ma_vai_tro = role.ma_vai_tro;
    roleForm.ten_vai_tro = role.ten_vai_tro;
    roleModal.value.$data.title = 'Chỉnh sửa Vai trò';
    roleModal.value.$data.save = 'Cập nhật';
    const res = await roleModal.value.openModal();
    if (res) submitRoleForm();
};

const resetRoleForm = () => {
    roleForm.id = null;
    roleForm.ma_vai_tro = '';
    roleForm.ten_vai_tro = '';
};

const submitRoleForm = async () => {
    if (!roleForm.ma_vai_tro || !roleForm.ten_vai_tro) return;
    try {
        const res = await axios.post(route('PhanQuyenController.luuVaiTro'), roleForm);
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) window.func.toastSuccess(res.data.message);
            fetchRoles();
        }
    } catch (err) {
        console.error('Error submitting role:', err);
        if (window.func && window.func.toastError) window.func.toastError(err.response?.data?.message || 'Thao tác thất bại');
    }
};

const deleteRole = async (role) => {
    if (!confirm(`Bạn có chắc chắn muốn xóa vai trò "${role.ten_vai_tro}"?`)) return;
    try {
        const res = await axios.delete(route('PhanQuyenController.xoaVaiTro', { id: role.id }));
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) window.func.toastSuccess('Xóa vai trò thành công!');
            fetchRoles();
        }
    } catch (err) {
        console.error('Error deleting role:', err);
        if (window.func && window.func.toastError) window.func.toastError(err.response?.data?.message || 'Xóa thất bại');
    }
};

// 3. Permissions logic
const fetchPermissions = async () => {
    loadingPermissions.value = true;
    try {
        const res = await axios.get(route('PhanQuyenController.getDanhSachQuyen'));
        if (res.data.status === 200) permissionsList.value = res.data.data;
    } catch (err) {
        console.error('Error fetching permissions:', err);
    } finally {
        loadingPermissions.value = false;
    }
};

const openAddPermissionModal = async () => {
    resetPermissionForm();
    permissionModal.value.$data.title = 'Thêm Quyền hạn Mới';
    permissionModal.value.$data.save = 'Lưu Quyền';
    const res = await permissionModal.value.openModal();
    if (res) submitPermissionForm();
};

const openEditPermissionModal = async (perm) => {
    permissionForm.id = perm.id;
    permissionForm.ma_quyen = perm.ma_quyen;
    permissionForm.ten_quyen = perm.ten_quyen;
    permissionModal.value.$data.title = 'Chỉnh sửa Quyền hạn';
    permissionModal.value.$data.save = 'Cập nhật';
    const res = await permissionModal.value.openModal();
    if (res) submitPermissionForm();
};

const resetPermissionForm = () => {
    permissionForm.id = null;
    permissionForm.ma_quyen = '';
    permissionForm.ten_quyen = '';
};

const submitPermissionForm = async () => {
    if (!permissionForm.ma_quyen || !permissionForm.ten_quyen) return;
    try {
        const res = await axios.post(route('PhanQuyenController.luuQuyen'), permissionForm);
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) window.func.toastSuccess(res.data.message);
            fetchPermissions();
        }
    } catch (err) {
        console.error('Error submitting permission:', err);
        if (window.func && window.func.toastError) window.func.toastError(err.response?.data?.message || 'Thao tác thất bại');
    }
};

const deletePermission = async (perm) => {
    if (!confirm(`Bạn có chắc chắn muốn xóa quyền "${perm.ten_quyen}"?`)) return;
    try {
        const res = await axios.delete(route('PhanQuyenController.xoaQuyen', { id: perm.id }));
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) window.func.toastSuccess('Xóa quyền thành công!');
            fetchPermissions();
        }
    } catch (err) {
        console.error('Error deleting permission:', err);
        if (window.func && window.func.toastError) window.func.toastError('Xóa quyền thất bại');
    }
};

// 4. Users role assignment logic
const fetchUsers = async () => {
    loadingUsers.value = true;
    try {
        const res = await axios.get(route('PhanQuyenController.getDanhSachNguoiDung'), {
            params: { user_type: userType.value, search: userSearch.value }
        });
        if (res.data.status === 200) usersList.value = res.data.data;
    } catch (err) {
        console.error('Error fetching users:', err);
    } finally {
        loadingUsers.value = false;
    }
};

const openAssignRoleModal = async (u) => {
    currentUserObj.value = u;
    selectedUserRoleIds.value = u.roles ? u.roles.map(r => r.vai_tro_id) : [];
    // Ensure roles list is loaded
    if (rolesList.value.length === 0) await fetchRoles();

    assignUserModal.value.$data.title = 'Gán Vai trò cho Người dùng';
    assignUserModal.value.$data.save = 'Lưu Phân Vai trò';
    const res = await assignUserModal.value.openModal();
    if (res) submitAssignForm();
};

const resetAssignForm = () => {
    currentUserObj.value = null;
    selectedUserRoleIds.value = [];
};

const submitAssignForm = async () => {
    if (!currentUserObj.value) return;
    try {
        const res = await axios.post(route('PhanQuyenController.ganVaiTroNguoiDung'), {
            user_id: currentUserObj.value.user_id,
            user_type: userType.value,
            vai_tro_ids: selectedUserRoleIds.value
        });
        if (res.data.status === 200) {
            if (window.func && window.func.toastSuccess) window.func.toastSuccess('Cập nhật vai trò người dùng thành công!');
            fetchUsers();
        }
    } catch (err) {
        console.error('Error assigning user role:', err);
        if (window.func && window.func.toastError) window.func.toastError('Gán vai trò thất bại');
    }
};

onMounted(() => {
    fetchMatrix();
});
</script>

<style scoped>
.custom-tabs .nav-link {
    color: #475569;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}
.custom-tabs .nav-link:hover {
    color: #1f4068;
}
.custom-tabs .nav-link.active {
    color: #1f4068;
    background-color: transparent;
    border-bottom-color: #1f4068;
}
.table th {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.btn-xs {
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}
</style>
