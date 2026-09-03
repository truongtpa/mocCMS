<template>
    <div class="table-responsive">
        <table
            :class="[
                'table',
                'app-table',
                'table-sm',
                'mb-0',
                { 'table-hover': hover },
                { 'table-striped': striped },
                { 'table-bordered': bordered }
            ]"
        >
            <slot name="thead">
                <thead style="background-color: #007bff !important;">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            :style="{
                                width: col.width || 'auto',
                                minWidth: col.minWidth || '120px',
                                maxWidth: col.maxWidth || 'auto',
                                textAlign: col.align || 'left'
                            }"
                            class="py-2 px-2.5 text-white font-weight-bold text-xs border-0 text-nowrap"
                        >
                            <slot :name="`header-${col.key}`" :column="col">
                                {{ col.label }}
                            </slot>
                        </th>
                        <th
                            v-if="hasActionsSlot"
                            :style="{ width: actionsWidth || '90px', minWidth: actionsWidth || '90px', textAlign: 'center' }"
                            class="py-2 px-2.5 text-white font-weight-bold text-xs border-0 text-nowrap sticky-actions-col"
                        >
                            {{ actionsLabel || 'Thao tác' }}
                        </th>
                    </tr>
                </thead>
            </slot>

            <slot name="tbody">
                <tbody>
                    <template v-if="items && items.length > 0">
                        <tr v-for="(item, index) in items" :key="item[itemKey] || index">
                            <td
                                v-for="col in columns"
                                :key="col.key"
                                :style="{ textAlign: col.align || 'left' }"
                                class="py-2 px-2.5 text-xs text-nowrap"
                            >
                                <slot :name="`col-${col.key}`" :item="item" :index="index">
                                    {{ item[col.key] }}
                                </slot>
                            </td>
                            <td v-if="hasActionsSlot" class="text-center py-2 px-2.5 text-xs text-nowrap sticky-actions-col">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <slot name="actions" :item="item" :index="index"></slot>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr v-else-if="$slots.empty || emptyText">
                        <td :colspan="totalColumns" class="py-3 text-center text-muted italic text-xs">
                            <slot name="empty">
                                {{ emptyText || 'Chưa có dữ liệu nào trong hệ thống.' }}
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </slot>

            <slot></slot>
        </table>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue'

const props = defineProps({
    columns: {
        type: Array,
        default: () => []
    },
    items: {
        type: Array,
        default: () => []
    },
    itemKey: {
        type: String,
        default: 'id'
    },
    hover: {
        type: Boolean,
        default: true
    },
    striped: {
        type: Boolean,
        default: false
    },
    bordered: {
        type: Boolean,
        default: false
    },
    emptyText: {
        type: String,
        default: 'Chưa có dữ liệu nào trong hệ thống.'
    },
    actionsLabel: {
        type: String,
        default: 'Thao tác'
    },
    actionsWidth: {
        type: String,
        default: '90px'
    }
})

const slots = useSlots()

const hasActionsSlot = computed(() => !!slots.actions)

const totalColumns = computed(() => {
    let count = props.columns ? props.columns.length : 0
    if (hasActionsSlot.value) count += 1
    return count || 1
})
</script>

<style scoped>
.table-responsive {
    overflow-x: auto !important;
    position: relative !important;
    -webkit-overflow-scrolling: touch;
}

.app-table {
    margin-bottom: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

.app-table th,
.app-table td {
    vertical-align: middle !important;
    white-space: nowrap !important;
}

/* Sticky Action Column */
.sticky-actions-col {
    position: -webkit-sticky !important;
    position: sticky !important;
    right: 0 !important;
    z-index: 10 !important;
    box-shadow: -3px 0 6px -2px rgba(0, 0, 0, 0.15) !important;
}

thead th.sticky-actions-col {
    z-index: 15 !important;
    background-color: #007bff !important;
    color: #ffffff !important;
}

tbody td.sticky-actions-col {
    background-color: #ffffff !important;
}

tbody tr:nth-of-type(odd) td.sticky-actions-col {
    background-color: #f8fafc !important;
}

tbody tr:hover td.sticky-actions-col {
    background-color: #f1f5f9 !important;
}
</style>
