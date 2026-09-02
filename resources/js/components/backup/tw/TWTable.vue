<template>
    <div class="overflow-x-auto bg-white rounded-xl shadow-sm border border-slate-200">
        <table class="w-full text-left border-collapse table-auto">
            <thead class="bg-blue-600 text-white border-b border-blue-700">
                <tr>
                    <th v-if="selectable" class="px-3 py-2.5 w-10 text-center border-r border-blue-500/30">
                        <input
                            type="checkbox"
                            :checked="isAllSelected"
                            @change="toggleAll"
                            class="rounded text-blue-800 focus:ring-blue-500 bg-white/20 border-white/40 cursor-pointer h-4 w-4"
                        />
                    </th>
                    <th
                        v-for="h in headers"
                        :key="h.key"
                        class="px-3 py-2.5 text-[11px] font-bold uppercase tracking-wider"
                        :style="h.css"
                        :class="h.class"
                    >
                        {{ h.label }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr v-if="loading">
                    <td :colspan="columnCount" class="py-10 text-center text-xs text-slate-400">
                        <div class="flex justify-center items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang tải dữ liệu...
                        </div>
                    </td>
                </tr>
                <tr v-else-if="!data || data.length === 0">
                    <td :colspan="columnCount" class="py-10 text-center text-xs text-slate-400 font-medium italic">
                        {{ emptyLabel }}
                    </td>
                </tr>
                <template v-else>
                    <tr
                        v-for="(item, index) in data"
                        :key="item[itemKey] || index"
                        class="transition-colors group"
                        :class="[
                            striped ? 'even:bg-slate-50/50' : '',
                            isSelected(item) ? 'bg-blue-50/60' : 'hover:bg-blue-50/30',
                            rowClass(item, index)
                        ]"
                    >
                        <td v-if="selectable" class="px-3 py-1.5 text-center border-r border-slate-100/50">
                            <input
                                type="checkbox"
                                :value="item[itemKey]"
                                :checked="isSelected(item)"
                                @change="toggleItem(item)"
                                class="rounded text-blue-600 focus:ring-blue-500 cursor-pointer h-4 w-4"
                            />
                        </td>
                        <td
                            v-for="h in headers"
                            :key="h.key"
                            class="px-3 py-1.5 align-middle"
                        >
                            <slot :name="h.key" :item="item" :index="index">
                                {{ item[h.key] }}
                            </slot>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    data: { type: Array, default: () => [] },
    headers: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
    emptyLabel: { type: String, default: 'Không có dữ liệu' },
    striped: { type: Boolean, default: true },
    selectable: { type: Boolean, default: false },
    selected: { type: Array, default: () => [] },
    itemKey: { type: String, default: 'id' },
    rowClass: { type: Function, default: () => '' }
})

const emit = defineEmits(['update:selected'])

const columnCount = computed(() => {
    return props.headers.length + (props.selectable ? 1 : 0)
})

const isAllSelected = computed(() => {
    return props.data.length > 0 && props.selected.length === props.data.length
})

const toggleAll = (e) => {
    if (e.target.checked) {
        emit('update:selected', props.data.map(item => item[props.itemKey]))
    } else {
        emit('update:selected', [])
    }
}

const isSelected = (item) => {
    return props.selected.includes(item[props.itemKey])
}

const toggleItem = (item) => {
    const val = item[props.itemKey]
    const arr = [...props.selected]
    const idx = arr.indexOf(val)
    if (idx > -1) {
        arr.splice(idx, 1)
    } else {
        arr.push(val)
    }
    emit('update:selected', arr)
}
</script>
