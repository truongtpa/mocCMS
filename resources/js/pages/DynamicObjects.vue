<template>
    <LTEContentWrapper :header="false">
        <template #content>
            <div class="dynamic-objects-container p-2.5 bg-slate-50 min-h-screen">
                <!-- Single Light Header Card -->
                <div class="bg-white rounded-xl px-3 py-1.5 border border-slate-200 shadow-2xs mb-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-boxes text-blue-600 text-sm"></i>
                        <h1 class="text-xs font-bold text-slate-800 tracking-tight mb-0">Quản lý Đối tượng & Thuộc tính</h1>
                    </div>

                    <!-- Navigation Tabs (Segmented Pill Bar) -->
                    <div class="inline-flex p-0.5 bg-slate-100 rounded-lg border border-slate-200/80">
                        <button 
                            @click="activeTab = 'types'" 
                            :class="['px-2.5 py-0.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1', activeTab === 'types' ? 'bg-white text-blue-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900']"
                        >
                            <i class="fas fa-list-ul text-[10px]"></i>
                            Loại Đối Tượng
                        </button>
                        <button 
                            @click="activeTab = 'fields'" 
                            :class="['px-2.5 py-0.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1', activeTab === 'fields' ? 'bg-white text-blue-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900']"
                        >
                            <i class="fas fa-sliders-h text-[10px]"></i>
                            Thuộc Tính Động
                        </button>
                        <button 
                            @click="activeTab = 'records'" 
                            :class="['px-2.5 py-0.5 rounded-md text-xs font-semibold transition-all flex items-center gap-1', activeTab === 'records' ? 'bg-white text-blue-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900']"
                        >
                            <i class="fas fa-database text-[10px]"></i>
                            Dữ Liệu Đối Tượng
                        </button>
                    </div>
                </div>

                <!-- TAB 1: LOẠI ĐỐI TƯỢNG -->
                <div v-if="activeTab === 'types'" class="space-y-2.5">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                        <div class="px-3 py-1.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-slate-800 text-xs mb-0">Danh mục Loại đối tượng</h4>
                                <span class="text-[11px] text-slate-500 bg-slate-200/60 px-2 py-0.2 rounded-full font-bold">{{ types.length }} loại</span>
                            </div>
                            <button v-if="hasPermission('DynamicObjectController.saveType')" @click="openTypeModal()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-lg shadow-2xs transition-all flex items-center gap-1">
                                <i class="fas fa-plus text-[9px]"></i> Thêm Loại đối tượng
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-100 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="py-2 px-2.5 w-8 text-center">#</th>
                                        <th class="py-2 px-2.5 whitespace-nowrap">Mã Loại</th>
                                        <th class="py-2 px-2.5">Tên Loại Đối Tượng & Mô Tả</th>
                                        <th class="py-2 px-2.5 text-center whitespace-nowrap">Thuộc tính</th>
                                        <th class="py-2 px-2.5 text-center whitespace-nowrap">Bản ghi</th>
                                        <th class="py-2 px-2.5 text-center whitespace-nowrap">Phân loại</th>
                                        <th class="py-2 px-2.5 text-right whitespace-nowrap">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="(type, idx) in types" :key="type.id" class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-2 px-2.5 text-center font-mono text-xs text-slate-400">{{ idx + 1 }}</td>
                                        <td class="py-2 px-2.5 font-mono text-xs font-bold text-blue-700 whitespace-nowrap">
                                            <span class="bg-blue-50/80 px-1.5 py-0.5 rounded border border-blue-100">{{ type.ma_loai }}</span>
                                        </td>
                                        <td class="py-2 px-2.5">
                                            <div class="font-bold text-slate-900 text-xs">{{ type.ten_loai }}</div>
                                            <div v-if="type.mo_ta" class="text-[11px] text-slate-500 mt-0.5 leading-snug font-normal">
                                                {{ type.mo_ta }}
                                            </div>
                                        </td>
                                        <td class="py-2 px-2.5 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ type.fields_count || 0 }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-2.5 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ type.records_count || 0 }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-2.5 text-center whitespace-nowrap">
                                            <span v-if="['giang_vien', 'sinh_vien'].includes(type.ma_loai)" class="text-xs bg-amber-50 text-amber-700 font-semibold px-2 py-0.5 rounded border border-amber-200">
                                                Mặc định
                                            </span>
                                            <span v-else class="text-xs bg-slate-50 text-slate-600 font-semibold px-2 py-0.5 rounded border border-slate-200">
                                                Mở rộng
                                            </span>
                                        </td>
                                        <td class="py-2 px-2.5 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1 justify-end">
                                                <button @click="configureFields(type)" class="w-6.5 h-6.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors" title="Cấu hình Thuộc tính">
                                                    <i class="fas fa-sliders-h text-[11px]"></i>
                                                </button>
                                                <button @click="viewRecords(type)" class="w-6.5 h-6.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-colors" title="Xem Dữ liệu đối tượng">
                                                    <i class="fas fa-database text-[11px]"></i>
                                                </button>
                                                <button v-if="hasPermission('DynamicObjectController.saveType')" @click="openTypeModal(type)" class="w-6.5 h-6.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition-colors" title="Chỉnh sửa">
                                                    <i class="fas fa-edit text-[11px]"></i>
                                                </button>
                                                <button v-if="hasPermission('DynamicObjectController.deleteType') && !['giang_vien', 'sinh_vien'].includes(type.ma_loai)" @click="deleteType(type)" class="w-6.5 h-6.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors" title="Xóa">
                                                    <i class="fas fa-trash-alt text-[11px]"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="types.length === 0">
                                        <td colspan="7" class="py-4 text-center text-slate-400 italic text-xs">
                                            Chưa có loại đối tượng nào trong hệ thống.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: THUỘC TÍNH ĐỘNG -->
                <div v-if="activeTab === 'fields'" class="space-y-2.5">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                        <div class="px-3 py-1.5 border-b border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-slate-700 whitespace-nowrap mb-0">Loại đối tượng:</label>
                                <select
                                    :value="selectedTypeId"
                                    @change="onTypeSelectChange($event.target.value)"
                                    class="text-xs font-semibold rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-slate-800 focus:ring-2 focus:ring-blue-500 outline-hidden"
                                >
                                    <option v-for="option in typeSelectOptions" :key="option.value" :value="option.value">
                                        {{ option.text }}
                                    </option>
                                </select>
                                <span class="text-xs text-slate-500 font-medium whitespace-nowrap">({{ fields.length }} thuộc tính - {{ Object.keys(groupedFields).length }} nhóm)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button v-if="hasPermission('DynamicObjectController.saveField')" @click="openFieldModal()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-lg shadow-2xs transition-all flex items-center gap-1">
                                    <i class="fas fa-plus text-[9px]"></i> Thêm Thuộc tính
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-100 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="py-2 px-2.5 w-12 text-center whitespace-nowrap">Kéo / #</th>
                                        <th class="py-2 px-2.5 whitespace-nowrap">Mã Thuộc Tính</th>
                                        <th class="py-2 px-2.5 whitespace-nowrap">Tên Thuộc Tính</th>
                                        <th class="py-2 px-2.5 whitespace-nowrap">Kiểu Dữ Liệu</th>
                                        <th class="py-2 px-2.5 whitespace-nowrap text-center">Trạng Thái</th>
                                        <th class="py-2 px-2.5 whitespace-nowrap text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template v-for="(groupFields, groupName) in groupedFields" :key="groupName">
                                        <!-- Group Section Header Row -->
                                        <tr 
                                            @dragover.prevent="onDragOverGroupHeader($event, groupName)"
                                            @drop="onDrop($event, null, groupName)"
                                            :class="['border-y border-slate-200 transition-colors', dragOverGroup === groupName && !dragOverField ? 'bg-blue-100/80 border-blue-400' : 'bg-slate-100/80']"
                                        >
                                            <td colspan="6" class="py-1.5 px-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fas fa-layer-group text-blue-600 text-xs"></i>
                                                        <span class="font-bold text-xs text-slate-800 uppercase tracking-tight">{{ groupName }}</span>
                                                        <span class="text-[11px] font-bold bg-white text-slate-700 border border-slate-200 px-2 py-0.2 rounded-full">
                                                            {{ groupFields.length }} thuộc tính
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Group Member Rows (Draggable) -->
                                        <tr 
                                            v-for="(f, idx) in groupFields" 
                                            :key="f.id" 
                                            draggable="true"
                                            @dragstart="onDragStart($event, f, groupName)"
                                            @dragover.prevent="onDragOverField($event, f, groupName)"
                                            @dragleave="onDragLeave"
                                            @dragend="onDragEnd"
                                            @drop="onDrop($event, f, groupName)"
                                            :class="[
                                                'hover:bg-slate-50/80 transition-colors cursor-move',
                                                draggedField && draggedField.id === f.id ? 'opacity-30 bg-blue-50 border-2 border-dashed border-blue-400' : '',
                                                dragOverField && dragOverField.id === f.id ? 'bg-blue-50 border-t-2 border-blue-500' : ''
                                            ]"
                                        >
                                            <td class="py-1.5 px-2.5 text-center whitespace-nowrap">
                                                <div class="inline-flex items-center justify-center gap-1.5 text-slate-400 hover:text-blue-600">
                                                    <i class="fas fa-grip-vertical text-xs cursor-grab active:cursor-grabbing" title="Kéo để di chuyển / đổi nhóm"></i>
                                                    <span class="font-mono text-xs font-bold text-slate-600">{{ f.thu_tu || (idx + 1) }}</span>
                                                </div>
                                            </td>
                                            <td class="py-1.5 px-2.5 whitespace-nowrap">
                                                <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50/80 px-2 py-0.5 rounded border border-blue-100">
                                                    {{ f.ma_truong }}
                                                </span>
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold text-slate-800 whitespace-nowrap min-w-[140px]">
                                                <span>{{ f.ten_truong }}</span>
                                                <span v-if="f.bat_buoc" class="ml-1 text-rose-500 font-bold" title="Bắt buộc nhập">*</span>
                                            </td>
                                            <td class="py-1.5 px-2.5 whitespace-nowrap">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-slate-50 text-slate-700 border border-slate-200/80 inline-flex items-center gap-1.5 whitespace-nowrap">
                                                    <i :class="getDataTypeIcon(f.kieu_du_lieu) + ' text-blue-600 text-xs'"></i>
                                                    <span>{{ getDataTypeLabel(f.kieu_du_lieu) }}</span>
                                                </span>
                                            </td>
                                            <td class="py-1.5 px-2.5 text-center whitespace-nowrap">
                                                <span :class="['px-2 py-0.5 text-xs font-bold rounded-full border inline-flex items-center gap-1 whitespace-nowrap', f.trang_thai ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200']">
                                                    <span :class="['w-1.5 h-1.5 rounded-full', f.trang_thai ? 'bg-emerald-500' : 'bg-slate-400']"></span>
                                                    {{ f.trang_thai ? 'Đang dùng' : 'Tạm ẩn' }}
                                                </span>
                                            </td>
                                            <td class="py-1.5 px-2.5 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1 justify-end">
                                                    <button v-if="hasPermission('DynamicObjectController.saveField')" @click="openFieldModal(f)" class="w-6.5 h-6.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition-colors" title="Chỉnh sửa Thuộc tính">
                                                        <i class="fas fa-edit text-[11px]"></i>
                                                    </button>
                                                    <button v-if="hasPermission('DynamicObjectController.deleteField')" @click="deleteField(f)" class="w-6.5 h-6.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors" title="Xóa Thuộc tính">
                                                        <i class="fas fa-trash-alt text-[11px]"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr v-if="fields.length === 0">
                                        <td colspan="6" class="py-4 text-center text-slate-400 italic text-xs">
                                            Chưa có thuộc tính nào được cấu hình cho loại đối tượng này.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: DỮ LIỆU ĐỐI TƯỢNG -->
                <div v-if="activeTab === 'records'" class="space-y-2.5">
                    <!-- Record Data Table with Contextual Headers -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                        <div class="px-3 py-1.5 border-b border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-slate-700 whitespace-nowrap mb-0">Loại đối tượng:</label>
                                <select
                                    :value="selectedTypeId"
                                    @change="onTypeSelectChange($event.target.value)"
                                    class="text-xs font-semibold rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-slate-800 focus:ring-2 focus:ring-blue-500 outline-hidden"
                                >
                                    <option v-for="option in typeSelectOptions" :key="option.value" :value="option.value">
                                        {{ option.text }}
                                    </option>
                                </select>
                                <span class="text-xs text-slate-500 font-medium whitespace-nowrap">({{ records.length }} bản ghi)</span>
                            </div>
                            <button v-if="hasPermission('DynamicObjectController.saveRecord')" @click="openRecordModal()" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-lg shadow-2xs transition-all flex items-center gap-1">
                                <i class="fas fa-plus text-[9px]"></i> Thêm Bản ghi
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-100 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="py-2 px-2.5 w-8 text-center">#</th>
                                        <!-- Contextual Code Header (Hidden for giang_vien) -->
                                        <th v-if="currentRecordType && currentRecordType.ma_loai !== 'giang_vien'" class="py-2 px-2.5 whitespace-nowrap">{{ getCodeHeaderLabel() }}</th>
                                        <!-- Contextual Name & Email Header -->
                                        <th class="py-2 px-2.5 whitespace-nowrap">{{ getNameHeaderLabel() }}</th>
                                        <!-- Dynamic Field Headers -->
                                        <th v-for="f in recordFields" :key="f.id" class="py-2 px-2.5 text-blue-700 whitespace-nowrap">
                                            {{ f.ten_truong }}
                                        </th>
                                        <th class="py-2 px-2.5 text-right whitespace-nowrap">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="(rec, idx) in records" :key="rec.id" class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-1.5 px-2.5 text-center font-mono text-xs text-slate-400">{{ idx + 1 }}</td>
                                        <td v-if="currentRecordType && currentRecordType.ma_loai !== 'giang_vien'" class="py-1.5 px-2.5 font-mono text-xs font-bold text-blue-700 whitespace-nowrap">
                                            <span class="bg-blue-50/80 px-1.5 py-0.5 rounded border border-blue-100">{{ rec.ma_doi_tuong }}</span>
                                        </td>
                                        <td class="py-1.5 px-2.5 min-w-[160px]">
                                            <div class="font-bold text-slate-900 text-xs">{{ rec.ten_hien_thi }}</div>
                                            <div v-if="rec.email" class="text-[11px] text-slate-500 font-mono mt-0.5 leading-none">
                                                {{ rec.email }}
                                            </div>
                                        </td>
                                        <!-- Dynamic Field Values -->
                                        <td v-for="f in recordFields" :key="f.id" class="py-1.5 px-2.5 text-slate-700 font-medium whitespace-nowrap">
                                            <!-- Image preview with lightbox trigger -->
                                            <template v-if="f.kieu_du_lieu === 'image' && rec.attributes[f.ma_truong]">
                                                <div @click="previewImage(rec.attributes[f.ma_truong])" class="cursor-pointer group relative inline-block">
                                                    <img :src="rec.attributes[f.ma_truong]" class="w-7 h-7 object-cover rounded-lg border border-slate-200 shadow-2xs group-hover:opacity-80 transition-opacity" />
                                                    <div class="absolute inset-0 flex items-center justify-center bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity text-white text-[9px]">
                                                        <i class="fas fa-search-plus"></i>
                                                    </div>
                                                </div>
                                            </template>
                                            <!-- File download badge -->
                                            <template v-else-if="f.kieu_du_lieu === 'file' && rec.attributes[f.ma_truong]">
                                                <a :href="rec.attributes[f.ma_truong]" target="_blank" download class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded text-xs font-bold transition-colors border border-blue-100">
                                                    <i :class="getFileIconClass(rec.attributes[f.ma_truong])"></i>
                                                    <span>Tải tệp</span>
                                                </a>
                                            </template>
                                            <template v-else-if="f.kieu_du_lieu === 'boolean'">
                                                <span :class="['px-2 py-0.5 text-xs font-bold rounded-md', rec.attributes[f.ma_truong] ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200']">
                                                    {{ rec.attributes[f.ma_truong] ? 'Có / Đạt' : 'Không' }}
                                                </span>
                                            </template>
                                            <template v-else-if="f.kieu_du_lieu === 'color' && rec.attributes[f.ma_truong]">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-300 shadow-2xs" :style="{ backgroundColor: rec.attributes[f.ma_truong] }"></span>
                                                    <span class="font-mono text-xs">{{ rec.attributes[f.ma_truong] }}</span>
                                                </div>
                                            </template>
                                            <template v-else>
                                                {{ rec.attributes[f.ma_truong] || '-' }}
                                            </template>
                                        </td>
                                        <td class="py-1.5 px-2.5 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1 justify-end">
                                                <button v-if="hasPermission('DynamicObjectController.saveRecord')" @click="openRecordModal(rec)" class="w-6.5 h-6.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition-colors" title="Chỉnh sửa Bản ghi">
                                                    <i class="fas fa-edit text-[11px]"></i>
                                                </button>
                                                <button v-if="hasPermission('DynamicObjectController.deleteRecord')" @click="deleteRecord(rec)" class="w-6.5 h-6.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors" title="Xóa Bản ghi">
                                                    <i class="fas fa-trash-alt text-[11px]"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="records.length === 0">
                                        <td :colspan="3 + recordFields.length" class="py-4 text-center text-slate-400 italic text-xs">
                                            Chưa có dữ liệu bản ghi cho loại đối tượng này.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <Teleport to="body">
                <!-- MODAL: TYPE FORM -->
                <div v-if="showTypeModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100 animate-fadeIn">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900 text-lg mb-0">
                                {{ typeForm.id ? 'Chỉnh sửa Loại đối tượng' : 'Thêm Loại đối tượng mới' }}
                            </h3>
                            <button @click="showTypeModal = false" class="text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mã loại đối tượng *</label>
                                <input v-model="typeForm.ma_loai" :disabled="typeForm.id && ['giang_vien', 'sinh_vien'].includes(typeForm.ma_loai)" type="text" placeholder="Ví dụ: phong_hoc, thiet_bi..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:bg-slate-100" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tên loại đối tượng *</label>
                                <input v-model="typeForm.ten_loai" type="text" placeholder="Ví dụ: Phòng học & Giảng đường" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mô tả</label>
                                <textarea v-model="typeForm.mo_ta" rows="3" placeholder="Mô tả chức năng đối tượng..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6 pt-3 border-t border-slate-100">
                            <button @click="showTypeModal = false" class="px-4 py-2 text-slate-600 font-medium text-sm rounded-lg hover:bg-slate-100">
                                Hủy
                            </button>
                            <button @click="saveType" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow">
                                Lưu thông tin
                            </button>
                        </div>
                    </div>
                </div>

                <!-- MODAL: FIELD FORM WITH PHÂN NHÓM & DYNAMIC REFERENCE -->
                <div v-if="showFieldModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3">
                    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-4 border border-slate-100 animate-fadeIn max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-3 pb-2 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900 text-sm mb-0">
                                {{ fieldForm.id ? 'Chỉnh sửa Thuộc tính' : 'Thêm Thuộc tính động mới' }}
                            </h3>
                            <button @click="showFieldModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Mã Thuộc Tính *</label>
                                <input v-model="fieldForm.ma_truong" type="text" placeholder="Ví dụ: hoc_vi, chuc_danh, file_cv..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tên Thuộc Tính *</label>
                                <input v-model="fieldForm.ten_truong" type="text" placeholder="Ví dụ: Học vị / Tệp CV đính kèm" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                            </div>
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Phân Nhóm</label>
                                    <input v-model="fieldForm.phan_nhom" type="text" placeholder="Ví dụ: Thông tin Lý lịch & Cá nhân..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Thứ Tự Sắp Xếp</label>
                                    <input v-model.number="fieldForm.thu_tu" type="number" min="1" placeholder="Ví dụ: 1, 2, 3..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kiểu Dữ Liệu</label>
                                <select v-model="fieldForm.kieu_du_lieu" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">-- Chọn kiểu dữ liệu --</option>
                                    <option v-for="opt in dataTypeOptions" :key="opt.value" :value="opt.value">
                                        {{ opt.text }}
                                    </option>
                                </select>
                            </div>

                            <!-- Dynamic Reference Settings (Select / MultiSelect) -->
                            <div v-if="['select', 'multiselect'].includes(fieldForm.kieu_du_lieu)" class="p-3 bg-blue-50/50 rounded-lg border border-blue-200/60 space-y-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-blue-900 uppercase mb-1">Nguồn danh sách chọn</label>
                                    <select v-model="fieldForm.lien_ket_loai_doi_tuong_id" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option :value="null">📌 Tự định nghĩa danh sách tùy chọn bên dưới</option>
                                        <option v-for="t in types" :key="t.id" :value="t.id">
                                            🔗 Lấy tự động từ Danh mục: {{ t.ten_loai }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Dynamic Linked Binding Rule (Save Ma vs Save Ten) -->
                                <div v-if="fieldForm.lien_ket_loai_doi_tuong_id">
                                    <label class="block text-[11px] font-bold text-blue-900 uppercase mb-1">Giá trị lưu vào hệ thống</label>
                                    <select v-model="fieldForm.cau_hinh.tieu_chuan_gia_tri" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="ma">Mã đối tượng / Email (Khuyên dùng - Ràng buộc duy nhất)</option>
                                        <option value="ten">Tên hiển thị (Tên đầy đủ của đối tượng)</option>
                                    </select>
                                </div>

                                <!-- Manual Option List Chips -->
                                <div v-if="!fieldForm.lien_ket_loai_doi_tuong_id" class="space-y-2">
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Danh sách tùy chọn</label>
                                    
                                    <div class="flex items-center gap-1.5">
                                        <input 
                                            v-model="newOptionText" 
                                            @keydown.enter.prevent="addOptionChip"
                                            type="text" 
                                            placeholder="Nhập tên tùy chọn rồi nhấn Enter hoặc bấm Thêm..." 
                                            class="flex-1 bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                                        />
                                        <button 
                                            type="button"
                                            @click="addOptionChip" 
                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-2xs transition-colors flex items-center gap-1"
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
                            <div v-if="['file', 'image'].includes(fieldForm.kieu_du_lieu)" class="p-3 bg-amber-50/50 rounded-lg border border-amber-200/60 space-y-2.5">
                                <h4 class="font-bold text-[11px] text-amber-900 uppercase mb-1 flex items-center gap-1">
                                    <i class="fas fa-sliders-h text-amber-600"></i> Cấu hình tệp & Dung lượng
                                </h4>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Dung lượng tối đa (MB)</label>
                                        <input v-model.number="fieldForm.cau_hinh.dung_luong_toi_da" type="number" min="1" max="100" placeholder="Mặc định: 10 MB" class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Đuôi tệp cho phép</label>
                                        <input v-model="fieldForm.cau_hinh.dinh_dang_tep" type="text" placeholder="Ví dụ: pdf, docx, png..." class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Ràng buộc tên tệp (Pattern / Regex)</label>
                                    <input v-model="fieldForm.cau_hinh.mau_ten_tep" type="text" placeholder="Ví dụ: ^[A-Za-z0-9_-]+$ (Không dấu, không khoảng trắng)..." class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                </div>
                            </div>

                            <!-- Common Values & Validation Settings -->
                            <div class="p-3 bg-slate-100/70 rounded-lg border border-slate-200/80 space-y-2.5">
                                <h4 class="font-bold text-[11px] text-slate-800 uppercase mb-1 flex items-center gap-1">
                                    <i class="fas fa-check-circle text-slate-500"></i> Ràng buộc & Giá trị mặc định
                                </h4>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Giá trị mặc định ban đầu</label>
                                    <input v-model="fieldForm.cau_hinh.gia_tri_mac_dinh" type="text" placeholder="Ví dụ: Chưa xác định / Nam..." class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                </div>
                                <div class="flex items-center justify-between pt-1">
                                    <div class="flex items-center gap-2">
                                        <input v-model="fieldForm.bat_buoc" type="checkbox" id="field_required" class="w-3.5 h-3.5 text-rose-600 rounded cursor-pointer" />
                                        <label for="field_required" class="text-xs font-bold text-rose-700 mb-0 cursor-pointer">Ràng buộc bắt buộc nhập (Required)</label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input v-model="fieldForm.trang_thai" type="checkbox" id="field_status" class="w-3.5 h-3.5 text-blue-600 rounded cursor-pointer" />
                                        <label for="field_status" class="text-xs font-semibold text-slate-700 mb-0 cursor-pointer">Kích hoạt thuộc tính</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4 pt-2.5 border-t border-slate-100">
                            <button @click="showFieldModal = false" class="px-3 py-1.5 text-slate-600 font-medium text-xs rounded-lg hover:bg-slate-100 transition-colors">
                                Hủy
                            </button>
                            <button @click="saveField" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs rounded-lg shadow-2xs transition-all">
                                Lưu thuộc tính
                            </button>
                        </div>
                    </div>
                </div>

                <!-- MODAL: RECORD FORM WITH GROUPED ATTRIBUTES -->
                <div v-if="showRecordModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 border border-slate-100 animate-fadeIn max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900 text-lg mb-0">
                                {{ recordForm.id ? 'Chỉnh sửa Bản ghi' : 'Thêm Bản ghi mới' }} - {{ currentRecordType ? currentRecordType.ten_loai : '' }}
                            </h3>
                            <button @click="showRecordModal = false" class="text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-5">
                            <!-- Basic Fixed Information Group -->
                            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-4">
                                <h4 class="font-bold text-xs uppercase text-blue-800 mb-2 flex items-center gap-1.5 border-b border-blue-200/60 pb-2">
                                    <i class="fas fa-id-card text-blue-600"></i> Thông tin định danh cơ bản
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-if="currentRecordType && currentRecordType.ma_loai !== 'giang_vien'">
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">{{ getCodeHeaderLabel() }} *</label>
                                        <input v-model="recordForm.ma_doi_tuong" type="text" :placeholder="'Nhập ' + getCodeHeaderLabel() + '...'" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">{{ getNameHeaderLabel() }} *</label>
                                        <input v-model="recordForm.ten_hien_thi" type="text" :placeholder="'Nhập ' + getNameHeaderLabel() + '...'" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                    </div>

                                    <div v-if="currentRecordType && ['giang_vien', 'sinh_vien'].includes(currentRecordType.ma_loai)" class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">{{ getEmailHeaderLabel() }} *</label>
                                        <input v-model="recordForm.email" type="email" placeholder="nguyenvana@vlute.edu.vn..." class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Grouped Attributes -->
                            <div 
                                v-for="(groupFields, groupName) in groupedRecordFields" 
                                :key="groupName" 
                                class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-4"
                            >
                                <h4 class="font-bold text-xs uppercase text-slate-800 mb-2 flex items-center gap-1.5 border-b border-slate-100 pb-2">
                                    <i class="fas fa-layer-group text-blue-600"></i> {{ groupName }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-for="f in groupFields" :key="f.id" :class="['textarea', 'file', 'image'].includes(f.kieu_du_lieu) ? 'md:col-span-2' : ''">
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">{{ f.ten_truong }}</label>
                                        
                                        <!-- Input for Textarea -->
                                        <textarea 
                                            v-if="f.kieu_du_lieu === 'textarea'"
                                            v-model="recordForm.attributes[f.ma_truong]"
                                            rows="3"
                                            :placeholder="'Nhập ' + f.ten_truong.toLowerCase() + '...'"
                                            class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        ></textarea>

                                        <!-- Select Dropdown (Static or Dynamic Ref) -->
                                        <select
                                            v-else-if="['select', 'multiselect'].includes(f.kieu_du_lieu)"
                                            v-model="recordForm.attributes[f.ma_truong]"
                                            class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        >
                                            <option value="">-- Chọn {{ f.ten_truong }} --</option>
                                            <option 
                                                v-for="opt in getOptionsForField(f)" 
                                                :key="opt.value" 
                                                :value="opt.value"
                                            >
                                                {{ opt.text }}
                                            </option>
                                        </select>

                                        <!-- Input for File / Image with Preview Card -->
                                        <div v-else-if="['file', 'image'].includes(f.kieu_du_lieu)" class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                                            <!-- Display current attachment preview if exists -->
                                            <div v-if="recordForm.attributes[f.ma_truong] && typeof recordForm.attributes[f.ma_truong] === 'string'" class="flex items-center justify-between p-2 bg-white rounded-lg border border-slate-200">
                                                <div class="flex items-center gap-2 overflow-hidden">
                                                    <img v-if="f.kieu_du_lieu === 'image'" :src="recordForm.attributes[f.ma_truong]" class="w-10 h-10 object-cover rounded-lg border" />
                                                    <i v-else :class="getFileIconClass(recordForm.attributes[f.ma_truong]) + ' text-xl text-blue-600'"></i>
                                                    <span class="text-xs text-slate-700 font-mono truncate max-w-[240px]">
                                                        {{ getFileNameFromUrl(recordForm.attributes[f.ma_truong]) }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a :href="recordForm.attributes[f.ma_truong]" target="_blank" download class="text-blue-600 hover:text-blue-800 text-xs font-bold p-1">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" @click="clearFileAttribute(f.ma_truong)" class="text-rose-500 hover:text-rose-700 text-xs p-1">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <input 
                                                type="file" 
                                                @change="onFileSelected($event, f.ma_truong)"
                                                class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" 
                                            />
                                        </div>

                                        <!-- Input for Boolean -->
                                        <div v-else-if="f.kieu_du_lieu === 'boolean'" class="flex items-center gap-2 pt-1">
                                            <input 
                                                type="checkbox" 
                                                v-model="recordForm.attributes[f.ma_truong]"
                                                class="w-4 h-4 text-blue-600 rounded"
                                            />
                                            <span class="text-xs font-semibold text-slate-700 mb-0">Kích hoạt / Đạt</span>
                                        </div>

                                        <!-- Input for Color -->
                                        <input 
                                            v-else-if="f.kieu_du_lieu === 'color'"
                                            v-model="recordForm.attributes[f.ma_truong]"
                                            type="color"
                                            class="w-16 h-9 p-1 bg-slate-50 border border-slate-300 rounded-xl cursor-pointer"
                                        />

                                        <!-- Default Input (text, number, date, datetime, email, url) -->
                                        <input 
                                            v-else
                                            v-model="recordForm.attributes[f.ma_truong]" 
                                            :type="f.kieu_du_lieu === 'number' ? 'number' : (f.kieu_du_lieu === 'date' ? 'date' : (f.kieu_du_lieu === 'datetime' ? 'datetime-local' : (f.kieu_du_lieu === 'email' ? 'email' : 'text')))" 
                                            :placeholder="'Nhập ' + f.ten_truong.toLowerCase() + '...'" 
                                            class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6 pt-3 border-t border-slate-100">
                            <button @click="showRecordModal = false" class="px-4 py-2 text-slate-600 font-medium text-sm rounded-lg hover:bg-slate-100">
                                Hủy
                            </button>
                            <button @click="saveRecord" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-lg shadow">
                                Lưu bản ghi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- LIGHTBOX IMAGE PREVIEW MODAL -->
                <div v-if="previewImageUrl" class="fixed inset-0 z-[9999] bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4" @click.self="previewImageUrl = null">
                    <div class="relative max-w-3xl w-full bg-white rounded-2xl p-2 shadow-2xl overflow-hidden animate-fadeIn">
                        <button @click="previewImageUrl = null" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-900/60 text-white flex items-center justify-center hover:bg-slate-900 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                        <img :src="previewImageUrl" class="w-full max-h-[80vh] object-contain rounded-xl" />
                    </div>
                </div>
            </Teleport>
            </div>
        </template>
    </LTEContentWrapper>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '@/store/auth'

export default {
    name: 'DynamicObjects',
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
        }
    },
    mounted() {
        this.loadTypes()
    },
    methods: {
        hasPermission(perm) {
            return useAuthStore().hasPermission(perm)
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
        },

        onDragOverGroupHeader(event, groupName) {
            event.preventDefault()
            this.dragOverField = null
            this.dragOverGroup = groupName
        },

        onDragLeave() {
            this.dragOverField = null
            this.dragOverGroup = null
        },

        onDragEnd() {
            this.draggedField = null
            this.dragOverField = null
            this.dragOverGroup = null
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

            // Update group assignment
            srcField.phan_nhom = newGroup

            // Remove dragged item from current position
            const srcIndex = this.fields.findIndex(f => f.id === srcField.id)
            if (srcIndex !== -1) {
                this.fields.splice(srcIndex, 1)
            }

            // Insert into target position
            if (targetField) {
                const targetIndex = this.fields.findIndex(f => f.id === targetField.id)
                if (targetIndex !== -1) {
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
                const res = await axios.post(route('DynamicObjectController.reorderFields'), { orders })
                if (res.data.status === 200 && window.toastr) {
                    window.toastr.success('Cập nhật thứ tự và đánh số lại thành công!')
                }
            } catch (err) {
                console.error(err)
            }
        },

        async loadTypes() {
            try {
                const res = await axios.get(route('DynamicObjectController.getTypes'))
                if (res.data.status === 200) {
                    this.types = res.data.data
                    if (this.types.length > 0 && !this.selectedTypeId) {
                        this.selectedTypeId = this.types[0].id
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
        openTypeModal(type = null) {
            if (type) {
                this.typeForm = { id: type.id, ma_loai: type.ma_loai, ten_loai: type.ten_loai, mo_ta: type.mo_ta }
            } else {
                this.typeForm = { id: null, ma_loai: '', ten_loai: '', mo_ta: '' }
            }
            this.showTypeModal = true
        },

        async saveType() {
            try {
                const res = await axios.post(route('DynamicObjectController.saveType'), this.typeForm)
                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.showTypeModal = false
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
        openFieldModal(field = null) {
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
            this.showFieldModal = true
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
                const res = await axios.post(route('DynamicObjectController.reorderFields'), { orders })
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
                const res = await axios.post(route('DynamicObjectController.saveField'), this.fieldForm)
                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.showFieldModal = false
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

        openRecordModal(rec = null) {
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
            this.showRecordModal = true
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
                    if (this.recordForm.fileObjects[key]) {
                        formData.append(`attributes[${key}]`, this.recordForm.fileObjects[key])
                    } else {
                        formData.append(`attributes[${key}]`, this.recordForm.attributes[key] || '')
                    }
                }

                const res = await axios.post(route('DynamicObjectController.saveRecord'), formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })

                if (res.data.status === 200) {
                    if (window.func && window.func.toastSuccess) {
                        window.func.toastSuccess(res.data.message)
                    }
                    this.showRecordModal = false
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
