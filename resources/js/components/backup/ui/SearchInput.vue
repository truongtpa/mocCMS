<template>
    <div class="input-group col-md-6 col-12 py-md-0 pb-md-3 px-1 py-3">
        <input type="text" class="form-control float-right" v-model="searchQuery" @input="handleSearch"
            placeholder="Tìm kiếm thông tin ..." />
        <div class="input-group-append">
            <button type="submit" class="btn btn-default px-4">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    data: {
        type: [Array, Object],
        required: true,
        default: () => [],
    },
    include: {
        type: Array,
        required: false,
        default: () => [],
    },
});

const emit = defineEmits(['update:dataFilter']);

const searchQuery = ref('');


function flattenValues(obj) {
    const result = [];

    function recurse(value) {
        if (value == null) return;
        if (typeof value === 'string' || typeof value === 'number' || typeof value === 'boolean') {
            result.push(String(value));
        } else if (Array.isArray(value)) {
            value.forEach(recurse);
        } else if (typeof value === 'object') {
            Object.values(value).forEach(recurse);
        }
    }

    recurse(obj);
    return result;
}

const handleSearch = () => {
    const keyword = searchQuery.value.trim().toLowerCase();

    if (!keyword) {
        emit('update:dataFilter', props.data);
        return;
    }

    const isArray = Array.isArray(props.data);
    const sourceData = isArray ? props.data : Object.values(props.data);

    const filtered = sourceData.filter(item => {
        const fields = props.include.length > 0 ? props.include : Object.keys(item);
        const values = fields.flatMap(field => flattenValues(item[field]));
        return values.some(v => v.toLowerCase().includes(keyword));
    });

    const finalResult = isArray
        ? filtered
        : Object.fromEntries(Object.entries(props.data).filter(([, val]) => filtered.includes(val)));

    emit('update:dataFilter', finalResult);
};

// Optional: auto-run filter when `props.data` changes (if needed)
// watch(() => props.data, handleSearch);
</script>
