<template>
    <div class="row">
        <div class="col-12">
            <!-- Hiển thị bảng nếu không phải mobile -->
            <div v-if="!isMobile" class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-primary table-borderless">
                    <tr>
                        <th v-if="selectFeature && Array.isArray(data) && data.length > 0" class="select-slot">
                            <div class="select-slot-wrapper">
                                <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll" />
                            </div>
                        </th>
                        <th
                            v-for="header in headers"
                            :key="header.key"
                            :style="header.css"
                            @click="sortTable(header.key)"
                        >
                            {{ header.label }}
                            <span v-if="sortKey === header.key">
                  <i v-if="sortOrder === 'asc'" class="fas fa-sort-amount-up"></i>
                  <i v-if="sortOrder === 'desc'" class="fas fa-sort-amount-up-alt"></i>
                </span>
                        </th>
                        <th v-if="$slots.actions" :style="cssActions"></th>
                    </tr>
                    </thead>
                    <tbody class="table-borderless">
                    <template v-for="group in groupedData" :key="group.key">
                        <tr v-if="groupBy" class="table-group-row">
                            <td :colspan="columnCount">{{ group.label }}</td>
                        </tr>
                        <tr
                            v-for="(item, index) in group.items"
                            :key="getValueByPath(item.raw, trackBy) || (group.key + '-' + index)"
                            :class="item.rowClass"
                        >
                            <td v-if="selectFeature" class="select-slot">
                                <div class="select-slot-wrapper">
                                    <input
                                        type="checkbox"
                                        :checked="modelValue.includes(getValueByPath(item, trackBy))"
                                        @change="toggleSelectItem(item)"
                                    />
                                </div>
                            </td>
                            <td v-for="header in headers" :key="header.key" v-html="item[header.key]"></td>
                            <td v-if="$slots.actions" class="action-slot">
                                <slot name="actions" :item="item"></slot>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
                <div v-if="!data.length" class="text-center text-muted d-block p-2"><span>Chưa có dữ liệu</span></div>
            </div>

            <!-- Hiển thị dạng div khi mobile -->
            <div v-else>
                <hr>
                <template v-for="group in groupedData" :key="group.key">
                    <div v-if="groupBy" class="table-group-row p-2 mb-2">{{ group.label }}</div>
                    <div
                        v-for="(item, index) in group.items"
                        :key="getValueByPath(item.raw, trackBy) || (group.key + '-' + index)"
                        class="card mb-2 p-2 shadow-sm"
                    >
                        <div v-for="header in headers" :key="header.key" class="mb-1">
                            <strong>{{ header.label }}: </strong>
                            <span v-html="item[header.key]"></span>
                        </div>
                        <div v-if="$slots.actions" class="mt-2">
                            <slot name="actions" :item="item"></slot>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="col-6">
            <br>
            Có tổng cộng <b>{{ pagination.total }}</b> dòng dữ liệu.
        </div>

        <div class="col-6">
            <br>
            <ul style="margin-top: 12px;" v-if="pagination.last_page > 1" class="pagination pagination-sm m-0 float-right">
                <!-- Previous Button -->
                <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                    <a class="page-link" href="#" @click.prevent="fetchPage(pagination.prev_page_url)">Trang trước</a>
                </li>

                <!-- Loop through the total pages and display them -->
                <li v-for="page in pagination.last_page" :key="page" class="page-item"
                    :class="{ active: pagination.current_page === page }">
                    <a class="page-link" href="#" @click.prevent="fetchPage(getPageUrl(page))">{{ page }}</a>
                </li>

                <!-- Next Button -->
                <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                    <a class="page-link" href="#" @click.prevent="fetchPage(pagination.next_page_url)">Trang sau</a>
                </li>
            </ul>
        </div>

    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Array,
            required: true
        },
        headers: {
            type: Array,
            required: true
        },
        pagination: {
            type: Object,
            required: true
        },
        fetchPage: {
            type: Function,
            required: true
        },
        cssActions: {
            type: String,
            default: ""
        },
        selectFeature: {
            type: Boolean,
            default: false
        },
        trackBy: {
            type: String,
            require: true
        },
        selectedItems: {
            type: Array,
            default: () => []
        },
        isFooter: {
            type: Boolean,
            default: true
        },
        // Đường dẫn tới giá trị dùng để gom nhóm (vd: "raw.nhom"). Bỏ trống thì bảng hiển thị như cũ.
        groupBy: {
            type: String,
            default: ""
        },
        groupEmptyLabel: {
            type: String,
            default: "Chưa phân nhóm"
        }
    },
    data() {
        return {
            sortKey: '',
            sortOrder: 'asc',
            isMobile: window.innerWidth <= 768,
        };
    },
    mounted() {
        window.addEventListener('resize', this.handleResize);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    },
    computed: {
        sortedData() {
            let sortedData = [...this.data];
            if (this.sortKey) {
                // xác định kiểu dữ liệu của cột hiện tại
                const columnType = this.detectColumnType(this.sortKey);

                sortedData.sort((a, b) => {
                    let valA = a[this.sortKey] ?? '';
                    let valB = b[this.sortKey] ?? '';
                    let result = 0;

                    if (columnType === 'number') {
                        result = Number(valA) - Number(valB);
                    }
                    else if (columnType === 'date') {
                        result = this.compareDate(valA, valB);
                    }
                    else { // string
                        if (valA < valB) result = -1;
                        if (valA > valB) result = 1;
                    }

                    return this.sortOrder === 'asc' ? result : -result;
                });
            }
            return sortedData;
        },

        columnCount() {
            return this.headers.length + (this.selectFeature ? 1 : 0) + (this.$slots.actions ? 1 : 0);
        },
        // Gom dòng theo groupBy, giữ thứ tự nhóm xuất hiện lần đầu trong dữ liệu đã sắp xếp.
        groupedData() {
            if (!this.groupBy) {
                return [{ key: '', label: '', items: this.sortedData }];
            }

            const groups = new Map();
            this.sortedData.forEach((item) => {
                const value = this.getValueByPath(item, this.groupBy);
                const label = (value === null || value === undefined || value === '')
                    ? this.groupEmptyLabel
                    : String(value);
                if (!groups.has(label)) {
                    groups.set(label, { key: label, label: label, items: [] });
                }
                groups.get(label).items.push(item);
            });

            return Array.from(groups.values());
        },
        visiblePages() {
            const currentPage = this.pagination.current_page;
            const lastPage = this.pagination.last_page;
            const pages = [];
            const start = Math.max(1, currentPage - 1);
            const end = Math.min(lastPage, currentPage + 1);
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        modelValue: {
            get() {
                return this.selectedItems;
            },
            set(val) {
                this.$emit('update:selectedItems', val);
            }
        },
        isAllSelected() {
            const allIds = this.data.map(item => this.getValueByPath(item, this.trackBy));
            return this.data.length > 0 && allIds.every(id => this.modelValue.includes(id));
        }
    },
    methods: {
        handleResize() {
            this.isMobile = window.innerWidth <= 768;
        },

        detectColumnType(key) {
            const sampleValues = this.data.slice(0, 5).map(row => row[key]).filter(v => v !== null && v !== undefined);

            if (sampleValues.length && sampleValues.every(v => !isNaN(Number(v)))) {
                return 'number';
            }

            const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
            if (sampleValues.length && sampleValues.every(v => dateRegex.test(v))) {
                return 'date';
            }

            return 'string';
        },
        compareDate(a, b) {
            let [da, ma, ya] = a.split('/');
            let [db, mb, yb] = b.split('/');

            let dateA = new Date(`${ya}-${ma}-${da}`);
            let dateB = new Date(`${yb}-${mb}-${db}`);

            return dateA - dateB;
        },
        sortTable(key) {
            if (this.sortKey === key) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortOrder = 'asc';
            }
        },
        getPageUrl(page) {
            return `${this.pagination.path}?page=${page}`;
        },
        getValueByPath(obj, path) {
            if (!path || typeof path !== 'string') return undefined;
            return path.split('.').reduce((o, key) => (o ? o[key] : undefined), obj);
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.modelValue = this.data.map(item => this.getValueByPath(item, this.trackBy));
            } else {
                this.modelValue = [];
            }
        },

        toggleSelectItem(item) {
            const id = this.getValueByPath(item, this.trackBy);
            const newSelected = [...this.modelValue];

            const index = newSelected.indexOf(id);
            if (index === -1) {
                newSelected.push(id);
            } else {
                newSelected.splice(index, 1);
            }

            this.modelValue = newSelected;
        }
    },
    emits: ['update:selectedItems', 'row-click']
    ,
    watch: {
        selectedItems: {
            handler(newVal) {
                this.$emit('update:selectedItems', newVal);
            },
            deep: true,
            immediate: true
        }
    }
};
</script>

<style>
.blink {
    animation: blink-animation 1s infinite;
}

@keyframes blink-animation {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
    100% {
        opacity: 1;
    }
}

.text-primary {
    color: #1b70ef !important;
}

.text-white {
    color: #ffffff !important;
}

.text-red {
    color: #ff0000 !important;
}

.text-black {
    color: #000000 !important;
}

.badge-success {
    background-color: #28a745 !important;
}

.badge-secondary {
    background-color: #6c757d !important;
}

.badge-primary {
    background-color: #007bff !important;
}

.badge-warning {
    background-color: #fff30c !important;
}

.badge-danger {
    background-color: #dc3545 !important;
}


.icon-edit {
    font-size: 11px;
    background: #0c84ff;
    color: white;
    padding: 5px 3px 5px 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
}

.icon-view {
    font-size: 11px;
    background: #ffc60c;
    color: white;
    padding: 5px 3px 5px 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
}

.icon-view-green {
    font-size: 11px;
    background: #169100;
    color: white;
    padding: 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
}

.icon-delete {
    font-size: 11px;
    background: red;
    color: white;
    padding: 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
}

.icon-add {
    font-size: 11px;
    background: #17a2b8;
    color: white;
    padding: 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
}

.table td, .table th {
    padding: .35rem !important;
    vertical-align: middle;
    font-size: 15px;
}

.table {
    margin: 0px;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 5px;
    border: 1px solid lightgrey;
}

.table thead th tbody td {
    border: none !important;
}

.table-group-row {
    background: #eef3fb;
}

.table-group-row td, .table-group-row {
    font-weight: 700;
    color: #1b70ef;
}

.action-slot, .select-slot {
    width: 1%;
    white-space: nowrap;
}

.action-slot > * {
    cursor: pointer;
}

.select-slot-wrapper {
    display: flex;
    align-content: center;
    justify-items: center;
}

.select-slot-wrapper input[type="checkbox"] {
    width: 14px;
    height: 14px;
    margin: 0 3px;
    padding: 1px 0 0 1px;
}

.select-slot-wrapper input[type="checkbox"]:focus {
    outline: none;
}

/* Bảng nhiều trang (vài chục nút) phải xuống dòng thay vì tràn ngang. */
.pagination {
    flex-wrap: wrap;
    justify-content: flex-end;
    row-gap: 4px;
    max-width: 100%;
}

/* Bo góc theo từng dòng đã wrap, không chỉ nút đầu/cuối của cả danh sách. */
.pagination .page-item .page-link {
    border-radius: 3px;
    margin-left: 2px;
}

@media (max-width: 768px) {
    .table {
        display: block;
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }

    .table th, .table td {
        padding: 10px;
        font-size: 15px;
    }

    .pagination {
        justify-content: center;
    }
}
</style>
