<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="row m-0">
                <div class="col-12 p-0">
                    <div class="card border-0" style="border: 1px solid #cbd5e1 !important; border-radius: 8px !important; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important; overflow: hidden; background: #ffffff;">
                        <!-- Light Mode Card Header -->
                        <div class="card-header py-1.5 px-2.5 bg-light border-bottom d-flex align-items-center justify-content-between" style="background-color: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important;">
                            <h6 class="card-title font-weight-bold mb-0 text-xs text-uppercase tracking-wider text-dark d-flex align-items-center">
                                <i class="fas fa-boxes mr-2 text-primary"></i>
                                QUẢN LÝ ĐỐI TƯỢNG VÀ THUỘC TÍNH
                            </h6>
                        </div>

                        <div class="card-body p-2">
                            <!-- Nav Tabs -->
                            <ul class="nav nav-tabs custom-tabs mb-2" id="dynamicTab" role="tablist">
                                <li class="nav-item">
                                    <button 
                                        class="nav-link font-weight-bold py-1 px-2.5 text-xs" 
                                        :class="{ active: activeTab === 'types' }" 
                                        @click="switchTab('types')"
                                    >
                                        <i class="fas fa-list-ul mr-1"></i> Loại Đối Tượng
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button 
                                        class="nav-link font-weight-bold py-1 px-2.5 text-xs" 
                                        :class="{ active: activeTab === 'fields' }" 
                                        @click="switchTab('fields')"
                                    >
                                        <i class="fas fa-sliders-h mr-1"></i> Thuộc Tính Động
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button 
                                        class="nav-link font-weight-bold py-1 px-2.5 text-xs" 
                                        :class="{ active: activeTab === 'records' }" 
                                        @click="switchTab('records')"
                                    >
                                        <i class="fas fa-database mr-1"></i> Dữ Liệu Đối Tượng
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button 
                                        class="nav-link font-weight-bold py-1 px-2.5 text-xs" 
                                        :class="{ active: activeTab === 'layout' }" 
                                        @click="switchTab('layout')"
                                    >
                                        <i class="fas fa-th-large mr-1 text-primary"></i> Thiết Kế Bố Cục Trang
                                    </button>
                                </li>
                            </ul>

                            <!-- TAB 1: LOẠI ĐỐI TƯỢNG -->
                            <div v-if="activeTab === 'types'">
                                <div class="d-flex justify-content-between align-items-center mb-1.5">
                                    <h6 class="font-weight-bold text-dark mb-0 text-xs">
                                        Danh mục Loại đối tượng 
                                        <span class="badge badge-light border text-muted ml-1">{{ types.length }} loại</span>
                                    </h6>
                                    <LTEButton 
                                        v-if="hasPermission('DynamicObjectController.putType')" 
                                        variant="primary" 
                                        icon="fas fa-plus" 
                                        text="Thêm Loại đối tượng" 
                                        class="btn-sm py-1 px-2 text-xs shadow-sm" 
                                        @click="openTypeModal()" 
                                    />
                                </div>

                                <AppTable
                                    :columns="[
                                        { key: 'idx', label: '#', width: '45px', align: 'center' },
                                        { key: 'ma_loai', label: 'Mã Loại', width: '15%' },
                                        { key: 'ten_loai', label: 'Tên Loại Đối Tượng & Mô Tả', width: '40%' },
                                        { key: 'fields_count', label: 'Thuộc tính', width: '10%', align: 'center' },
                                        { key: 'records_count', label: 'Bản ghi', width: '10%', align: 'center' },
                                        { key: 'phan_loai', label: 'Phân loại', width: '10%', align: 'center' }
                                    ]"
                                    :items="types"
                                    actions-width="15%"
                                    empty-text="Chưa có loại đối tượng nào trong hệ thống."
                                >
                                    <template #col-idx="{ index }">
                                        <span class="text-muted font-mono">{{ index + 1 }}</span>
                                    </template>
                                    <template #col-ma_loai="{ item }">
                                        <AppBadge variant="primary">{{ item.ma_loai }}</AppBadge>
                                    </template>
                                    <template #col-ten_loai="{ item }">
                                        <div class="font-weight-bold text-dark text-xs">{{ item.ten_loai }}</div>
                                        <div v-if="item.mo_ta" class="small text-muted mt-0.5 leading-snug" style="font-size: 0.75rem;">
                                            {{ item.mo_ta }}
                                        </div>
                                    </template>
                                    <template #col-fields_count="{ item }">
                                        <AppBadge variant="secondary">{{ item.fields_count || 0 }}</AppBadge>
                                    </template>
                                    <template #col-records_count="{ item }">
                                        <AppBadge variant="secondary">{{ item.records_count || 0 }}</AppBadge>
                                    </template>
                                    <template #col-phan_loai="{ item }">
                                        <AppBadge v-if="['giang_vien', 'sinh_vien'].includes(item.ma_loai)" variant="warning">Mặc định</AppBadge>
                                        <AppBadge v-else variant="light">Mở rộng</AppBadge>
                                    </template>
                                    <template #actions="{ item }">
                                        <IconButton @click="configureFields(item)" variant="slate" icon="fas fa-sliders-h" title="Cấu hình Thuộc tính" />
                                        <IconButton @click="viewRecords(item)" variant="blue" icon="fas fa-database" title="Xem Dữ liệu đối tượng" />
                                        <IconButton v-if="hasPermission('DynamicObjectController.putType')" @click="openTypeModal(item)" variant="amber" icon="fas fa-edit" title="Chỉnh sửa" />
                                        <IconButton v-if="hasPermission('DynamicObjectController.deleteType') && !['giang_vien', 'sinh_vien'].includes(item.ma_loai)" @click="deleteType(item)" variant="red" icon="fas fa-trash-alt" title="Xóa" />
                                    </template>
                                </AppTable>
                            </div>

                            <!-- TAB 2: THUỘC TÍNH ĐỘNG -->
                            <div v-if="activeTab === 'fields'">
                                <div class="d-flex justify-content-between align-items-center mb-1.5">
                                    <div class="d-flex align-items-center">
                                        <label class="small font-weight-bold text-dark mb-0 mr-2 text-xs">Loại đối tượng:</label>
                                        <LTESelect2Option
                                            :model-value="selectedTypeId"
                                            :init-value="selectedTypeId"
                                            :data="typeSelectOptions"
                                            :multiple="false"
                                            :close-on-select="true"
                                            :allow-clear="false"
                                            :enable-data-watch="true"
                                            @update:model-value="onTypeSelectChange"
                                            style="width: 220px;"
                                        />
                                        <span class="text-muted small ml-2 font-weight-normal">({{ fields.length }} thuộc tính - {{ Object.keys(groupedFields).length }} nhóm)</span>
                                    </div>
                                    <LTEButton 
                                        v-if="hasPermission('DynamicObjectController.putField')" 
                                        variant="primary" 
                                        icon="fas fa-plus" 
                                        text="Thêm Thuộc tính" 
                                        class="btn-sm py-1 px-2 text-xs shadow-sm" 
                                        @click="openFieldModal()" 
                                    />
                                </div>

                                <AppTable>
                                    <template #thead>
                                        <thead style="background-color: #007bff !important;">
                                            <tr>
                                                <th style="width: 52px; min-width: 52px; max-width: 52px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Kéo / #</th>
                                                <th style="width: 160px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">Mã Thuộc Tính</th>
                                                <th class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">Tên Thuộc Tính</th>
                                                <th style="width: 150px;" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">Kiểu Dữ Liệu</th>
                                                <th style="width: 120px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Quyền Sửa</th>
                                                <th style="width: 110px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Trạng Thái</th>
                                                <th style="width: 90px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">Thao tác</th>
                                            </tr>
                                        </thead>
                                    </template>
                                    <template #tbody>
                                        <tbody>
                                            <template v-for="(groupFields, groupName) in groupedFields" :key="groupName">
                                                <!-- Group Subheader Row matching Permissions.vue subheaders -->
                                                <tr 
                                                    @dragover.prevent="onDragOverGroupHeader($event, groupName)"
                                                    @drop="onDrop($event, null, groupName)"
                                                    :class="{ 'group-drag-over': dragOverGroup === groupName && !dragOverField }"
                                                    style="background-color: #eff6ff; border-left: 4px solid #2563eb;"
                                                >
                                                    <td colspan="7" class="py-1 px-2.5">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-layer-group text-primary mr-1.5 text-xs"></i>
                                                                <span class="font-weight-bold text-primary text-xs uppercase">{{ groupName }}</span>
                                                                <span class="badge badge-light border text-dark ml-2 px-2 py-0.5">
                                                                    {{ groupFields.length }} thuộc tính
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr 
                                                    v-for="(f, idx) in groupFields" 
                                                    :key="f.id" 
                                                    draggable="true"
                                                    @dragstart="onDragStart($event, f, groupName)"
                                                    @dragover.prevent="onDragOverField($event, f, groupName)"
                                                    @dragleave="onDragLeave"
                                                    @dragend="onDragEnd"
                                                    @drop="onDrop($event, f, groupName)"
                                                    :class="{
                                                        'row-dragged': draggedField && draggedField.id === f.id,
                                                        'row-drag-over-above': dragOverField && dragOverField.id === f.id && dragOverPosition === 'above',
                                                        'row-drag-over-below': dragOverField && dragOverField.id === f.id && dragOverPosition === 'below'
                                                    }"
                                                >
                                                    <td class="text-center py-1 px-2 text-xs" style="width: 52px; min-width: 52px; max-width: 52px;">
                                                        <i class="fas fa-grip-vertical text-muted mr-1 cursor-grab" title="Kéo để di chuyển"></i>
                                                        <span class="font-weight-bold text-dark">{{ f.thu_tu || (idx + 1) }}</span>
                                                    </td>
                                                    <td class="py-1 px-2">
                                                        <code style="font-size: 0.78rem;" class="text-primary font-weight-bold">{{ f.ma_truong }}</code>
                                                    </td>
                                                    <td class="font-weight-bold text-dark py-1 px-2 text-xs">
                                                        <span>{{ f.ten_truong }}</span>
                                                        <span v-if="f.bat_buoc" class="text-danger ml-1" title="Bắt buộc nhập">*</span>
                                                    </td>
                                                    <td class="py-1 px-2 text-xs">
                                                        <AppBadge variant="light">
                                                            <i :class="getDataTypeIcon(f.kieu_du_lieu) + ' text-primary mr-1'"></i>
                                                            {{ getDataTypeLabel(f.kieu_du_lieu) }}
                                                        </AppBadge>
                                                    </td>
                                                    <td class="text-center py-1 px-2">
                                                        <AppBadge :variant="(f.cho_phep_chinh_sua !== false && f.cho_phep_chinh_sua !== 0 && f.cho_phep_chinh_sua !== '0') ? 'success' : 'danger'">
                                                            {{ (f.cho_phep_chinh_sua !== false && f.cho_phep_chinh_sua !== 0 && f.cho_phep_chinh_sua !== '0') ? 'Cho phép' : 'Khóa' }}
                                                        </AppBadge>
                                                    </td>
                                                    <td class="text-center py-1 px-2">
                                                        <AppBadge :variant="f.trang_thai ? 'success' : 'secondary'">
                                                            {{ f.trang_thai ? 'Đang dùng' : 'Tạm ẩn' }}
                                                        </AppBadge>
                                                    </td>
                                                    <td class="text-center py-1 px-2">
                                                        <div class="d-inline-flex align-items-center gap-1">
                                                            <IconButton v-if="hasPermission('DynamicObjectController.putField')" @click="openFieldModal(f)" variant="amber" icon="fas fa-edit" title="Chỉnh sửa Thuộc tính" />
                                                            <IconButton v-if="hasPermission('DynamicObjectController.deleteField')" @click="deleteField(f)" variant="red" icon="fas fa-trash-alt" title="Xóa Thuộc tính" />
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr v-if="fields.length === 0">
                                                <td colspan="7" class="py-3 text-center text-muted italic text-xs">
                                                    Chưa có thuộc tính nào được cấu hình cho loại đối tượng này.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </AppTable>
                            </div>

                            <!-- TAB 3: DỮ LIỆU ĐỐI TƯỢNG -->
                            <div v-if="activeTab === 'records'">
                                <div class="d-flex justify-content-between align-items-center mb-1.5">
                                    <div class="d-flex align-items-center">
                                        <label class="small font-weight-bold text-dark mb-0 mr-2 text-xs">Loại đối tượng:</label>
                                        <LTESelect2Option
                                            :model-value="selectedTypeId"
                                            :init-value="selectedTypeId"
                                            :data="typeSelectOptions"
                                            :multiple="false"
                                            :close-on-select="true"
                                            :allow-clear="false"
                                            :enable-data-watch="true"
                                            @update:model-value="onTypeSelectChange"
                                            style="width: 220px;"
                                        />
                                        <span class="text-muted small ml-2 font-weight-normal">({{ records.length }} bản ghi)</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <LTEButton 
                                            variant="outline-primary" 
                                            icon="fas fa-file-excel text-success" 
                                            text="File Mẫu Excel" 
                                            class="btn-sm py-1 px-2.5 text-xs font-weight-bold shadow-sm" 
                                            title="Tải file mẫu Excel được tạo tự động theo thuộc tính đã cấu hình" 
                                            @click="downloadImportTemplate()" 
                                        />

                                        <LTEButton 
                                            v-if="hasPermission('DynamicObjectController.putRecord')" 
                                            variant="info" 
                                            icon="fas fa-file-import" 
                                            text="Import Dữ Liệu" 
                                            class="btn-sm py-1 px-2.5 text-xs font-weight-bold shadow-sm text-white" 
                                            title="Chuyển sang trang Import & Phân tích Dữ liệu tập trung" 
                                            @click="goToImportPage()" 
                                        />

                                        <LTEButton 
                                            variant="outline-success" 
                                            icon="fas fa-file-export" 
                                            text="Xuất Dữ Liệu (Excel)" 
                                            class="btn-sm py-1 px-2.5 text-xs font-weight-bold shadow-sm" 
                                            title="Xuất toàn bộ danh sách bản ghi và dữ liệu thuộc tính ra file Excel (.xlsx)" 
                                            @click="exportRecords()" 
                                        />

                                        <LTEButton 
                                            v-if="hasPermission('DynamicObjectController.putRecord')" 
                                            variant="success" 
                                            icon="fas fa-plus" 
                                            text="Thêm Bản ghi" 
                                            class="btn-sm py-1 px-2.5 text-xs font-weight-bold shadow-sm" 
                                            @click="openRecordModal()" 
                                        />
                                    </div>
                                </div>

                                <AppTable>
                                    <template #thead>
                                        <thead style="background-color: #007bff !important;">
                                            <tr>
                                                <th style="width: 45px;" class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0">#</th>
                                                <th v-if="currentRecordType && currentRecordType.ma_loai !== 'giang_vien'" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">{{ getCodeHeaderLabel() }}</th>
                                                <th class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">{{ getNameHeaderLabel() }}</th>
                                                <th v-for="f in recordFields" :key="f.id" class="py-1.5 px-2 text-white font-weight-bold text-xs border-0">
                                                    {{ f.ten_truong }}
                                                </th>
                                                <th class="text-center py-1.5 px-2 text-white font-weight-bold text-xs border-0" style="width: 10%;">Thao tác</th>
                                            </tr>
                                        </thead>
                                    </template>
                                    <template #tbody>
                                        <tbody>
                                            <tr v-for="(rec, idx) in records" :key="rec.id">
                                                <td class="text-center py-1 px-2 text-xs text-muted">{{ idx + 1 }}</td>
                                                <td v-if="currentRecordType && currentRecordType.ma_loai !== 'giang_vien'" class="py-1 px-2 font-mono text-xs font-weight-bold">
                                                    <AppBadge variant="primary">{{ rec.ma_doi_tuong }}</AppBadge>
                                                </td>
                                                <td class="py-1 px-2">
                                                    <div class="font-weight-bold text-dark text-xs">{{ rec.ten_hien_thi }}</div>
                                                    <div v-if="rec.email" class="small text-muted font-mono mt-0.5">
                                                        {{ rec.email }}
                                                    </div>
                                                </td>
                                                <td v-for="f in recordFields" :key="f.id" class="py-1 px-2 text-xs text-dark">
                                                    <template v-if="f.kieu_du_lieu === 'image' && rec.attributes[f.ma_truong]">
                                                        <div @click="previewImage(rec.attributes[f.ma_truong])" class="cursor-pointer">
                                                            <img :src="rec.attributes[f.ma_truong]" class="rounded border" style="width: 32px; height: 32px; object-fit: cover;" />
                                                        </div>
                                                    </template>
                                                    <template v-else-if="f.kieu_du_lieu === 'file' && rec.attributes[f.ma_truong]">
                                                        <a :href="rec.attributes[f.ma_truong]" target="_blank" download class="btn btn-xs btn-outline-primary font-weight-bold">
                                                            <i :class="getFileIconClass(rec.attributes[f.ma_truong])"></i> Tải tệp
                                                        </a>
                                                    </template>
                                                    <template v-else-if="f.kieu_du_lieu === 'boolean'">
                                                        <AppBadge :variant="rec.attributes[f.ma_truong] ? 'success' : 'secondary'">
                                                            {{ rec.attributes[f.ma_truong] ? 'Có / Đạt' : 'Không' }}
                                                        </AppBadge>
                                                    </template>
                                                    <template v-else-if="f.kieu_du_lieu === 'color' && rec.attributes[f.ma_truong]">
                                                        <div class="d-flex align-items-center">
                                                            <span class="rounded-circle border mr-1" :style="{ backgroundColor: rec.attributes[f.ma_truong], width: '14px', height: '14px', display: 'inline-block' }"></span>
                                                            <span class="font-mono text-xs">{{ rec.attributes[f.ma_truong] }}</span>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        {{ rec.attributes[f.ma_truong] || '-' }}
                                                    </template>
                                                </td>
                                                <td class="text-center py-1 px-2">
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        <IconButton v-if="hasPermission('DynamicObjectController.putRecord')" @click="openRecordModal(rec)" variant="amber" icon="fas fa-edit" title="Chỉnh sửa Bản ghi" />
                                                        <IconButton v-if="hasPermission('DynamicObjectController.deleteRecord')" @click="deleteRecord(rec)" variant="red" icon="fas fa-trash-alt" title="Xóa Bản ghi" />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="records.length === 0">
                                                <td :colspan="3 + recordFields.length" class="py-3 text-center text-muted italic text-xs">
                                                    Chưa có dữ liệu bản ghi cho loại đối tượng này.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </AppTable>
                            </div>

                            <!-- TAB 4: THIẾT KẾ BỐ CỤC TRANG (VISUAL LAYOUT DESIGNER) -->
                            <div v-if="activeTab === 'layout'">
                                <div class="d-flex justify-content-between align-items-center mb-2.5 bg-slate-50 p-2 rounded border border-slate-200">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="small font-weight-bold text-dark mb-0 text-xs">Loại đối tượng:</label>
                                        <LTESelect2Option
                                            :model-value="selectedTypeId"
                                            :init-value="selectedTypeId"
                                            :data="typeSelectOptions"
                                            :multiple="false"
                                            :close-on-select="true"
                                            :allow-clear="false"
                                            :enable-data-watch="true"
                                            @update:model-value="onTypeSelectChange"
                                            style="width: 220px;"
                                        />
                                        
                                        <!-- Device Viewport Switcher -->
                                        <div class="btn-group btn-group-toggle ml-3" role="group">
                                            <button 
                                                type="button" 
                                                class="btn btn-xs px-2.5 py-1 font-weight-bold"
                                                :class="previewDevice === 'desktop' ? 'btn-primary' : 'btn-outline-secondary'"
                                                @click="previewDevice = 'desktop'"
                                            >
                                                <i class="fas fa-desktop mr-1"></i> Máy tính (Desktop)
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-xs px-2.5 py-1 font-weight-bold"
                                                :class="previewDevice === 'mobile' ? 'btn-primary' : 'btn-outline-secondary'"
                                                @click="previewDevice = 'mobile'"
                                            >
                                                <i class="fas fa-mobile-alt mr-1"></i> Điện thoại (Mobile)
                                            </button>
                                        </div>
                                    </div>

                                    <LTEButton 
                                        v-if="hasPermission('DynamicObjectController.putField')"
                                        variant="success" 
                                        icon="far fa-save" 
                                        text="Lưu Bố Cục Trang" 
                                        class="btn-sm py-1 px-3 text-xs font-weight-bold shadow-sm" 
                                        @click="saveLayoutConfig()" 
                                    />
                                </div>

                                <!-- CANVAS CONTAINER -->
                                <div class="layout-designer-wrapper p-3 bg-slate-100 rounded-xl border border-slate-200 min-h-[500px]">
                                    <div :class="previewDevice === 'mobile' ? 'max-w-[400px] mx-auto bg-white p-3 rounded-2xl shadow-xl border-4 border-slate-700' : 'w-full'">
                                        
                                        <div v-if="previewDevice === 'mobile'" class="text-center pb-2 border-b mb-3">
                                            <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-1"></div>
                                            <span class="text-[10px] text-muted uppercase tracking-wider font-bold">Xem trước Giao diện Mobile</span>
                                        </div>

                                        <div v-for="(groupFields, groupName) in groupedLayoutFields" :key="groupName" class="mb-4 bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">
                                            <div class="d-flex align-items-center justify-content-between border-b pb-2 mb-3">
                                                <h6 class="font-weight-bold text-primary text-xs uppercase mb-0 d-flex align-items-center">
                                                    <i class="fas fa-layer-group mr-1.5 text-xs"></i>
                                                    {{ groupName }}
                                                    <span class="badge badge-light border text-muted ml-2 font-normal">({{ groupFields.length }} thuộc tính)</span>
                                                </h6>
                                                <span class="text-[11px] text-muted italic">Kéo icon <i class="fas fa-grip-vertical"></i> để đổi vị trí</span>
                                            </div>

                                            <div class="row m-0">
                                                <div 
                                                    v-for="(field, fIdx) in groupFields" 
                                                    :key="field.id"
                                                    :class="[
                                                        previewDevice === 'mobile' ? 'col-12' : getColClass(field.col_span),
                                                        'p-1'
                                                    ]"
                                                    draggable="true"
                                                    @dragstart="onDragStart($event, field, groupName)"
                                                    @dragover.prevent="onDragOverField($event, field, groupName)"
                                                    @dragleave="onDragLeave"
                                                    @dragend="onDragEnd"
                                                    @drop="onDropLayout($event, field, groupName)"
                                                >
                                                    <div 
                                                        class="field-card p-2 bg-slate-50 hover:bg-blue-50/40 rounded-lg border transition-all shadow-2xs group relative"
                                                        :class="{
                                                            'border-primary border-2 shadow-md bg-blue-50/90 scale-[1.01]': dragOverField && dragOverField.id === field.id,
                                                            'opacity-40 border-dashed border-primary': draggedField && draggedField.id === field.id,
                                                            'border-slate-200': !dragOverField || dragOverField.id !== field.id
                                                        }"
                                                    >
                                                        <!-- Insertion Line Indicator -->
                                                        <div 
                                                            v-if="dragOverField && dragOverField.id === field.id"
                                                            class="absolute left-0 right-0 h-1 bg-primary rounded-full z-10"
                                                            :class="dragOverPosition === 'below' ? '-bottom-1' : '-top-1'"
                                                        ></div>
                                                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="fas fa-grip-vertical text-slate-400 cursor-grab hover:text-blue-600 mr-1" title="Kéo để di chuyển"></i>
                                                                <code class="text-primary font-bold text-xs">{{ field.ma_truong }}</code>
                                                                <span v-if="field.bat_buoc" class="text-danger font-bold ml-0.5">*</span>
                                                                <span v-if="field.cho_phep_chinh_sua === false || field.cho_phep_chinh_sua === 0 || field.cho_phep_chinh_sua === '0'" class="badge badge-light border text-danger text-[10px] ml-1">Khóa</span>
                                                            </div>

                                                            <!-- Quick Col Width Buttons -->
                                                            <div class="btn-group btn-group-toggle" role="group">
                                                                <button 
                                                                    type="button" 
                                                                    title="Độ rộng 100% (1 cột full)" 
                                                                    class="btn btn-xs px-1.5 py-0.5 text-[10px] font-weight-bold"
                                                                    :class="field.col_span === 12 ? 'btn-primary' : 'btn-light border'"
                                                                    @click="setFieldColSpan(field, 12)"
                                                                >
                                                                    100%
                                                                </button>
                                                                <button 
                                                                    type="button" 
                                                                    title="Độ rộng 50% (2 cột)" 
                                                                    class="btn btn-xs px-1.5 py-0.5 text-[10px] font-weight-bold"
                                                                    :class="field.col_span === 6 ? 'btn-primary' : 'btn-light border'"
                                                                    @click="setFieldColSpan(field, 6)"
                                                                >
                                                                    50%
                                                                </button>
                                                                <button 
                                                                    type="button" 
                                                                    title="Độ rộng 33% (3 cột)" 
                                                                    class="btn btn-xs px-1.5 py-0.5 text-[10px] font-weight-bold"
                                                                    :class="field.col_span === 4 ? 'btn-primary' : 'btn-light border'"
                                                                    @click="setFieldColSpan(field, 4)"
                                                                >
                                                                    33%
                                                                </button>
                                                                <button 
                                                                    type="button" 
                                                                    title="Độ rộng 25% (4 cột)" 
                                                                    class="btn btn-xs px-1.5 py-0.5 text-[10px] font-weight-bold"
                                                                    :class="field.col_span === 3 ? 'btn-primary' : 'btn-light border'"
                                                                    @click="setFieldColSpan(field, 3)"
                                                                >
                                                                    25%
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="text-xs font-bold text-dark mb-1">{{ field.ten_truong }}</div>

                                                        <!-- Live Component Mockup Preview -->
                                                        <div class="preview-mockup pointer-events-none opacity-80">
                                                            <textarea v-if="field.kieu_du_lieu === 'textarea'" rows="2" class="w-full bg-white border rounded px-2 py-1 text-xs" readonly placeholder="Textarea ô nhập nhiều dòng..."></textarea>
                                                            <div v-else-if="['file', 'image'].includes(field.kieu_du_lieu)" class="p-1.5 bg-white border rounded text-xs text-center text-muted">
                                                                <i :class="getDataTypeIcon(field.kieu_du_lieu) + ' text-primary mr-1'"></i> {{ getDataTypeLabel(field.kieu_du_lieu) }} (Tệp đính kèm)
                                                            </div>
                                                            <div v-else-if="['select', 'multiselect'].includes(field.kieu_du_lieu)" class="w-full bg-white border rounded px-2 py-1 text-xs text-muted d-flex justify-content-between align-items-center">
                                                                <span>-- Chọn {{ field.ten_truong }} --</span>
                                                                <i class="fas fa-chevron-down text-[10px]"></i>
                                                            </div>
                                                            <input v-else type="text" class="w-full bg-white border rounded px-2 py-1 text-xs" readonly :placeholder="'Nhập ' + field.ten_truong.toLowerCase() + '...'" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="layoutFields.length === 0" class="text-center py-5 text-muted italic text-xs">
                                            Chưa có thuộc tính nào để thiết kế bố cục.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <Teleport to="body">
                <!-- MODAL: TYPE FORM -->
                <LTEModal ref="typeModal" size="md">
                    <div class="space-y-2">
                        <LTEInput 
                            v-model="typeForm.ma_loai" 
                            label="MÃ LOẠI ĐỐI TƯỢNG *" 
                            placeholder="Ví dụ: phong_hoc, thiet_bi..." 
                            :is-disabled="typeForm.id && ['giang_vien', 'sinh_vien'].includes(typeForm.ma_loai)" 
                        />
                        <LTEInput 
                            v-model="typeForm.ten_loai" 
                            label="TÊN LOẠI ĐỐI TƯỢNG *" 
                            placeholder="Ví dụ: Phòng học & Giảng đường" 
                        />
                        <LTETextArea 
                            v-model="typeForm.mo_ta" 
                            label="MÔ TẢ" 
                            placeholder="Mô tả chức năng đối tượng..." 
                            :rows="3" 
                        />
                    </div>
                </LTEModal>

                <!-- MODAL: FIELD FORM WITH PHÂN NHÓM & DYNAMIC REFERENCE -->
                <LTEModal ref="fieldModal" size="lg">
                    <div class="space-y-2">
                        <LTEInput 
                            v-model="fieldForm.ma_truong" 
                            label="MÃ THUỘC TÍNH *" 
                            placeholder="Ví dụ: hoc_vi, chuc_danh, file_cv..." 
                        />
                        <LTEInput 
                            v-model="fieldForm.ten_truong" 
                            label="TÊN THUỘC TÍNH *" 
                            placeholder="Ví dụ: Học vị / Tệp CV đính kèm" 
                        />
                        <div class="grid grid-cols-2 gap-x-3 gap-y-0">
                            <LTEInput 
                                v-model="fieldForm.phan_nhom" 
                                label="PHÂN NHÓM" 
                                placeholder="Ví dụ: Thông tin Lý lịch & Cá nhân..." 
                            />
                            <LTEInput 
                                v-model.number="fieldForm.thu_tu" 
                                type="number" 
                                label="THỨ TỰ SẮP XẾP" 
                                placeholder="Ví dụ: 1, 2, 3..." 
                            />
                        </div>
                        <div>
                            <LTESelect2Option
                                v-model="fieldForm.kieu_du_lieu"
                                :init-value="fieldForm.kieu_du_lieu"
                                label="KIỂU DỮ LIỆU"
                                placeholder="-- Chọn kiểu dữ liệu --"
                                :data="dataTypeOptions"
                                :multiple="false"
                                :close-on-select="true"
                                :allow-clear="false"
                                :enable-data-watch="true"
                            />
                        </div>

                        <!-- Dynamic Reference Settings (Select / MultiSelect) -->
                        <div v-if="['select', 'multiselect'].includes(fieldForm.kieu_du_lieu)" class="p-3 bg-blue-50/50 rounded-lg border border-blue-200/60 space-y-2">
                            <div>
                                <LTESelect2Option
                                    v-model="fieldForm.lien_ket_loai_doi_tuong_id"
                                    :init-value="fieldForm.lien_ket_loai_doi_tuong_id"
                                    label="NGUỒN DANH SÁCH CHỌN"
                                    placeholder="-- Nguồn danh sách chọn --"
                                    :data="[
                                        { value: '', text: '📌 Tự định nghĩa danh sách tùy chọn bên dưới' },
                                        ...types.map(t => ({ value: t.id, text: '🔗 Lấy tự động từ Danh mục: ' + t.ten_loai }))
                                    ]"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :allow-clear="false"
                                    :enable-data-watch="true"
                                />
                            </div>

                            <!-- Dynamic Linked Binding Rule (Save Ma vs Save Ten) -->
                            <div v-if="fieldForm.lien_ket_loai_doi_tuong_id">
                                <LTESelect2Option
                                    v-model="fieldForm.cau_hinh.tieu_chuan_gia_tri"
                                    :init-value="fieldForm.cau_hinh.tieu_chuan_gia_tri"
                                    label="GIÁ TRỊ LƯU VÀO HỆ THỐNG"
                                    placeholder="-- Giá trị lưu vào hệ thống --"
                                    :data="[
                                        { value: 'ma', text: 'Mã đối tượng / Email (Khuyên dùng - Ràng buộc duy nhất)' },
                                        { value: 'ten', text: 'Tên hiển thị (Tên đầy đủ của đối tượng)' }
                                    ]"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :allow-clear="false"
                                    :enable-data-watch="true"
                                />
                            </div>

                            <!-- Manual Option List Chips -->
                            <div v-if="!fieldForm.lien_ket_loai_doi_tuong_id" class="space-y-1">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Danh sách tùy chọn</label>
                                
                                <div class="flex items-center gap-1.5">
                                    <LTEInput 
                                        v-model="newOptionText" 
                                        placeholder="Nhập tên tùy chọn rồi nhấn Enter..." 
                                        class="flex-1 mb-0 p-0" 
                                        @keydown.enter.prevent="addOptionChip"
                                    />
                                    <button 
                                        type="button"
                                        @click="addOptionChip" 
                                        class="btn btn-sm btn-primary text-xs px-3 h-[31px]"
                                    >
                                        <i class="fas fa-plus text-[10px]"></i> Thêm
                                    </button>
                                </div>

                                <div class="flex flex-wrap gap-1.5 pt-1 min-h-[32px] p-2 bg-white rounded-lg border border-slate-200">
                                    <div 
                                        v-for="(opt, idx) in optionList" 
                                        :key="idx" 
                                        class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-md text-xs font-semibold group"
                                    >
                                        <span>{{ opt }}</span>
                                        <button 
                                            type="button"
                                            @click="removeOptionChip(idx)" 
                                            class="text-blue-400 hover:text-rose-600 transition-colors"
                                            title="Xóa tùy chọn này"
                                        >
                                            <i class="fas fa-times text-[10px]"></i>
                                        </button>
                                    </div>
                                    <span v-if="optionList.length === 0" class="text-[11px] text-slate-400 italic">Chưa có tùy chọn nào. Hãy nhập tên ở trên để thêm.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced File & Image Config Section -->
                        <div v-if="['file', 'image'].includes(fieldForm.kieu_du_lieu)" class="p-3 bg-amber-50/50 rounded-lg border border-amber-200/60 space-y-2">
                            <h4 class="font-bold text-[11px] text-amber-900 uppercase mb-1 flex items-center gap-1">
                                <i class="fas fa-sliders-h text-amber-600"></i> Cấu hình tệp & Dung lượng
                            </h4>
                            <div class="grid grid-cols-2 gap-x-3 gap-y-0">
                                <LTEInput 
                                    v-model.number="fieldForm.cau_hinh.dung_luong_toi_da" 
                                    type="number" 
                                    label="DUNG LƯỢNG TỐI ĐA (MB)" 
                                    placeholder="Mặc định: 10 MB" 
                                />
                                <LTEInput 
                                    v-model="fieldForm.cau_hinh.dinh_dang_tep" 
                                    label="ĐUÔI TỆP CHO PHÉP" 
                                    placeholder="Ví dụ: pdf, docx, png..." 
                                />
                            </div>
                            <LTEInput 
                                v-model="fieldForm.cau_hinh.mau_ten_tep" 
                                label="RÀNG BUỘC TÊN TỆP (PATTERN / REGEX)" 
                                placeholder="Ví dụ: ^[A-Za-z0-9_-]+$ (Không dấu, không khoảng trắng)..." 
                            />
                        </div>

                        <!-- Common Values & Validation Settings -->
                        <div class="p-3 bg-slate-100/70 rounded-lg border border-slate-200/80 space-y-2">
                            <h4 class="font-bold text-[11px] text-slate-800 uppercase mb-1 flex items-center gap-1">
                                <i class="fas fa-check-circle text-slate-500"></i> Ràng buộc & Giá trị mặc định
                            </h4>
                            <LTEInput 
                                v-model="fieldForm.cau_hinh.gia_tri_mac_dinh" 
                                label="GIÁ TRỊ MẶC ĐỊNH BAN ĐẦU" 
                                placeholder="Ví dụ: Chưa xác định / Nam..." 
                            />
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex items-center gap-2">
                                    <input v-model="fieldForm.bat_buoc" type="checkbox" id="field_required" class="w-3.5 h-3.5 text-rose-600 rounded cursor-pointer" />
                                    <label for="field_required" class="text-xs font-bold text-rose-700 mb-0 cursor-pointer">Bắt buộc nhập</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input v-model="fieldForm.cho_phep_chinh_sua" type="checkbox" id="field_editable" class="w-3.5 h-3.5 text-amber-600 rounded cursor-pointer" />
                                    <label for="field_editable" class="text-xs font-bold text-amber-700 mb-0 cursor-pointer">Cho phép chỉnh sửa</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input v-model="fieldForm.trang_thai" type="checkbox" id="field_status" class="w-3.5 h-3.5 text-blue-600 rounded cursor-pointer" />
                                    <label for="field_status" class="text-xs font-semibold text-slate-700 mb-0 cursor-pointer">Kích hoạt</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </LTEModal>

                <!-- MODAL: RECORD FORM WITH GROUPED ATTRIBUTES -->
                <LTEModal ref="recordModal" size="xl">
                    <div class="space-y-3">
                        <!-- Basic Fixed Information Group -->
                        <div class="bg-blue-50/40 p-3 rounded-lg border border-blue-100 mb-2">
                            <h4 class="font-bold text-xs uppercase text-blue-800 mb-2 flex items-center gap-1.5 border-b border-blue-200/60 pb-1.5">
                                <i class="fas fa-id-card text-blue-600"></i> Thông tin định danh cơ bản
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3 gap-y-0">
                                <LTEInput 
                                    v-if="currentRecordType && currentRecordType.ma_loai !== 'giang_vien'"
                                    v-model="recordForm.ma_doi_tuong" 
                                    :label="getCodeHeaderLabel() + ' *'" 
                                    :placeholder="'Nhập ' + getCodeHeaderLabel() + '...'" 
                                />

                                <LTEInput 
                                    v-model="recordForm.ten_hien_thi" 
                                    :label="getNameHeaderLabel() + ' *'" 
                                    :placeholder="'Nhập ' + getNameHeaderLabel() + '...'" 
                                />

                                <LTEInput 
                                    v-if="currentRecordType && ['giang_vien', 'sinh_vien'].includes(currentRecordType.ma_loai)" 
                                    v-model="recordForm.email" 
                                    type="email" 
                                    :label="getEmailHeaderLabel() + ' *'" 
                                    placeholder="nguyenvana@vlute.edu.vn..." 
                                    class="md:col-span-2"
                                />
                            </div>
                        </div>

                        <!-- Dynamic Grouped Attributes -->
                        <div 
                            v-for="(groupFields, groupName) in groupedRecordFields" 
                            :key="groupName" 
                            class="bg-white p-3 rounded-lg border border-slate-200 shadow-2xs mb-2"
                        >
                            <h4 class="font-bold text-xs uppercase text-slate-800 mb-2 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                                <i class="fas fa-layer-group text-blue-600"></i> {{ groupName }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3 gap-y-0">
                                <div v-for="f in groupFields" :key="f.id" :class="(f.col_span === 12 || ['textarea', 'file', 'image'].includes(f.kieu_du_lieu)) ? 'md:col-span-2' : 'md:col-span-1'">
                                     <!-- Input for Textarea -->
                                    <LTETextArea 
                                        v-if="f.kieu_du_lieu === 'textarea'"
                                        v-model="recordForm.attributes[f.ma_truong]"
                                        :label="f.ten_truong"
                                        :is-disabled="recordForm.id && (f.cho_phep_chinh_sua === false || f.cho_phep_chinh_sua === 0 || f.cho_phep_chinh_sua === '0')"
                                        :rows="3"
                                        :placeholder="'Nhập ' + f.ten_truong.toLowerCase() + '...'"
                                    />

                                    <!-- Select Dropdown (Static or Dynamic Ref) -->
                                    <LTESelect2Option
                                        v-else-if="['select', 'multiselect'].includes(f.kieu_du_lieu)"
                                        v-model="recordForm.attributes[f.ma_truong]"
                                        :init-value="recordForm.attributes[f.ma_truong]"
                                        :label="f.ten_truong"
                                        :disabled="recordForm.id && (f.cho_phep_chinh_sua === false || f.cho_phep_chinh_sua === 0 || f.cho_phep_chinh_sua === '0')"
                                        :placeholder="'-- Chọn ' + f.ten_truong + ' --'"
                                        :data="getOptionsForField(f)"
                                        :multiple="f.kieu_du_lieu === 'multiselect'"
                                        :close-on-select="f.kieu_du_lieu !== 'multiselect'"
                                        :allow-clear="true"
                                        :enable-data-watch="true"
                                    />

                                    <!-- Input for File / Image via LTEFilePond Component -->
                                    <LTEFilePond
                                        v-else-if="['file', 'image'].includes(f.kieu_du_lieu)"
                                        v-model="recordForm.attributes[f.ma_truong]"
                                        :label="f.ten_truong"
                                        :accepted-file-types="f.kieu_du_lieu === 'image' ? ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/*'] : null"
                                        :is-disabled="recordForm.id && (f.cho_phep_chinh_sua === false || f.cho_phep_chinh_sua === 0 || f.cho_phep_chinh_sua === '0')"
                                        folder="dynamic_uploads"
                                    />

                                    <!-- Input for Boolean -->
                                    <div v-else-if="f.kieu_du_lieu === 'boolean'" class="mb-2">
                                        <label class="font-weight-bold small text-muted text-xs mb-1 block">{{ f.ten_truong }}</label>
                                        <div class="flex items-center gap-2 pt-0.5">
                                            <input 
                                                type="checkbox" 
                                                v-model="recordForm.attributes[f.ma_truong]"
                                                :disabled="recordForm.id && (f.cho_phep_chinh_sua === false || f.cho_phep_chinh_sua === 0 || f.cho_phep_chinh_sua === '0')"
                                                class="w-4 h-4 text-blue-600 rounded disabled:cursor-not-allowed"
                                            />
                                            <span class="text-xs font-semibold text-slate-700 mb-0">Kích hoạt / Đạt</span>
                                        </div>
                                    </div>

                                    <!-- Input for Color -->
                                    <div v-else-if="f.kieu_du_lieu === 'color'">
                                        <LTEInput 
                                            v-model="recordForm.attributes[f.ma_truong]"
                                            :label="f.ten_truong"
                                            type="color"
                                            :is-disabled="recordForm.id && (f.cho_phep_chinh_sua === false || f.cho_phep_chinh_sua === 0 || f.cho_phep_chinh_sua === '0')"
                                            class="w-24"
                                        />
                                    </div>

                                    <!-- Default Input (text, number, date, datetime, email, url) -->
                                    <LTEInput 
                                        v-else
                                        v-model="recordForm.attributes[f.ma_truong]" 
                                        :label="f.ten_truong"
                                        :is-disabled="recordForm.id && (f.cho_phep_chinh_sua === false || f.cho_phep_chinh_sua === 0 || f.cho_phep_chinh_sua === '0')"
                                        :type="f.kieu_du_lieu === 'number' ? 'number' : (f.kieu_du_lieu === 'date' ? 'date' : (f.kieu_du_lieu === 'datetime' ? 'datetime-local' : (f.kieu_du_lieu === 'email' ? 'email' : 'text')))" 
                                        :placeholder="'Nhập ' + f.ten_truong.toLowerCase() + '...'" 
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </LTEModal>

                <!-- LIGHTBOX IMAGE PREVIEW MODAL -->
                <div v-if="previewImageUrl" class="fixed inset-0 z-[9999] bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4" @click.self="previewImageUrl = null">
                    <div class="relative max-w-3xl w-full bg-white rounded-2xl p-2 shadow-2xl overflow-hidden animate-fadeIn">
                        <button @click="previewImageUrl = null" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-900/60 text-white flex items-center justify-center hover:bg-slate-900 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                        <img :src="previewImageUrl" class="w-full max-h-[80vh] object-contain rounded-xl" />
                    </div>
                </div>
                <!-- MODAL: BATCH IMPORT DỮ LIỆU -->
                <div v-if="showImportModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full p-6 border border-slate-100 animate-fadeIn max-h-[92vh] overflow-y-auto">
                        <!-- Modal Header -->
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg mb-0 flex items-center gap-2">
                                    <i class="fas fa-file-import text-blue-600"></i> Import Dữ liệu Đối tượng
                                </h3>
                                <p class="text-xs text-slate-500 mb-0 mt-0.5" v-if="currentRecordType">
                                    Loại đối tượng: <strong class="text-slate-800">{{ currentRecordType.ten_loai }}</strong> (Mã: <code>{{ currentRecordType.ma_loai }}</code>)
                                </p>
                            </div>
                            <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <!-- Step Indicator -->
                        <div class="flex items-center justify-center mb-5 gap-3">
                            <div :class="['flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold transition-all', importStep === 1 ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500']">
                                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-xs">1</span>
                                <span>Chọn File & Chế độ</span>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                            <div :class="['flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold transition-all', importStep === 2 ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500']">
                                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-xs">2</span>
                                <span>Xem trước & Kiểm tra dữ liệu</span>
                            </div>
                        </div>

                        <!-- STEP 1: UPLOAD & MODE SELECTION -->
                        <div v-if="importStep === 1" class="space-y-5">
                            <!-- Download Sample Excel Banner -->
                            <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm flex-shrink-0">
                                        <i class="fas fa-file-excel"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-slate-800 text-sm mb-0">Tải tệp mẫu chuẩn Excel (.xlsx)</h5>
                                        <p class="text-xs text-slate-600 mb-0 mt-0.5">
                                            Tệp mẫu được tự động sinh dựa trên thuộc tính động đã cấu hình của loại đối tượng này.
                                        </p>
                                    </div>
                                </div>
                                <button @click="downloadImportTemplate()" class="btn btn-primary btn-sm px-3 font-weight-bold shadow-sm flex-shrink-0">
                                    <i class="fas fa-download mr-1"></i> Tải File Mẫu
                                </button>
                            </div>

                            <!-- Upload Dropzone -->
                            <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-6 text-center bg-slate-50/50 hover:bg-blue-50/30 transition-all cursor-pointer relative" @click="$refs.fileInput.click()">
                                <input ref="fileInput" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="onFileSelect" />
                                <div v-if="!selectedFile">
                                    <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-3 text-2xl">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm mb-1">Nhấp vào đây hoặc kéo thả file Excel / CSV vào</h5>
                                    <p class="text-xs text-slate-500 mb-0">Hỗ trợ định dạng: <code>.xlsx</code>, <code>.xls</code>, <code>.csv</code> (Dung lượng tối đa 10MB)</p>
                                </div>
                                <div v-else class="flex items-center justify-center gap-3 py-2">
                                    <i class="fas fa-file-excel text-emerald-600 text-3xl"></i>
                                    <div class="text-left">
                                        <p class="font-bold text-slate-900 text-sm mb-0">{{ selectedFile.name }}</p>
                                        <span class="text-xs text-slate-500">{{ (selectedFile.size / 1024).toFixed(1) }} KB</span>
                                    </div>
                                    <button @click.stop="selectedFile = null" class="ml-4 text-slate-400 hover:text-red-600 p-1">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Import Mode Options -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Chọn quy tắc xử lý khi trùng lặp (Mã đối tượng)</label>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <!-- Option 1: Upsert -->
                                    <label :class="['border p-3 rounded-xl cursor-pointer transition-all flex items-start gap-2.5', importMode === 'upsert' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-500/20' : 'bg-white border-slate-200 hover:border-slate-300']">
                                        <input type="radio" v-model="importMode" value="upsert" class="mt-0.5 text-blue-600 focus:ring-blue-500" />
                                        <div>
                                            <span class="font-bold text-slate-900 text-xs block">🔄 Thêm mới & Cập nhật</span>
                                            <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Tự động thêm mới nếu chưa có, cập nhật dữ liệu nếu mã đã tồn tại.</span>
                                        </div>
                                    </label>

                                    <!-- Option 2: Insert new only -->
                                    <label :class="['border p-3 rounded-xl cursor-pointer transition-all flex items-start gap-2.5', importMode === 'insert_new' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-500/20' : 'bg-white border-slate-200 hover:border-slate-300']">
                                        <input type="radio" v-model="importMode" value="insert_new" class="mt-0.5 text-blue-600 focus:ring-blue-500" />
                                        <div>
                                            <span class="font-bold text-slate-900 text-xs block">➕ Chỉ Thêm mới</span>
                                            <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Thêm các bản ghi mới, tự động bỏ qua nếu mã đã tồn tại trong DB.</span>
                                        </div>
                                    </label>

                                    <!-- Option 3: Update existing only -->
                                    <label :class="['border p-3 rounded-xl cursor-pointer transition-all flex items-start gap-2.5', importMode === 'update_existing' ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-500/20' : 'bg-white border-slate-200 hover:border-slate-300']">
                                        <input type="radio" v-model="importMode" value="update_existing" class="mt-0.5 text-blue-600 focus:ring-blue-500" />
                                        <div>
                                            <span class="font-bold text-slate-900 text-xs block">✏️ Chỉ Cập nhật</span>
                                            <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Cập nhật dữ liệu cho mã đã có, bỏ qua nếu mã chưa tồn tại.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Footer Buttons -->
                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <LTEButton 
                                    variant="light" 
                                    text="Hủy" 
                                    class="btn-sm text-xs font-weight-bold px-3 border" 
                                    @click="showImportModal = false" 
                                />
                                <LTEButton 
                                    variant="primary" 
                                    :icon="previewLoading ? 'fas fa-spinner fa-spin' : 'fas fa-search'" 
                                    :text="previewLoading ? 'Đang đọc file...' : 'Kiểm tra & Xem trước Dữ liệu'" 
                                    class="btn-sm text-xs font-weight-bold px-3 shadow-sm" 
                                    :is-disabled="!selectedFile || previewLoading" 
                                    @click="runPreviewImport" 
                                />
                            </div>
                        </div>

                        <!-- STEP 2: PREVIEW & VALIDATION TABLE -->
                        <div v-else class="space-y-4">
                            <!-- Stat Pills Summary -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="bg-slate-100 p-3 rounded-xl border border-slate-200 text-center">
                                    <span class="text-xs text-slate-500 font-bold uppercase block">Tổng số hàng</span>
                                    <span class="text-lg font-extrabold text-slate-800">{{ previewData.total_rows }}</span>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-200 text-center">
                                    <span class="text-xs text-blue-600 font-bold uppercase block">➕ Thêm mới</span>
                                    <span class="text-lg font-extrabold text-blue-700">{{ previewData.new_count }}</span>
                                </div>
                                <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-200 text-center">
                                    <span class="text-xs text-emerald-600 font-bold uppercase block">🔄 Cập nhật</span>
                                    <span class="text-lg font-extrabold text-emerald-700">{{ previewData.update_count }}</span>
                                </div>
                                <div class="bg-red-50 p-3 rounded-xl border border-red-200 text-center">
                                    <span class="text-xs text-red-600 font-bold uppercase block">⚠️ Lỗi / Không hợp lệ</span>
                                    <span class="text-lg font-extrabold text-red-700">{{ previewData.error_count }}</span>
                                </div>
                            </div>

                            <!-- Error Auto-clear Option Banner -->
                            <div v-if="previewData.error_count > 0" class="bg-amber-50/90 border border-amber-200/80 rounded-xl p-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle text-amber-600 text-sm"></i>
                                    <span class="text-xs text-amber-900 font-semibold">Có {{ previewData.error_count }} hàng chứa giá trị thuộc tính không hợp lệ.</span>
                                </div>
                                <label class="d-inline-flex align-items-center gap-2 cursor-pointer mb-0">
                                    <input type="checkbox" v-model="autoClearErrors" class="w-4 h-4 text-blue-600 rounded cursor-pointer" />
                                    <span class="text-xs font-bold text-amber-900">Tự động bỏ trống ô bị lỗi thuộc tính & vẫn import bản ghi đó</span>
                                </label>
                            </div>

                            <!-- Preview Data Table -->
                            <div class="border border-slate-200 rounded-xl overflow-hidden max-h-[380px] overflow-y-auto">
                                <table class="table table-hover table-striped app-table mb-0" style="font-size: 0.8rem;">
                                    <thead class="thead-dark sticky-top">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">#</th>
                                            <th style="width: 130px;" class="text-center">Trạng thái</th>
                                            <th>Mã đối tượng</th>
                                            <th>Tên hiển thị</th>
                                            <th v-if="currentRecordType && ['giang_vien', 'sinh_vien'].includes(currentRecordType.ma_loai)">Email</th>
                                            <th v-for="f in previewData.fields" :key="f.id">{{ f.ten_truong }}</th>
                                            <th>Ghi chú / Lỗi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="r in previewData.rows" :key="r.row_index" :class="r.action_type === 'error' ? 'bg-red-50/60' : ''">
                                            <td class="text-center font-weight-bold text-muted">{{ r.row_index }}</td>
                                            <td class="text-center">
                                                <AppBadge v-if="r.action_type === 'new'" variant="primary">➕ Thêm mới</AppBadge>
                                                <AppBadge v-else-if="r.action_type === 'update'" variant="success">🔄 Cập nhật</AppBadge>
                                                <AppBadge v-else variant="danger">⚠️ Lỗi</AppBadge>
                                            </td>
                                            <td class="font-weight-bold text-dark">{{ r.ma_doi_tuong || '-' }}</td>
                                            <td>{{ r.ten_hien_thi || '-' }}</td>
                                            <td v-if="currentRecordType && ['giang_vien', 'sinh_vien'].includes(currentRecordType.ma_loai)">{{ r.email || '-' }}</td>
                                            <td v-for="f in previewData.fields" :key="f.id">
                                                {{ r.attributes[f.ma_truong] || '-' }}
                                            </td>
                                            <td>
                                                <div v-if="r.errors && r.errors.length > 0" class="text-danger font-weight-bold text-xs">
                                                    <div v-for="(err, i) in r.errors" :key="i">• {{ err }}</div>
                                                </div>
                                                <span v-else class="text-muted text-xs">Hợp lệ</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer Action Buttons -->
                            <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                                <LTEButton 
                                    variant="outline-secondary" 
                                    icon="fas fa-arrow-left" 
                                    text="Quay lại chọn file" 
                                    class="btn-sm text-xs font-weight-bold px-2.5" 
                                    @click="importStep = 1" 
                                />

                                <div class="flex gap-2">
                                    <LTEButton 
                                        variant="light" 
                                        text="Hủy" 
                                        class="btn-sm text-xs font-weight-bold px-3 border" 
                                        @click="showImportModal = false" 
                                    />
                                    <LTEButton 
                                        variant="success" 
                                        :icon="processLoading ? 'fas fa-spinner fa-spin' : 'fas fa-check-circle'" 
                                        :text="processLoading ? 'Đang import...' : `Xác Nhận Import (${previewData.new_count + previewData.update_count} bản ghi)`" 
                                        class="btn-sm text-xs font-weight-bold px-3 shadow-sm" 
                                        :is-disabled="processLoading || (previewData.new_count === 0 && previewData.update_count === 0)" 
                                        @click="executeProcessImport" 
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>
        </template>
    </LTEContentWrapper>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '@/store/auth'
import LTESelect2Option from '@/components/controls/LTESelect2Option.vue'
import LTEInput from '@/components/controls/LTEInput.vue'
import LTETextArea from '@/components/controls/LTETextArea.vue'
import LTEModal from '@/components/controls/LTEModal.vue'
import LTEButton from '@/components/controls/LTEButton.vue'
import IconButton from '@/components/controls/IconButton.vue'

export default {
    name: 'DynamicObjects',
    components: { LTEModal, LTESelect2Option, LTEInput, LTETextArea, LTEButton, IconButton },
    data() {
        return {
            activeTab: 'types', // types | fields | records
            types: [],
            fields: [],
            records: [],
            recordFields: [],
            currentRecordType: null,
            selectedTypeId: null,
            previewImageUrl: null,

            // Drag and drop state
            draggedField: null,
            dragOverField: null,
            dragOverGroup: null,
            dragOverPosition: 'above',

            // Import State
            showImportModal: false,
            importStep: 1, // 1: Select file & mode, 2: Preview
            importMode: 'upsert', // upsert | insert_new | update_existing
            autoClearErrors: false,
            selectedFile: null,
            previewLoading: false,
            processLoading: false,
            previewData: {
                total_rows: 0,
                new_count: 0,
                update_count: 0,
                error_count: 0,
                fields: [],
                rows: []
            },

            // Modals
            showTypeModal: false,
            typeForm: {
                id: null,
                ma_loai: '',
                ten_loai: '',
                mo_ta: ''
            },

            showFieldModal: false,
            newOptionText: '',
            optionList: [],
            fieldForm: {
                id: null,
                loai_doi_tuong_id: null,
                ma_truong: '',
                ten_truong: '',
                phan_nhom: 'Thông tin bổ sung',
                kieu_du_lieu: 'text',
                thu_tu: 1,
                lien_ket_loai_doi_tuong_id: null,
                lua_chon: '',
                bat_buoc: false,
                cau_hinh: {
                    tieu_chuan_gia_tri: 'ma',
                    dung_luong_toi_da: 10,
                    dinh_dang_tep: '',
                    mau_ten_tep: '',
                    gia_tri_mac_dinh: ''
                },
                trang_thai: true
            },

            showRecordModal: false,
            recordForm: {
                id: null,
                loai_doi_tuong_id: null,
                ma_doi_tuong: '',
                ten_hien_thi: '',
                email: '',
                attributes: {},
                fileObjects: {}
            },

            // Layout Builder State
            layoutFields: [],
            previewDevice: 'desktop',
            savingLayout: false,

            // Data type options
            dataTypeOptions: [
                { value: 'text', text: 'Chuỗi văn bản' },
                { value: 'textarea', text: 'Văn bản nhiều dòng' },
                { value: 'number', text: 'Số nguyên / Số thực' },
                { value: 'date', text: 'Ngày tháng' },
                { value: 'datetime', text: 'Ngày và Giờ' },
                { value: 'select', text: 'Danh sách chọn đơn' },
                { value: 'multiselect', text: 'Chọn nhiều tùy chọn' },
                { value: 'file', text: 'Tệp đính kèm / Tài liệu' },
                { value: 'image', text: 'Hình ảnh / Chân dung' },
                { value: 'boolean', text: 'Đúng / Sai' },
                { value: 'url', text: 'Liên kết Web' },
                { value: 'email', text: 'Địa chỉ Email' },
                { value: 'color', text: 'Mã màu đại diện' }
            ]
        }
    },
    computed: {
        typeSelectOptions() {
            return this.types.map(t => ({
                value: t.id,
                text: `${t.ten_loai} (${t.ma_loai})`
            }))
        },

        groupedRecordFields() {
            const groups = {}
            this.recordFields.forEach(f => {
                const groupName = f.phan_nhom || 'Thông tin bổ sung'
                if (!groups[groupName]) {
                    groups[groupName] = []
                }
                groups[groupName].push(f)
            })
            return groups
        },

        groupedFields() {
            const groups = {}
            this.fields.forEach(f => {
                const groupName = f.phan_nhom || 'Thông tin bổ sung'
                if (!groups[groupName]) {
                    groups[groupName] = []
                }
                groups[groupName].push(f)
            })
            return groups
        },

        groupedLayoutFields() {
            const groups = {}
            this.layoutFields.forEach(f => {
                const groupName = f.phan_nhom || 'Thông tin bổ sung'
                if (!groups[groupName]) {
                    groups[groupName] = []
                }
                groups[groupName].push(f)
            })
            return groups
        }
    },
    watch: {
        '$route.query'(newQuery) {
            if (newQuery.tab && ['types', 'fields', 'records', 'layout'].includes(newQuery.tab)) {
                this.activeTab = newQuery.tab
            }
            if (newQuery.loai_doi_tuong_id && this.types.some(t => t.id == newQuery.loai_doi_tuong_id)) {
                this.selectedTypeId = parseInt(newQuery.loai_doi_tuong_id)
            }
            if (this.activeTab === 'fields') {
                this.loadFields()
            } else if (this.activeTab === 'records') {
                this.loadRecords()
            } else if (this.activeTab === 'layout') {
                this.loadLayoutConfig()
            }
        }
    },
    mounted() {
        this.loadTypes()
    },
    methods: {
        hasPermission(perm) {
            return useAuthStore().hasPermission(perm)
        },
        // Import Methods
        downloadImportTemplate() {
            if (!this.selectedTypeId) {
                if (window.func && window.func.toastError) {
                    window.func.toastError('Vui lòng chọn loại đối tượng trước!');
                }
                return;
            }
            const url = route('DynamicObjectController.exportImportTemplate', { loai_doi_tuong_id: this.selectedTypeId });
            window.open(url, '_blank');
        },

        goToImportPage() {
            if (!this.selectedTypeId) {
                if (window.func && window.func.toastError) {
                    window.func.toastError('Vui lòng chọn loại đối tượng trước!');
                }
                return;
            }
            this.$router.push({
                name: 'router-portal-doi-tuong-dong-import',
                query: { loai_doi_tuong_id: this.selectedTypeId }
            });
        },

        exportRecords() {
            if (!this.selectedTypeId) {
                if (window.func && window.func.toastError) {
                    window.func.toastError('Vui lòng chọn loại đối tượng để xuất dữ liệu!');
                }
                return;
            }
            const url = route('DynamicObjectController.exportRecords', { loai_doi_tuong_id: this.selectedTypeId });
            window.open(url, '_blank');
        },

        openImportModal() {
            if (!this.selectedTypeId) {
                if (window.func && window.func.toastError) {
                    window.func.toastError('Vui lòng chọn loại đối tượng để import!');
                }
                return;
            }
            this.importStep = 1;
            this.selectedFile = null;
            this.importMode = 'upsert';
            this.previewData = { total_rows: 0, new_count: 0, update_count: 0, error_count: 0, fields: [], rows: [] };
            this.showImportModal = true;
        },

        onFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file;
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

                const response = await axios.post(route('DynamicObjectController.putPreviewImport'), formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (response.data.status === 200) {
                    this.previewData = response.data.data;
                    this.importStep = 2;
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
                    this.showImportModal = false;
                    this.fetchRecords();
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
        },
        // Drag & Drop handlers
        onDragStart(event, field, groupName) {
            this.draggedField = field
            event.dataTransfer.effectAllowed = 'move'
            event.dataTransfer.setData('text/plain', String(field.id))
        },

        onDragOverField(event, targetField, groupName) {
            event.preventDefault()
            this.dragOverField = targetField
            this.dragOverGroup = groupName

            if (event.currentTarget) {
                const rect = event.currentTarget.getBoundingClientRect()
                const offsetY = event.clientY - rect.top
                this.dragOverPosition = offsetY > rect.height / 2 ? 'below' : 'above'
            }
        },

        onDragOverGroupHeader(event, groupName) {
            event.preventDefault()
            this.dragOverField = null
            this.dragOverGroup = groupName
            this.dragOverPosition = 'above'
        },

        onDragLeave() {
            this.dragOverField = null
            this.dragOverGroup = null
            this.dragOverPosition = 'above'
        },

        onDragEnd() {
            this.draggedField = null
            this.dragOverField = null
            this.dragOverGroup = null
            this.dragOverPosition = 'above'
        },

        // Helper to re-index all fields sequentially (1, 2, 3, 4...) across visual groups
        reindexFields() {
            const groupOrder = []
            const grouped = {}
            this.fields.forEach(f => {
                const gName = f.phan_nhom || 'Thông tin bổ sung'
                if (!grouped[gName]) {
                    grouped[gName] = []
                    groupOrder.push(gName)
                }
                grouped[gName].push(f)
            })

            const newFieldsList = []
            let seq = 1
            groupOrder.forEach(gName => {
                grouped[gName].forEach(f => {
                    f.thu_tu = seq++
                    newFieldsList.push(f)
                })
            })

            this.fields = newFieldsList
            return newFieldsList.map(f => ({
                id: f.id,
                thu_tu: f.thu_tu,
                phan_nhom: f.phan_nhom || 'Thông tin bổ sung'
            }))
        },

        async onDrop(event, targetField, targetGroup) {
            event.preventDefault()
            if (!this.draggedField) return

            const srcField = this.draggedField
            const newGroup = targetGroup || (targetField ? targetField.phan_nhom : srcField.phan_nhom)
            const insertBelow = this.dragOverPosition === 'below'

            // Update group assignment
            srcField.phan_nhom = newGroup

            // Remove dragged item from current position
            const srcIndex = this.fields.findIndex(f => f.id === srcField.id)
            if (srcIndex !== -1) {
                this.fields.splice(srcIndex, 1)
            }

            // Insert into target position
            if (targetField) {
                let targetIndex = this.fields.findIndex(f => f.id === targetField.id)
                if (targetIndex !== -1) {
                    if (insertBelow) {
                        targetIndex += 1
                    }
                    this.fields.splice(targetIndex, 0, srcField)
                } else {
                    this.fields.push(srcField)
                }
            } else {
                // Dropped onto group header row -> insert at end of that group
                const lastInGroupIndex = this.fields.findLastIndex(f => (f.phan_nhom || 'Thông tin bổ sung') === newGroup)
                if (lastInGroupIndex !== -1) {
                    this.fields.splice(lastInGroupIndex + 1, 0, srcField)
                } else {
                    this.fields.push(srcField)
                }
            }

            // Re-sequence numbers 1, 2, 3, 4, 5, 6, 7... sequentially
            const orders = this.reindexFields()

            this.onDragEnd()

            // Save new sequential order to backend database
            try {
                const res = await axios.post(route('DynamicObjectController.updateFieldOrders'), { orders })
                if (res.data.status === 200 && window.toastr) {
                    window.toastr.success('Cập nhật thứ tự và đánh số lại thành công!')
                }
            } catch (err) {
                console.error(err)
            }
        },

        onDropLayout(event, targetField, targetGroup) {
            event.preventDefault()
            if (!this.draggedField) return

            const srcField = this.draggedField
            const newGroup = targetGroup || (targetField ? targetField.phan_nhom : srcField.phan_nhom)
            const insertBelow = this.dragOverPosition === 'below'

            srcField.phan_nhom = newGroup

            // Remove dragged item from layoutFields
            const srcIndex = this.layoutFields.findIndex(f => f.id === srcField.id)
            if (srcIndex !== -1) {
                this.layoutFields.splice(srcIndex, 1)
            }

            // Insert into target position in layoutFields
            if (targetField) {
                let targetIndex = this.layoutFields.findIndex(f => f.id === targetField.id)
                if (targetIndex !== -1) {
                    if (insertBelow) {
                        targetIndex += 1
                    }
                    this.layoutFields.splice(targetIndex, 0, srcField)
                } else {
                    this.layoutFields.push(srcField)
                }
            } else {
                const lastInGroupIndex = this.layoutFields.findLastIndex(f => (f.phan_nhom || 'Thông tin bổ sung') === newGroup)
                if (lastInGroupIndex !== -1) {
                    this.layoutFields.splice(lastInGroupIndex + 1, 0, srcField)
                } else {
                    this.layoutFields.push(srcField)
                }
            }

            this.onDragEnd()
        },

        getColClass(colSpan) {
            const span = parseInt(colSpan || 6)
            if (span === 12) return 'col-12'
            if (span === 6) return 'col-12 col-md-6'
            if (span === 4) return 'col-12 col-md-4'
            if (span === 3) return 'col-12 col-md-3'
            return 'col-12 col-md-6'
        },

        setFieldColSpan(field, span) {
            field.col_span = span
        },

        async loadLayoutConfig() {
            if (!this.selectedTypeId) return
            try {
                const res = await axios.get(route('DynamicObjectController.getLayoutConfig'), {
                    params: { loai_doi_tuong_id: this.selectedTypeId }
                })
                if (res.data.status === 200) {
                    this.layoutFields = res.data.data.fields || []
                }
            } catch (err) {
                console.error(err)
            }
        },

        async saveLayoutConfig() {
            if (!this.selectedTypeId || this.savingLayout) return
            this.savingLayout = true
            try {
                const layoutItems = this.layoutFields.map((f, idx) => ({
                    id: f.id,
                    thu_tu: idx + 1,
                    col_span: f.col_span || 6,
                    phan_nhom: f.phan_nhom
                }))
                const res = await axios.post(route('DynamicObjectController.putLayoutConfig'), {
                    loai_doi_tuong_id: this.selectedTypeId,
                    layout_items: layoutItems
                })
                if (res.data.status === 200) {
                    this.loadLayoutConfig()
                    this.$func.toastSuccess(res.data.message)
                } else {
                    this.$func.toastError(res.data)
                }
            } catch (err) {
                console.error(err)
            } finally {
                this.savingLayout = false
            }
        },

        switchTab(tabName) {
            this.activeTab = tabName
            if (tabName === 'types') {
                this.loadTypes()
            } else if (tabName === 'fields') {
                this.loadFields()
            } else if (tabName === 'records') {
                this.loadRecords()
            } else if (tabName === 'layout') {
                this.loadLayoutConfig()
            }
        },

        async loadTypes() {
            try {
                const res = await axios.get(route('DynamicObjectController.getTypes'))
                if (res.data.status === 200) {
                    this.types = res.data.data
                    const queryTab = this.$route?.query?.tab
                    const queryTypeId = this.$route?.query?.loai_doi_tuong_id

                    if (queryTab && ['types', 'fields', 'records', 'layout'].includes(queryTab)) {
                        this.activeTab = queryTab
                    }

                    if (queryTypeId && this.types.some(t => t.id == queryTypeId)) {
                        this.selectedTypeId = parseInt(queryTypeId)
                    } else if (this.types.length > 0 && !this.selectedTypeId) {
                        this.selectedTypeId = this.types[0].id
                    }

                    if (this.activeTab === 'fields') {
                        this.loadFields()
                    } else if (this.activeTab === 'records') {
                        this.loadRecords()
                    } else if (this.activeTab === 'layout') {
                        this.loadLayoutConfig()
                    }
                }
            } catch (err) {
                console.error(err)
            }
        },

        onTypeSelectChange(val) {
            this.selectedTypeId = val
            if (this.activeTab === 'fields') {
                this.loadFields()
            } else if (this.activeTab === 'records') {
                this.loadRecords()
            } else if (this.activeTab === 'layout') {
                this.loadLayoutConfig()
            }
        },

        configureFields(type) {
            this.selectedTypeId = type.id
            this.activeTab = 'fields'
            this.loadFields()
        },

        viewRecords(type) {
            this.selectedTypeId = type.id
            this.activeTab = 'records'
            this.loadRecords()
        },

        async loadFields() {
            if (!this.selectedTypeId && this.types && this.types.length > 0) {
                this.selectedTypeId = this.types[0].id
            }
            if (!this.selectedTypeId) return
            try {
                const res = await axios.get(route('DynamicObjectController.getFields'), {
                    params: { loai_doi_tuong_id: this.selectedTypeId }
                })
                if (res.data.status === 200) {
                    this.fields = res.data.data
                    this.reindexFields()
                }
            } catch (err) {
                console.error(err)
            }
        },

        async loadRecords() {
            if (!this.selectedTypeId && this.types && this.types.length > 0) {
                this.selectedTypeId = this.types[0].id
            }
            if (!this.selectedTypeId) return
            try {
                const res = await axios.get(route('DynamicObjectController.getRecords'), {
                    params: { loai_doi_tuong_id: this.selectedTypeId }
                })
                if (res.data.status === 200) {
                    this.currentRecordType = res.data.data.type
                    this.recordFields = res.data.data.fields
                    this.records = res.data.data.records
                }
            } catch (err) {
                console.error(err)
            }
        },

        getCodeHeaderLabel() {
            if (!this.currentRecordType) return 'MÃ / ĐỊNH DANH'
            const code = this.currentRecordType.ma_loai
            if (code === 'giang_vien') return 'MÃ SỐ GIẢNG VIÊN (MSGV)'
            if (code === 'sinh_vien') return 'MÃ SỐ SINH VIÊN (MSSV)'
            if (code === 'phong_hoc') return 'MÃ PHÒNG / GIẢNG ĐƯỜNG'
            if (code === 'thiet_bi') return 'MÃ THIẾT BỊ / SÊ-RI'
            return 'MÃ / ĐỊNH DANH'
        },

        getNameHeaderLabel() {
            if (!this.currentRecordType) return 'TÊN HIỂN THỊ'
            const code = this.currentRecordType.ma_loai
            if (code === 'giang_vien') return 'HỌ VÀ TÊN GIẢNG VIÊN'
            if (code === 'sinh_vien') return 'HỌ VÀ TÊN SINH VIÊN'
            if (code === 'phong_hoc') return 'TÊN PHÒNG HỌC'
            if (code === 'thiet_bi') return 'TÊN THIẾT BỊ / TÀI SẢN'
            return 'TÊN HIỂN THỊ'
        },

        getEmailHeaderLabel() {
            if (!this.currentRecordType) return 'GMAIL / EMAIL'
            const code = this.currentRecordType.ma_loai
            if (code === 'giang_vien') return 'GMAIL / EMAIL CÁ NHÂN'
            if (code === 'sinh_vien') return 'EMAIL SINH VIÊN'
            return 'GMAIL / EMAIL'
        },

        getFileIconClass(urlStr) {
            if (!urlStr) return 'fas fa-file'
            const ext = urlStr.split('.').pop().toLowerCase()
            if (['pdf'].includes(ext)) return 'fas fa-file-pdf text-rose-500'
            if (['doc', 'docx'].includes(ext)) return 'fas fa-file-word text-blue-600'
            if (['xls', 'xlsx'].includes(ext)) return 'fas fa-file-excel text-emerald-600'
            if (['zip', 'rar', '7z'].includes(ext)) return 'fas fa-file-archive text-amber-500'
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'fas fa-file-image text-purple-500'
            return 'fas fa-file-alt text-slate-500'
        },

        getFileNameFromUrl(urlStr) {
            if (!urlStr) return 'Tệp đính kèm'
            const parts = urlStr.split('/')
            return parts[parts.length - 1]
        },

        clearFileAttribute(fieldKey) {
            this.recordForm.attributes[fieldKey] = ''
            delete this.recordForm.fileObjects[fieldKey]
        },

        getDataTypeLabel(type) {
            const item = this.dataTypeOptions.find(o => o.value === type)
            return item ? item.text : type
        },

        getDataTypeIcon(type) {
            const icons = {
                text: 'fas fa-font',
                textarea: 'fas fa-align-left',
                number: 'fas fa-hashtag',
                date: 'fas fa-calendar-alt',
                datetime: 'fas fa-clock',
                select: 'fas fa-list-ul',
                multiselect: 'fas fa-tasks',
                file: 'fas fa-file-alt',
                image: 'fas fa-image',
                boolean: 'fas fa-check-square',
                url: 'fas fa-link',
                email: 'fas fa-envelope',
                color: 'fas fa-palette'
            }
            return icons[type] || 'fas fa-info-circle'
        },

        // Type Modal
        async openTypeModal(type = null) {
            if (type) {
                this.typeForm = { id: type.id, ma_loai: type.ma_loai, ten_loai: type.ten_loai, mo_ta: type.mo_ta }
            } else {
                this.typeForm = { id: null, ma_loai: '', ten_loai: '', mo_ta: '' }
            }
            this.$refs.typeModal.$data.title = type ? 'Chỉnh sửa Loại đối tượng' : 'Thêm Loại đối tượng mới'
            this.$refs.typeModal.$data.save = 'Lưu thông tin'
            const res = await this.$refs.typeModal.openModal()
            if (res) this.saveType()
        },

        async saveType() {
            try {
                const res = await axios.post(route('DynamicObjectController.putType'), this.typeForm)
                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.$refs.typeModal.closeModal()
                    this.loadTypes()
                }
            } catch (err) {
                const msg = err.response?.data?.message || 'Có lỗi xảy ra'
                if (window.func && window.func.toastError) {
                    window.func.toastError(msg)
                } else {
                    alert(msg)
                }
            }
        },

        async deleteType(type) {
            if (!confirm(`Bạn có chắc chắn muốn xóa loại đối tượng "${type.ten_loai}"?`)) return
            try {
                const res = await axios.delete(route('DynamicObjectController.deleteType', { id: type.id }))
                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.loadTypes()
                }
            } catch (err) {
                const msg = err.response?.data?.message || 'Có lỗi xảy ra'
                if (window.func && window.func.toastError) {
                    window.func.toastError(msg)
                }
            }
        },

        // Field Modal & Reordering
        async openFieldModal(field = null) {
            this.newOptionText = ''
            if (field) {
                let parsedConfig = {
                    tieu_chuan_gia_tri: 'ma',
                    dung_luong_toi_da: 10,
                    dinh_dang_tep: '',
                    mau_ten_tep: '',
                    gia_tri_mac_dinh: ''
                }
                if (field.cau_hinh) {
                    try {
                        const c = typeof field.cau_hinh === 'string' ? JSON.parse(field.cau_hinh) : field.cau_hinh
                        parsedConfig = { ...parsedConfig, ...c }
                    } catch (e) {}
                }

                this.fieldForm = {
                    id: field.id,
                    loai_doi_tuong_id: field.loai_doi_tuong_id,
                    ma_truong: field.ma_truong,
                    ten_truong: field.ten_truong,
                    phan_nhom: field.phan_nhom || 'Thông tin bổ sung',
                    kieu_du_lieu: field.kieu_du_lieu,
                    thu_tu: field.thu_tu || 1,
                    lien_ket_loai_doi_tuong_id: field.lien_ket_loai_doi_tuong_id || null,
                    lua_chon: field.lua_chon || '',
                    bat_buoc: !!field.bat_buoc,
                    cho_phep_chinh_sua: field.cho_phep_chinh_sua !== false && field.cho_phep_chinh_sua !== 0 && field.cho_phep_chinh_sua !== '0',
                    cau_hinh: parsedConfig,
                    trang_thai: !!field.trang_thai
                }
                this.parseOptionList(field.lua_chon)
            } else {
                this.fieldForm = {
                    id: null,
                    loai_doi_tuong_id: this.selectedTypeId,
                    ma_truong: '',
                    ten_truong: '',
                    phan_nhom: 'Thông tin bổ sung',
                    kieu_du_lieu: 'text',
                    thu_tu: this.fields.length + 1,
                    lien_ket_loai_doi_tuong_id: null,
                    lua_chon: '',
                    bat_buoc: false,
                    cho_phep_chinh_sua: true,
                    cau_hinh: {
                        tieu_chuan_gia_tri: 'ma',
                        dung_luong_toi_da: 10,
                        dinh_dang_tep: '',
                        mau_ten_tep: '',
                        gia_tri_mac_dinh: ''
                    },
                    trang_thai: true
                }
                this.optionList = []
            }
            this.$refs.fieldModal.$data.title = field ? 'Chỉnh sửa Thuộc tính' : 'Thêm Thuộc tính động mới'
            this.$refs.fieldModal.$data.save = 'Lưu thuộc tính'
            const res = await this.$refs.fieldModal.openModal()
            if (res) this.saveField()
        },

        parseOptionList(rawChoices) {
            if (!rawChoices) {
                this.optionList = []
                return
            }
            try {
                const parsed = JSON.parse(rawChoices)
                if (Array.isArray(parsed)) {
                    this.optionList = parsed.map(o => typeof o === 'object' ? (o.text || o.value) : o)
                    return
                }
            } catch (e) {}
            
            if (typeof rawChoices === 'string') {
                this.optionList = rawChoices.split(',').map(s => s.trim()).filter(Boolean)
            } else {
                this.optionList = []
            }
        },

        addOptionChip() {
            const val = this.newOptionText.trim()
            if (val && !this.optionList.includes(val)) {
                this.optionList.push(val)
                this.newOptionText = ''
            }
        },

        removeOptionChip(index) {
            this.optionList.splice(index, 1)
        },

        getOptionsForField(f) {
            if (f.ref_options && f.ref_options.length > 0) {
                return f.ref_options;
            }
            if (f.ma_truong === 'gioi_tinh' && !f.lua_chon) {
                return [{ value: 'Nam', text: 'Nam' }, { value: 'Nữ', text: 'Nữ' }, { value: 'Khác', text: 'Khác' }];
            }
            try {
                if (!f.lua_chon) return [];
                const parsed = JSON.parse(f.lua_chon);
                return parsed.map(o => typeof o === 'object' ? o : { value: o, text: o });
            } catch (e) {
                if (typeof f.lua_chon === 'string') {
                    return f.lua_chon.split(',').map(s => {
                        const trimmed = s.trim();
                        return { value: trimmed, text: trimmed };
                    });
                }
                return [];
            }
        },

        async moveFieldUp(index) {
            if (index <= 0) return
            const temp = this.fields[index]
            this.fields[index] = this.fields[index - 1]
            this.fields[index - 1] = temp

            // Re-assign sequence numbers
            this.fields.forEach((f, i) => { f.thu_tu = i + 1 })
            await this.saveFieldOrder()
        },

        async moveFieldDown(index) {
            if (index >= this.fields.length - 1) return
            const temp = this.fields[index]
            this.fields[index] = this.fields[index + 1]
            this.fields[index + 1] = temp

            // Re-assign sequence numbers
            this.fields.forEach((f, i) => { f.thu_tu = i + 1 })
            await this.saveFieldOrder()
        },

        async saveFieldOrder() {
            try {
                const orders = this.fields.map((f, i) => ({ id: f.id, thu_tu: i + 1 }))
                const res = await axios.post(route('DynamicObjectController.updateFieldOrders'), { orders })
                if (res.data.status === 200 && window.func && window.func.toastSuccess) {
                    window.func.toastSuccess('Đã cập nhật thứ tự thuộc tính!')
                }
            } catch (err) {
                console.error(err)
            }
        },

        async saveField() {
            try {
                this.fieldForm.loai_doi_tuong_id = this.selectedTypeId
                // Sync optionList array to lua_chon JSON string
                if (['select', 'multiselect'].includes(this.fieldForm.kieu_du_lieu) && !this.fieldForm.lien_ket_loai_doi_tuong_id) {
                    this.fieldForm.lua_chon = JSON.stringify(this.optionList)
                }
                const res = await axios.post(route('DynamicObjectController.putField'), this.fieldForm)
                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.$refs.fieldModal.closeModal()
                    this.loadFields()
                }
            } catch (err) {
                const msg = err.response?.data?.message || 'Có lỗi xảy ra'
                if (window.func && window.func.toastError) {
                    window.func.toastError(msg)
                }
            }
        },

        async deleteField(field) {
            if (!confirm(`Bạn có chắc chắn muốn xóa thuộc tính "${field.ten_truong}"?`)) return
            try {
                const res = await axios.delete(route('DynamicObjectController.deleteField', { id: field.id }))
                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.loadFields()
                }
            } catch (err) {
                console.error(err)
            }
        },

        // Record Modal & File Selection
        onFileSelected(event, fieldKey) {
            const file = event.target.files[0]
            if (file) {
                this.recordForm.fileObjects[fieldKey] = file
                this.recordForm.attributes[fieldKey] = URL.createObjectURL(file)
            }
        },

        async openRecordModal(rec = null) {
            if (rec) {
                this.recordForm = {
                    id: rec.id,
                    loai_doi_tuong_id: this.selectedTypeId,
                    ma_doi_tuong: rec.ma_doi_tuong || '',
                    ten_hien_thi: rec.ten_hien_thi || '',
                    email: rec.email || '',
                    attributes: { ...rec.attributes },
                    fileObjects: {}
                }
            } else {
                this.recordForm = {
                    id: null,
                    loai_doi_tuong_id: this.selectedTypeId,
                    ma_doi_tuong: '',
                    ten_hien_thi: '',
                    email: '',
                    attributes: {},
                    fileObjects: {}
                }
            }
            const typeName = this.currentRecordType ? this.currentRecordType.ten_loai : ''
            this.$refs.recordModal.$data.title = (rec ? 'Chỉnh sửa Bản ghi' : 'Thêm Bản ghi mới') + (typeName ? ' - ' + typeName : '')
            this.$refs.recordModal.$data.save = 'Lưu bản ghi'
            const res = await this.$refs.recordModal.openModal()
            if (res) this.saveRecord()
        },

        async saveRecord() {
            try {
                const formData = new FormData()
                formData.append('loai_doi_tuong_id', this.selectedTypeId)
                if (this.recordForm.id) formData.append('id', this.recordForm.id)
                formData.append('ma_doi_tuong', this.recordForm.ma_doi_tuong)
                formData.append('ten_hien_thi', this.recordForm.ten_hien_thi)
                formData.append('email', this.recordForm.email)

                // Append attributes
                for (const key in this.recordForm.attributes) {
                    const val = this.recordForm.attributes[key]
                    if (val instanceof File) {
                        formData.append(`attributes[${key}]`, val)
                    } else if (this.recordForm.fileObjects[key]) {
                        formData.append(`attributes[${key}]`, this.recordForm.fileObjects[key])
                    } else {
                        formData.append(`attributes[${key}]`, val || '')
                    }
                }

                const res = await axios.post(route('DynamicObjectController.putRecord'), formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })

                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.$refs.recordModal.closeModal()
                    this.loadRecords()
                }
            } catch (err) {
                const msg = err.response?.data?.message || 'Có lỗi xảy ra'
                if (window.func && window.func.toastError) {
                    window.func.toastError(msg)
                }
            }
        },

        async deleteRecord(rec) {
            if (!confirm(`Xóa bản ghi "${rec.ten_hien_thi}"?`)) return
            try {
                const res = await axios.delete(route('DynamicObjectController.deleteRecord', { id: rec.id }), {
                    params: { loai_doi_tuong_id: this.selectedTypeId }
                })
                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.loadRecords()
                }
            } catch (err) {
                console.error(err)
            }
        }
    }
}
</script>

<style scoped>
/* Tabs */
.custom-tabs {
    border-bottom: 1px solid #cbd5e1 !important;
}
.custom-tabs .nav-link {
    color: #475569 !important;
    border: none !important;
    border-bottom: 2px solid transparent !important;
    padding: 0.5rem 1rem !important;
    background: transparent !important;
    transition: all 0.2s ease !important;
    border-radius: 0 !important;
}
.custom-tabs .nav-link:hover {
    color: #1f4068 !important;
    border-bottom-color: #cbd5e1 !important;
}
.custom-tabs .nav-link.active {
    color: #1f4068 !important;
    background-color: transparent !important;
    border-bottom: 2px solid #1f4068 !important;
}

/* Badges styling */
.badge {
    font-size: 0.7rem !important;
    font-weight: 500 !important;
    padding: 0.15rem 0.4rem !important;
    border-radius: 4px !important;
}

/* Force Blue Background Table Header with White Text */
.table thead,
.table thead tr,
.table thead th,
.table thead tr th {
    background-color: #007bff !important;
    background: #007bff !important;
    color: #ffffff !important;
    border-bottom: none !important;
    border-top: none !important;
    font-weight: 700 !important;
}

/* Drag & Drop Visual Effects */
.row-dragged {
    opacity: 0.35 !important;
    background-color: #f1f5f9 !important;
}
.row-drag-over-above {
    background-color: #dbeafe !important;
    border-top: 3px solid #2563eb !important;
}
.row-drag-over-below {
    background-color: #dbeafe !important;
    border-bottom: 3px solid #2563eb !important;
}
.group-drag-over {
    background-color: #bfdbfe !important;
}
.cursor-grab {
    cursor: grab;
}
.cursor-grab:active {
    cursor: grabbing;
}
</style>
