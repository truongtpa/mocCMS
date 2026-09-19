<template>
    <div class="app-table-wrap">
        <!-- Thanh công cụ: để trang tự đặt ô tìm kiếm, nút thêm mới ... -->
        <div v-if="$slots.toolbar" class="app-table-toolbar">
            <slot name="toolbar" :selected="selectedItems" />
        </div>

        <!-- Thanh hiện khi đang chọn nhiều dòng -->
        <div v-if="selectable && selectedKeys.length" class="app-table-selectionbar">
            <span>
                Đã chọn <strong>{{ selectedKeys.length }}</strong> dòng
            </span>
            <slot name="selection-actions" :selected="selectedItems" />
            <button type="button" class="btn btn-sm btn-link text-decoration-none ms-auto" @click="clearSelection">
                Bỏ chọn
            </button>
        </div>

        <div class="table-responsive app-table-scroll" :class="{ 'app-table-sticky': stickyHeader }">
            <table class="table app-table mb-0" :class="tableClass" :style="{ minWidth: minWidth || null }">
                <caption v-if="caption" class="visually-hidden">{{ caption }}</caption>

                <thead>
                    <tr>
                        <th v-if="selectable" scope="col" class="app-table-check-col">
                            <input
                                v-if="selectMode === 'multiple'"
                                class="form-check-input"
                                type="checkbox"
                                :checked="isAllSelected"
                                :indeterminate.prop="isSomeSelected"
                                :aria-label="isAllSelected ? 'Bỏ chọn tất cả' : 'Chọn tất cả'"
                                @change="toggleSelectAll"
                            >
                        </th>

                        <th
                            v-for="col in columns"
                            :key="col.key"
                            scope="col"
                            :class="[colClass(col), col.headerClass, { 'app-table-sortable': isSortable(col) }]"
                            :aria-sort="ariaSort(col)"
                        >
                            <component
                                :is="isSortable(col) ? 'button' : 'span'"
                                v-bind="isSortable(col) ? { type: 'button', class: 'app-table-sortbtn' } : {}"
                                @click="isSortable(col) && requestSort(col.key)"
                            >
                                <slot :name="`header-${col.key}`" :column="col">{{ col.label }}</slot>
                                <i v-if="isSortable(col)" :class="sortIcon(col.key)" aria-hidden="true"></i>
                            </component>
                        </th>

                        <th
                            v-if="$slots.actions"
                            scope="col"
                            class="app-table-actions-col"
                        >
                            {{ actionsLabel }}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <template v-if="rows.length">
                        <template v-for="(item, index) in rows" :key="rowKey(item, index)">
                            <!-- Dòng tiêu đề nhóm -->
                            <tr v-if="groupBy && isNewGroup(item, index)" class="app-table-group">
                                <td :colspan="totalColumns">
                                    <slot name="group-header" :group-value="item[groupBy]" :item="item">
                                        {{ item[groupBy] }}
                                    </slot>
                                </td>
                            </tr>

                            <tr
                                :class="[
                                    rowClass ? rowClass(item, index) : null,
                                    {
                                        'app-table-row-selected': isSelected(item),
                                        'app-table-row-clickable': selectable && rowClickable && !item.disabled,
                                        'app-table-row-disabled': item.disabled,
                                    },
                                ]"
                                @mousedown="onRowMouseDown"
                                @mouseup="onRowMouseUp(item, $event)"
                            >
                                <td v-if="selectable" class="app-table-check-col" @click.stop @mousedown.stop @mouseup.stop>
                                    <input
                                        class="form-check-input"
                                        :type="selectMode === 'single' ? 'radio' : 'checkbox'"
                                        :name="selectMode === 'single' ? radioName : undefined"
                                        :disabled="item.disabled"
                                        :checked="isSelected(item)"
                                        :aria-label="`Chọn dòng ${index + 1}`"
                                        @change="toggleSelect(item)"
                                    >
                                </td>

                                <td
                                    v-for="col in columns"
                                    :key="col.key"
                                    :class="[colClass(col), col.class]"
                                >
                                    <slot
                                        :name="`cell-${col.key}`"
                                        :item="item"
                                        :value="rawValue(item, col)"
                                        :index="index"
                                        :highlight="highlightParts"
                                    >
                                        <template v-if="highlight && !col.formatter">
                                            <!-- Highlight an toàn: không dùng v-html -->
                                            <template v-for="(part, pi) in highlightParts(cellText(item, col))" :key="pi">
                                                <mark v-if="part.hit" class="app-table-mark">{{ part.text }}</mark>
                                                <template v-else>{{ part.text }}</template>
                                            </template>
                                        </template>
                                        <template v-else>{{ cellText(item, col) }}</template>
                                    </slot>
                                </td>

                                <td v-if="$slots.actions" class="app-table-actions-col" @click.stop @mousedown.stop @mouseup.stop>
                                    <div class="app-table-actions">
                                        <slot name="actions" :item="item" :index="index" />
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    <!-- Rỗng -->
                    <tr v-else class="app-table-empty">
                        <td :colspan="totalColumns">
                            <slot name="empty">
                                <i class="bi bi-inbox app-table-empty-icon" aria-hidden="true"></i>
                                <div>{{ emptyText }}</div>
                            </slot>
                        </td>
                    </tr>
                </tbody>

                <tfoot v-if="$slots.footer">
                    <slot name="footer" :items="rows" />
                </tfoot>
            </table>
        </div>

        <!-- Phân trang -->
        <div v-if="showPagination && meta.total > 0" class="app-table-footer">
            <div class="app-table-count">
                Hiển thị <strong>{{ meta.from }}</strong>–<strong>{{ meta.to }}</strong>
                trên tổng <strong>{{ meta.total }}</strong> dòng
            </div>

            <nav v-if="meta.last_page > 1" aria-label="Phân trang">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: meta.current_page <= 1 }">
                        <button type="button" class="page-link" :disabled="meta.current_page <= 1" @click="goPage(meta.current_page - 1)">
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                            <span class="visually-hidden">Trang trước</span>
                        </button>
                    </li>

                    <li v-for="(p, i) in pageWindow" :key="`p-${i}`" class="page-item" :class="{ active: p === meta.current_page, disabled: p === '…' }">
                        <span v-if="p === '…'" class="page-link">…</span>
                        <button
                            v-else
                            type="button"
                            class="page-link"
                            :aria-current="p === meta.current_page ? 'page' : undefined"
                            @click="goPage(p)"
                        >{{ p }}</button>
                    </li>

                    <li class="page-item" :class="{ disabled: meta.current_page >= meta.last_page }">
                        <button type="button" class="page-link" :disabled="meta.current_page >= meta.last_page" @click="goPage(meta.current_page + 1)">
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                            <span class="visually-hidden">Trang sau</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, useSlots, watch } from 'vue'

/**
 * AppTable — bảng dữ liệu dùng chung.
 *
 * Kế thừa ý tưởng của TablePro/AppTable cũ nhưng:
 *  - Ưu tiên server-side: sort/phân trang phát sự kiện ra ngoài thay vì tự xử lý
 *    trên đúng 1 trang dữ liệu (lỗi âm thầm của bản cũ).
 *  - Style scoped + dùng biến màu Bootstrap nên chạy đúng ở cả dark mode.
 *  - Không dùng v-html cho dữ liệu, highlight bằng <mark> an toàn.
 */
const props = defineProps({
    /** Dữ liệu của trang hiện tại */
    items: { type: Array, default: () => [] },

    /**
     * Cấu hình cột — chỉ dữ liệu và hành vi:
     *   { key, label, sortable, formatter(value, item), class, headerClass }
     *
     * Phần trình bày (bề rộng, căn lề, xuống dòng...) làm bằng CSS.
     * Mỗi ô được gắn sẵn class `app-col-<key>` ở cả <th> lẫn <td>:
     *
     *   .card :deep(.app-col-tieu_de) { width: 26%; }
     *   .card :deep(.app-col-ngay_tao) { text-align: right; white-space: nowrap; }
     */
    columns: { type: Array, default: () => [] },

    /** Object paginator của Laravel ({ current_page, last_page, per_page, total, from, to }) */
    pagination: { type: Object, default: () => ({}) },

    /** Trường làm khoá định danh dòng */
    itemKey: { type: String, default: 'id' },

    emptyText: { type: String, default: 'Không có dữ liệu.' },
    caption: { type: String, default: '' },

    /* ----- Giao diện ----- */
    hover: { type: Boolean, default: true },
    striped: { type: Boolean, default: false },
    /* Mặc định không kẻ dọc cho khớp theme global; cần kẻ thì truyền bordered */
    bordered: { type: Boolean, default: false },
    small: { type: Boolean, default: false },
    stickyHeader: { type: Boolean, default: false },
    /** Bề rộng tối thiểu của bảng; hẹp hơn thì cuộn ngang thay vì bóp nát cột */
    minWidth: { type: String, default: '860px' },
    rowClass: { type: Function, default: null },

    actionsLabel: { type: String, default: 'Thao tác' },

    showPagination: { type: Boolean, default: true },

    /** Chuỗi cần làm nổi bật trong ô (an toàn, không dùng v-html) */
    highlight: { type: String, default: '' },

    /* ----- Sắp xếp ----- */
    /** 'server': phát sự kiện ra ngoài | 'local': tự sort trong trang hiện tại */
    sortMode: { type: String, default: 'server', validator: (v) => ['server', 'local'].includes(v) },
    sortKey: { type: String, default: '' },
    sortOrder: { type: String, default: 'asc', validator: (v) => ['asc', 'desc'].includes(v) },

    /* ----- Chọn dòng ----- */
    selectable: { type: Boolean, default: false },
    selectMode: { type: String, default: 'multiple', validator: (v) => ['single', 'multiple'].includes(v) },
    selectKey: { type: String, default: '' },
    rowClickable: { type: Boolean, default: true },
    returnType: { type: String, default: 'object', validator: (v) => ['object', 'key'].includes(v) },
    /** Giữ lựa chọn khi chuyển trang */
    preserveSelection: { type: Boolean, default: false },

    /** Gom nhóm theo trường */
    groupBy: { type: String, default: '' },
})

const emit = defineEmits([
    'update:page',
    'update:sortKey',
    'update:sortOrder',
    'page-change',
    'sort-change',
    'row-click',
    'selection-change',
])

const radioName = `app-table-radio-${Math.random().toString(36).slice(2, 9)}`

/* ------------------------------------------------------------------ */
/* Khoá dòng                                                           */
/* ------------------------------------------------------------------ */
const keyField = computed(() => props.selectKey || props.itemKey)

function keyOf(item) {
    return item?.[keyField.value]
}

function rowKey(item, index) {
    const k = keyOf(item)
    return k === undefined || k === null ? `idx-${index}` : k
}

/** Dòng mở đầu một nhóm — items phải được sắp theo groupBy sẵn từ server */
function isNewGroup(item, index) {
    return index === 0 || rows.value[index - 1]?.[props.groupBy] !== item?.[props.groupBy]
}

/* ------------------------------------------------------------------ */
/* Dữ liệu hiển thị                                                    */
/* ------------------------------------------------------------------ */
const localSortKey = ref(props.sortKey)
const localSortOrder = ref(props.sortOrder)

watch(() => props.sortKey, (v) => { localSortKey.value = v })
watch(() => props.sortOrder, (v) => { localSortOrder.value = v })

const rows = computed(() => {
    if (props.sortMode !== 'local' || !localSortKey.value) {
        return props.items
    }

    const dir = localSortOrder.value === 'asc' ? 1 : -1

    return [...props.items].sort((a, b) => {
        const A = a?.[localSortKey.value]
        const B = b?.[localSortKey.value]

        if (A === B) return 0
        if (A === null || A === undefined) return 1
        if (B === null || B === undefined) return -1

        if (typeof A === 'number' && typeof B === 'number') return (A - B) * dir

        return String(A).localeCompare(String(B), 'vi', { numeric: true }) * dir
    })
})

const meta = computed(() => {
    const p = props.pagination || {}
    const total = Number(p.total ?? props.items.length ?? 0)
    const perPage = Number(p.per_page ?? props.items.length ?? 0)
    const current = Number(p.current_page ?? 1)

    return {
        current_page: current,
        last_page: Number(p.last_page ?? 1),
        per_page: perPage,
        total,
        from: Number(p.from ?? (total ? (current - 1) * perPage + 1 : 0)),
        to: Number(p.to ?? Math.min(current * perPage, total)),
    }
})

const totalColumns = computed(() => {
    let n = props.columns.length
    if (props.selectable) n++
    if (slotsHasActions.value) n++
    return n || 1
})

const slots = useSlots()
const slotsHasActions = computed(() => !!slots.actions)

const tableClass = computed(() => ({
    'table-hover': props.hover,
    'table-striped': props.striped,
    'table-bordered': props.bordered,
    'table-sm': props.small,
}))

/* ------------------------------------------------------------------ */
/* Nội dung ô                                                          */
/* ------------------------------------------------------------------ */
function rawValue(item, col) {
    // Hỗ trợ key lồng: 'tac_gia.ho_ten'
    return col.key.split('.').reduce((acc, k) => (acc == null ? acc : acc[k]), item)
}

function cellText(item, col) {
    const v = rawValue(item, col)
    if (typeof col.formatter === 'function') return col.formatter(v, item)
    return v === null || v === undefined ? '' : v
}

/**
 * Bỏ dấu 1 ký tự nhưng GIỮ NGUYÊN độ dài chuỗi, để vị trí khớp vẫn ánh xạ
 * đúng sang chuỗi gốc. Cần thiết vì collation MySQL so khớp không phân biệt
 * dấu ("khai giang" tìm ra "Khai giảng"), nếu client so khớp có dấu thì
 * tìm ra kết quả mà không tô được chữ nào.
 */
function boDau(chuoi) {
    let out = ''
    for (const ch of String(chuoi)) {
        const tach = ch.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        let c = tach === '' ? ch : tach[0]
        if (c === 'đ') c = 'd'
        else if (c === 'Đ') c = 'D'
        out += c.toLowerCase()
    }
    return out
}

function highlightParts(text) {
    const s = String(text ?? '')
    const term = props.highlight.trim()
    if (!term) return [{ text: s, hit: false }]

    const parts = []
    const lower = boDau(s)
    const needle = boDau(term)
    let i = 0

    while (i < s.length) {
        const at = lower.indexOf(needle, i)
        if (at === -1) {
            parts.push({ text: s.slice(i), hit: false })
            break
        }
        if (at > i) parts.push({ text: s.slice(i, at), hit: false })
        parts.push({ text: s.slice(at, at + needle.length), hit: true })
        i = at + needle.length
    }

    return parts
}

/* ------------------------------------------------------------------ */
/* Sắp xếp                                                             */
/* ------------------------------------------------------------------ */
function isSortable(col) {
    return !!col.sortable
}

function requestSort(key) {
    const order = localSortKey.value === key && localSortOrder.value === 'asc' ? 'desc' : 'asc'

    localSortKey.value = key
    localSortOrder.value = order

    emit('update:sortKey', key)
    emit('update:sortOrder', order)
    emit('sort-change', { key, order })
}

function sortIcon(key) {
    if (localSortKey.value !== key) return 'bi bi-arrow-down-up app-table-sorticon is-idle'
    return localSortOrder.value === 'asc'
        ? 'bi bi-caret-up-fill app-table-sorticon'
        : 'bi bi-caret-down-fill app-table-sorticon'
}

function ariaSort(col) {
    if (!isSortable(col)) return undefined
    if (localSortKey.value !== col.key) return 'none'
    return localSortOrder.value === 'asc' ? 'ascending' : 'descending'
}

/** Class ổn định cho từng cột, để trang tự chỉnh bề rộng/căn lề bằng CSS */
function colClass(col) {
    return `app-col-${String(col.key).replace(/[^a-zA-Z0-9_-]+/g, '-')}`
}

/* ------------------------------------------------------------------ */
/* Phân trang                                                          */
/* ------------------------------------------------------------------ */
function goPage(page) {
    const p = Number(page)
    if (p < 1 || p > meta.value.last_page || p === meta.value.current_page) return

    emit('update:page', p)
    emit('page-change', p)
}

const pageWindow = computed(() => {
    const { current_page: cur, last_page: last } = meta.value
    const span = 2
    const out = []

    let start = Math.max(1, cur - span)
    let end = Math.min(last, cur + span)

    if (start > 1) {
        out.push(1)
        if (start > 2) out.push('…')
    }
    for (let i = start; i <= end; i++) out.push(i)
    if (end < last) {
        if (end < last - 1) out.push('…')
        out.push(last)
    }

    return out
})

/* ------------------------------------------------------------------ */
/* Chọn dòng                                                           */
/* ------------------------------------------------------------------ */
const selectedKeys = ref([])
const selectedStore = new Map()
const lastSelectedKey = ref(null)

const selectedItems = computed(() =>
    props.returnType === 'key'
        ? [...selectedKeys.value]
        : selectedKeys.value.map((k) => selectedStore.get(k)).filter(Boolean),
)

const selectableRows = computed(() => rows.value.filter((i) => !i.disabled))

const isAllSelected = computed(
    () => selectableRows.value.length > 0 && selectableRows.value.every((i) => selectedKeys.value.includes(keyOf(i))),
)

const isSomeSelected = computed(() => selectedKeys.value.length > 0 && !isAllSelected.value)

function isSelected(item) {
    return selectedKeys.value.includes(keyOf(item))
}

function commitSelection() {
    emit('selection-change', selectedItems.value)
}

function setSelected(keys) {
    selectedKeys.value = keys
    commitSelection()
}

function toggleSelect(item) {
    if (item.disabled) return
    const k = keyOf(item)
    selectedStore.set(k, item)

    if (props.selectMode === 'single') {
        setSelected(selectedKeys.value.includes(k) ? [] : [k])
    } else {
        setSelected(
            selectedKeys.value.includes(k)
                ? selectedKeys.value.filter((x) => x !== k)
                : [...selectedKeys.value, k],
        )
    }

    lastSelectedKey.value = k
}

function toggleSelectAll() {
    if (isAllSelected.value) {
        const pageKeys = selectableRows.value.map(keyOf)
        setSelected(selectedKeys.value.filter((k) => !pageKeys.includes(k)))
    } else {
        const add = []
        selectableRows.value.forEach((i) => {
            const k = keyOf(i)
            selectedStore.set(k, i)
            if (!selectedKeys.value.includes(k)) add.push(k)
        })
        setSelected([...selectedKeys.value, ...add])
    }
    lastSelectedKey.value = null
}

function clearSelection() {
    selectedStore.clear()
    lastSelectedKey.value = null
    setSelected([])
}

/** Chọn khoảng bằng Shift, chỉ trong phạm vi trang hiện tại */
function selectRangeTo(item) {
    const keys = rows.value.map(keyOf)
    const from = keys.indexOf(lastSelectedKey.value)
    const to = keys.indexOf(keyOf(item))
    if (from === -1 || to === -1) return toggleSelect(item)

    const [a, b] = from < to ? [from, to] : [to, from]
    const add = []

    for (let i = a; i <= b; i++) {
        const row = rows.value[i]
        if (row.disabled) continue
        const k = keyOf(row)
        selectedStore.set(k, row)
        if (!selectedKeys.value.includes(k)) add.push(k)
    }

    setSelected([...selectedKeys.value, ...add])
}

/* Phân biệt click thật với thao tác bôi đen văn bản */
const mouseDown = { x: 0, y: 0, t: 0 }

function onRowMouseDown(e) {
    mouseDown.x = e.clientX
    mouseDown.y = e.clientY
    mouseDown.t = Date.now()
}

function onRowMouseUp(item, e) {
    const moved = Math.abs(e.clientX - mouseDown.x) > 5 || Math.abs(e.clientY - mouseDown.y) > 5
    const held = Date.now() - mouseDown.t > 250
    if (moved || held) return

    emit('row-click', item)

    if (!props.selectable || !props.rowClickable || item.disabled) return

    if (props.selectMode === 'multiple' && e.shiftKey && lastSelectedKey.value !== null) {
        selectRangeTo(item)
        return
    }

    toggleSelect(item)
}

/** Đổi trang / đổi dữ liệu thì bỏ chọn, trừ khi preserveSelection */
watch(
    () => props.items,
    () => {
        if (!props.preserveSelection) clearSelection()
    },
)

defineExpose({ clearSelection, selectedItems, setSelected })
</script>

<style scoped>
.app-table-wrap {
    --app-table-border: var(--bs-border-color, #dee2e6);
    position: relative;
}

/* Nhóm thao tác dạt trái, ô tìm kiếm dạt phải — xem .app-toolbar-* ở theme.css */
.app-table-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.app-table-selectionbar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.5rem;
    border: 1px solid var(--bs-primary-border-subtle, #b6d4fe);
    background: var(--bs-primary-bg-subtle, #cfe2ff);
    border-radius: var(--bs-border-radius, 0.375rem);
    font-size: 0.875rem;
}

.app-table-scroll {
    position: relative;
    border: 1px solid var(--app-table-border);
    /* Token do theme global đặt, để khung ngoài khớp với bo góc của header */
    border-radius: var(--app-table-radius, var(--bs-border-radius, 0.375rem));
}

.app-table {
    /* Bảng nằm trong khung bo góc nên bỏ viền ngoài của chính table */
    border-color: var(--app-table-border);
}

.app-table :deep(th),
.app-table :deep(td) {
    padding: 0.5rem 0.625rem;
    vertical-align: middle;
}

/* Bỏ viền mép ngoài để không chồng lên border của khung bo góc */
.app-table :deep(tr > *:first-child) { border-left: 0; }
.app-table :deep(tr > *:last-child) { border-right: 0; }
.app-table :deep(thead tr:first-child > *) { border-top: 0; }
.app-table :deep(tbody tr:last-child > *) { border-bottom: 0; }

.app-table-sticky thead th {
    position: sticky;
    top: 0;
    z-index: 2;
}

/* ----- Nút sắp xếp ----- */
.app-table-sortbtn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0;
    border: 0;
    background: none;
    color: inherit;
    font: inherit;
    cursor: pointer;
}

.app-table-sortbtn:hover { text-decoration: underline; }

.app-table-sortbtn:focus-visible {
    outline: 2px solid var(--bs-focus-ring-color, rgba(13, 110, 253, 0.5));
    outline-offset: 2px;
    border-radius: 2px;
}

.app-table-sorticon { font-size: 0.7em; }
.app-table-sorticon.is-idle { opacity: 0.6; }

/* ----- Cột đặc biệt ----- */
.app-table-check-col {
    width: 42px;
    text-align: center;
}

.app-table-actions-col {
    width: 110px;
    text-align: center;
    white-space: nowrap;
}

.app-table-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    flex-wrap: nowrap;
}

/* ----- Dòng ----- */
.app-table-row-clickable { cursor: pointer; }

.app-table-row-disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.app-table-row-selected > :deep(td) {
    /* Dùng biến Bootstrap nên tự đúng màu ở dark mode */
    background-color: var(--bs-primary-bg-subtle, #cfe2ff) !important;
}

.app-table-group > :deep(td) {
    background: var(--bs-secondary-bg, #e9ecef);
    font-weight: 600;
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-left: 3px solid var(--bs-primary, #0d6efd);
}

.app-table-mark {
    padding: 0 0.1em;
    background: var(--bs-warning-bg-subtle, #fff3cd);
    color: inherit;
    border-radius: 2px;
}

/* ----- Rỗng ----- */
.app-table-empty :deep(td) {
    padding: 2.5rem 1rem;
    text-align: center;
    color: var(--bs-secondary-color, #6c757d);
}

.app-table-empty-icon {
    display: block;
    font-size: 2rem;
    opacity: 0.4;
    margin-bottom: 0.5rem;
}

/* ----- Chân bảng ----- */
.app-table-footer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: 0.75rem;
}

.app-table-count {
    font-size: 0.875rem;
    color: var(--bs-secondary-color, #6c757d);
}

@media (max-width: 575.98px) {
    .app-table-footer { justify-content: center; }
}
</style>
