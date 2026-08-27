<template>
    <div class="row">
        <div v-if="showSearch" class="col-8"></div>
        <div v-if="showSearch" class="col-4">
            <div class="input-group input-group-sm" style="margin-bottom: 12px;">
                <input type="text" class="form-control float-right" v-model="searchQuery"
                       placeholder="Tìm kiếm thông tin ...">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover border-bottom">
                    <thead>
                        <tr>
                            <th v-if="selectable" style="width: 40px; text-align: center">
                                <input
                                    v-if="selectMode === 'multiple'"
                                    type="checkbox"
                                    :checked="isAllSelected"
                                    @change="toggleSelectAll"
                                />
                            </th>

                            <th
                                v-for="header in headers"
                                :key="header.key"
                                :style="header.css"
                                @click="header.key !== 'col0' && sortTable(header.key)"
                                style="cursor: pointer"
                            >
                                <slot :name="'header.' + header.key" v-bind:header="header">
                                    {{ header.label }}
                                </slot>
                                <span v-if="sortKey === header.key">
                                    <i v-if="sortOrder === 'asc'" class="fas fa-sort-amount-up"></i>
                                    <i v-if="sortOrder === 'desc'" class="fas fa-sort-amount-down-alt"></i>
                                </span>
                            </th>

                            <th v-if="$slots.actions" class="tablepro-actions-th" :style="cssActions"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <template v-for="(item, index) in sortedData" :key="item[selectKey]">
                            <!-- Dòng tiêu đề nhóm -->
                            <tr v-if="groupBy && isNewGroup(item, index)" class="tablepro-group-header-row">
                                <td :colspan="totalColumns" class="tablepro-group-header-td">
                                    <slot name="group-header" :group-value="item[groupBy]" :item="item">
                                        <strong>{{ item[groupBy] }}</strong>
                                    </slot>
                                </td>
                            </tr>

                            <!-- Dòng dữ liệu thông thường -->
                            <tr
                                :class="{ 'row-selected': isSelected(item) }"
                                @mousedown="rowClickable && selectable && handleMouseDown($event)"
                                @mouseup="rowClickable && selectable && handleMouseUp(item, $event)"
                                style="cursor: pointer"
                            >
                                <td v-if="selectable" style="text-align: center" @click.stop @mousedown.stop>
                                    <input
                                        :type="selectMode === 'single' ? 'radio' : 'checkbox'"
                                        :name="selectMode === 'single' ? 'row-select' : null"
                                        :disabled="item.disabled"
                                        :checked="isSelected(item)"
                                        @change="toggleSelect(item)"
                                    />
                                </td>

                                <template v-for="header in headers" :key="header.key">
                                    <td
                                        v-if="!rowSpans[header.key] || rowSpans[header.key][index] !== 0"
                                        :rowspan="rowSpans[header.key] && rowSpans[header.key][index] > 1 ? rowSpans[header.key][index] : null"
                                    >
                                        <slot v-if="$slots[header.key]" :name="header.key" :item="item"></slot>
                                        <span v-else v-html="highlight(item[header.key])"></span>
                                    </td>
                                </template>

                                <td v-if="$slots.actions" class="tablepro-actions-td" @click.stop @mousedown.stop @mouseup.stop>
                                    <div class="tablepro-actions-wrapper">
                                        <slot name="actions" :item="item"></slot>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="no-data" v-if="EmptyLabel && data.length == 0">
                    {{ EmptyLabel }}
                </div>
            </div>
        </div>

        <template v-if="showPagination">
            <hr />
            <div class="col-6 mt-1">
                Có tổng cộng <b>{{ pagination.total }}</b> dòng dữ liệu.
            </div>

            <div class="col-6">
                <ul v-if="pagination.last_page > 1" class="pagination pagination-sm m-0 float-right">
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">Trước</a>
                    </li>

                    <li v-if="pagination.current_page > 3" class="page-item">
                        <a class="page-link" href="#" @click.prevent="changePage(1)">1</a>
                    </li>
                    <li v-if="pagination.current_page > 4" class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>

                    <li
                        v-for="page in visiblePages"
                        :key="page"
                        class="page-item"
                        :class="{ active: pagination.current_page === page }"
                    >
                        <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                    </li>

                    <li v-if="pagination.current_page < pagination.last_page - 3" class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li v-if="pagination.current_page < pagination.last_page - 2" class="page-item">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.last_page)">
                            {{ pagination.last_page }}
                        </a>
                    </li>

                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">Sau</a>
                    </li>
                </ul>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        data: Array,
        headers: Array,
        pagination: Object,
        fetchPage: Function,
        cssActions: { type: String, default: 'width: 100px' },
        EmptyLabel: { type: String, default: '' },
        showActions: { type: Boolean, default: true },
        showPagination: { type: Boolean, default: true },
        showSearch: { type: Boolean, default: false },

        /** bật chế độ chọn */
        selectable: { type: Boolean, default: false },

        /** chế độ: single (radio) hoặc multiple (checkbox) */
        selectMode: { type: String, default: 'multiple' },

        /** click vào dòng để chọn */
        rowClickable: { type: Boolean, default: true },

        /** dùng trường nào làm key */
        selectKey: { type: String, default: 'id' },

        /** trả về object đầy đủ hay key */
        returnType: { type: String, default: 'object' }, // 'object' | 'key'

        /** danh sách các cột cần gộp ô */
        mergeColumns: { type: Array, default: () => [] },

        /** trường dùng để nhóm dữ liệu thành các dòng tiêu đề phân cách */
        groupBy: { type: String, default: '' },
    },

    data() {
        return {
            searchQuery: '',
            sortKey: '',
            sortOrder: 'asc',
            selectedMap: new Map(), // key → item
            lastSelectedKey: null,
            mouseDownCoords: { x: 0, y: 0, time: 0 },
        }
    },

    watch: {
        /** Clear selection khi đổi trang */
        'pagination.current_page'() {
            // this.selectedMap.clear()
            // this.emitSelection(true)
            // this.lastSelectedKey = null
            this.resetSelection()
        },
        data() {
            this.resetSelection()
        },
    },

    computed: {
        sortedData() {
            let sorted = [...this.data]

            if (this.showSearch && this.searchQuery) {
                sorted = sorted.filter(item => {
                    return this.headers.some(header => {
                        const value = item[header.key];
                        return value && value.toString().toLowerCase().includes(this.searchQuery.toLowerCase());
                    });
                });
            }

            if (this.sortKey) {
                sorted.sort((a, b) => {
                    let A = a[this.sortKey] || ''
                    let B = b[this.sortKey] || ''
                    if (A < B) return this.sortOrder === 'asc' ? -1 : 1
                    if (A > B) return this.sortOrder === 'asc' ? 1 : -1
                    return 0
                })
            }
            return sorted
        },

        visiblePages() {
            const { current_page, last_page } = this.pagination
            let start = Math.max(current_page - 2, 1)
            let end = Math.min(current_page + 2, last_page)
            return Array.from({ length: end - start + 1 }, (_, i) => start + i)
        },

        // isAllSelected() {
        //     return this.data.length > 0 && this.selectedMap.size === this.data.length
        // },
        isAllSelected() {
            const selectableItems = this.data.filter((item) => !item.disabled)
            return selectableItems.length > 0 && this.selectedMap.size === selectableItems.length
        },

        rowSpans() {
            const spans = {}
            const data = this.sortedData
            if (!this.mergeColumns || this.mergeColumns.length === 0 || data.length === 0) {
                return spans
            }

            this.mergeColumns.forEach(key => {
                spans[key] = []
                let i = 0
                while (i < data.length) {
                    let span = 1
                    const val = data[i][key]
                    while (i + span < data.length && data[i + span][key] === val) {
                        span++
                    }
                    spans[key][i] = span
                    for (let j = 1; j < span; j++) {
                        spans[key][i + j] = 0
                    }
                    i += span
                }
            })
            return spans
        },

        totalColumns() {
            let count = this.headers.length
            if (this.selectable) count++
            if (this.$slots.actions) count++
            return count
        },
    },

    methods: {
        isNewGroup(item, index) {
            if (!this.groupBy) return false
            if (index === 0) return true
            const prevItem = this.sortedData[index - 1]
            return prevItem[this.groupBy] !== item[this.groupBy]
        },

        highlight(text) {
            if (!this.showSearch || !this.searchQuery) return text;
            const regex = new RegExp(`(${this.searchQuery})`, 'gi');
            return text ? text.toString().replace(regex, '<span class="highlight">$1</span>') : text;
        },

        /** ---------- SORT ---------- */
        sortTable(key) {
            if (this.sortKey === key) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
            } else {
                this.sortKey = key
                this.sortOrder = 'asc'
            }
        },

        /** ---------- PAGE CHANGE + CLEAR SELECT ---------- */
        changePage(page) {
            this.fetchPage(page)
        },

        isRowDisabled(item) {
            return !!item.disabled
        },

        /** ---------- SELECTION LOGIC ---------- */
        isSelected(item) {
            return this.selectedMap.has(item[this.selectKey])
        },

        /** * Đảo trạng thái chọn của một item (Bất kể chế độ single/multiple) */
        toggleSelect(item) {
            if (item.disabled) return

            const key = item[this.selectKey]

            if (this.selectMode === 'single') {
                this.selectedMap.clear()
                this.selectedMap.set(key, item)
            } else {
                if (this.selectedMap.has(key)) this.selectedMap.delete(key)
                else this.selectedMap.set(key, item)
            }

            this.lastSelectedKey = key // Luôn cập nhật key được chọn/bỏ chọn cuối cùng

            this.emitSelection()
            this.$emit('select-item', item)
        },

        toggleSelectAll() {
            if (this.isAllSelected) {
                this.selectedMap.clear()
            } else {
                this.data.forEach((item) => {
                    if (!item.disabled) {
                        this.selectedMap.set(item[this.selectKey], item)
                    }
                    // this.selectedMap.set(item[this.selectKey], item)
                })
            }
            this.lastSelectedKey = null
            this.emitSelection()
            this.$emit('select-all', Array.from(this.selectedMap.values()))
        },

        /** Xử lý click hàng mặc định (cho selectMode='single' hoặc click đơn giản) */
        handleDefaultRowClick(item) {
            this.toggleSelect(item)
            this.$emit('row-clicked', item)
        },

        // ************** PHƯƠNG THỨC MỚI: THEO DÕI CHUỘT **************
        handleMouseDown(event) {
            this.mouseDownCoords = {
                x: event.clientX,
                y: event.clientY,
                time: Date.now(),
            }
        },

        handleMouseUp(item, event) {
            if (item.disabled) return

            const dx = Math.abs(event.clientX - this.mouseDownCoords.x)
            const dy = Math.abs(event.clientY - this.mouseDownCoords.y)
            const dt = Date.now() - this.mouseDownCoords.time

            // Nếu có sự di chuyển đáng kể (> 5px) hoặc thời gian giữ chuột lâu (> 200ms),
            // coi là thao tác kéo/quét, KHÔNG CHỌN HÀNG.
            const isDragging = dx > 5 || dy > 5 || dt > 200

            if (isDragging) {
                return
            }

            // Nếu là click, tiến hành logic chọn hàng
            if (this.selectMode === 'multiple') {
                this.handleRowClick(item, event)
            } else {
                this.handleDefaultRowClick(item)
            }
        },
        // *************************************************************

        /**
         * Chứa logic chọn nâng cao (Shift/Ctrl/Cmd) cho selectMode='multiple'.
         * Hàm này được gọi bởi handleMouseUp (khi xác định đó là thao tác click).
         */
        handleRowClick(item, event) {
            if (item.disabled) return

            const currentKey = item[this.selectKey]
            const isShift = event.shiftKey
            const isCtrlOrCmd = event.ctrlKey || event.metaKey // metaKey là Cmd trên Mac

            if (isShift && this.lastSelectedKey !== null) {
                // 1. Logic chọn liên tục (Shift + Click)
                const dataKeys = this.sortedData.map((d) => d[this.selectKey])
                const startIdx = dataKeys.indexOf(this.lastSelectedKey)
                const endIdx = dataKeys.indexOf(currentKey)

                if (startIdx !== -1 && endIdx !== -1) {
                    const [minIdx, maxIdx] = [Math.min(startIdx, endIdx), Math.max(startIdx, endIdx)]
                    this.selectedMap.clear()

                    for (let i = minIdx; i <= maxIdx; i++) {
                        const key = dataKeys[i]
                        const itemToSelect = this.sortedData.find((d) => d[this.selectKey] === key)
                        if (!itemToSelect.disabled) {
                            this.selectedMap.set(key, itemToSelect)
                        }
                        // this.selectedMap.set(key, itemToSelect)
                    }
                }
            } else if (isCtrlOrCmd) {
                // 2. Logic chọn không liên tục (Ctrl/Cmd + Click)
                this.toggleSelect(item)
            } else {
                // 3. Logic chọn đơn giản (Click bình thường)
                // Click thường = Thêm/Bỏ chọn dòng hiện tại (Không xóa các dòng khác)
                this.toggleSelect(item)
            }

            this.emitSelection()
            this.$emit('row-clicked', item)
        },
        resetSelection() {
            this.selectedMap.clear()
            this.emitSelection(true) // true báo hiệu là clear
            this.lastSelectedKey = null
            this.$emit('select-all', []) // Emit sự kiện rỗng nếu cần thiết
            this.$emit('selection-changed', [], false)
        },
        /** Emit mảng object hoặc mảng key */
        emitSelection(clear = false) {
            let payload = []

            if (this.returnType === 'object') {
                payload = Array.from(this.selectedMap.values())
            } else {
                payload = Array.from(this.selectedMap.keys())
            }

            this.$emit('selection-changed', payload, clear)
        },
    },
}
</script>

<style>
.highlight {
    background-color: yellow;
    font-weight: bold;
    border-radius: 2px;
}
.row-selected,
.table-striped tbody tr.row-selected,
.table-striped tbody tr.row-selected:nth-of-type(odd) {
    background-color: #d0ebff !important;
}

/* Modernized styles for all legacy actions icons to look like premium IconButtons */
.icon-block,
.icon-unblock,
.icon-edit,
.icon-delete,
.icon-detail {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 28px !important;
    height: 28px !important;
    padding: 0 !important;
    border-radius: 6px !important;
    border: 1px solid transparent !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer !important;
    font-size: 12px !important;
    line-height: 1 !important;
    box-sizing: border-box !important;
    margin: 0 2px !important;
    float: none !important;
}

/* Hover active scaling */
.icon-block:active,
.icon-unblock:active,
.icon-edit:active,
.icon-delete:active,
.icon-detail:active {
    transform: scale(0.95);
}

/* Edit / Detail (Blue) */
.icon-edit,
.icon-detail {
    background-color: #eff6ff !important;
    color: #2563eb !important;
    border-color: #dbeafe !important;
}
.icon-edit:hover,
.icon-detail:hover {
    background-color: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -2px rgba(37, 99, 235, 0.2) !important;
}

/* Delete / Block (Red) */
.icon-delete,
.icon-block {
    background-color: #fef2f2 !important;
    color: #dc2626 !important;
    border-color: #fee2e2 !important;
}
.icon-delete:hover,
.icon-block:hover {
    background-color: #dc2626 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
    box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2), 0 2px 4px -2px rgba(220, 38, 38, 0.2) !important;
}

/* Unblock (Emerald) */
.icon-unblock {
    background-color: #ecfdf5 !important;
    color: #059669 !important;
    border-color: #d1fae5 !important;
}
.icon-unblock:hover {
    background-color: #059669 !important;
    color: #ffffff !important;
    border-color: #059669 !important;
    box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.2), 0 2px 4px -2px rgba(5, 150, 105, 0.2) !important;
}

.table { margin-bottom: 0rem !important; }
.table td, .table th { padding: 0.4rem !important; vertical-align: middle; border-top: 1px solid #dee2e6; border-left: 1px solid #e9ecef; font-size: 15px; }
.table td:last-child, .table th:last-child { border-right: 1px solid #e9ecef; }
.table thead tr th { border-top: none; background-color: #007bff !important; color: white; border-left: 1px solid rgba(255, 255, 255, 0.2); }
.table thead tr th:first-child { border-top-left-radius: 6px; border-left: none; }
.table thead tr th:last-child { border-top-right-radius: 6px; border-right: 1px solid rgba(255, 255, 255, 0.2); }

/* Lighter, gentler alternating rows and hover state overrides */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #fafbfc !important; /* Very light slate-gray tint */
}
.table-hover tbody tr:hover {
    background-color: #f1f5f9 !important; /* Soft modern hover highlight */
}
.table-hover tbody tr.row-selected:hover {
    background-color: #d0ebff !important; /* Prevent selected rows from changing color on hover */
}

/* Center all table actions and prevent wrapping */
.tablepro-actions-th,
.tablepro-actions-td {
    text-align: center !important;
    white-space: nowrap !important; /* Force single line */
}
.tablepro-actions-wrapper {
    display: inline-flex !important; /* Fit content size exactly */
    justify-content: center !important;
    align-items: center !important;
    gap: 4px !important;
    flex-wrap: nowrap !important; /* Force single line */
}
.tablepro-actions-wrapper > div,
.tablepro-actions-wrapper > span,
.tablepro-actions-wrapper > section,
.tablepro-actions-wrapper > p,
.tablepro-actions-wrapper > .d-flex,
.tablepro-actions-wrapper > .flex {
    display: inline-flex !important; /* Fit content size exactly */
    justify-content: center !important;
    align-items: center !important;
    gap: 4px !important;
    margin: 0 !important;
    padding: 0 !important;
    flex-wrap: nowrap !important; /* Force single line */
}
.tablepro-actions-wrapper * {
    float: none !important;
}

/* CSS Styling cho dòng tiêu đề nhóm */
.tablepro-group-header-row {
    background-color: #f8fafc !important; /* light slate background */
}
.tablepro-group-header-td {
    font-weight: bold;
    color: #0f172a !important; /* dark slate color */
    padding: 0.6rem 0.8rem !important;
    text-transform: uppercase;
    font-size: 13px !important;
    letter-spacing: 0.05em;
    border-left: 4px solid #007bff !important; /* blue accent left border */
    text-align: left !important;
}
</style>
