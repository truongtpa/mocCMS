<template>
    <label v-if="label" class="font-weight-bold small text-muted text-xs mb-1">{{ label }}</label>
    <div class="flex-grow-1 search-group" @click="onOutsideClick">
        <input 
            type="text" 
            :class="inputClass" 
            v-model="searchQuery" 
            @input="handleFind"
            @focusin="() => { searchFocus = true; handleFind() }" 
            @blur="searchFocusOut" 
            :placeholder="placeholder"
            :disabled="disabled" 
        />

        <div v-if="suggestList.length && searchFocus" class="suggest">
            <div 
                v-for="(item, index) in suggestList" 
                :key="index" 
                @mousedown="onSuggestMouseDown"
                @click="handleSuggestClick(item, index)" 
                class="suggest-item"
            >
                <div class="w-100 h-100 d-flex align-items-center"
                    v-html="template?.(item) ?? 'Hãy nhập template cho item'">
                </div>
            </div>

            <div v-if="typeof templateNotResult === 'function'" class="suggest-item d-flex align-items-center"
                v-html="templateNotResult?.()"></div>
        </div>
    </div>
</template>

<script setup>
import debounce from "lodash.debounce"
import { ref, watch } from "vue"

const modelValue = defineModel() // v-model: value thực
const props = defineProps({
    search: {
        type: Function,
        required: true // Gọi API hoặc fetch gợi ý
    },
    action: Function, // Hàm callback khi chọn item
    placeholder: {
        type: String,
        default: "Nhập một vài từ khóa để tìm kiếm"
    },
    label: {
        type: String,
        default: ""
    },
    inputClass: {
        type: String,
        default: "form-control form-control-sm text-xs"
    },
    disabled: Boolean,
    template: Function, // Template hiển thị trong danh sách
    templateShow: Function, // Hàm hiển thị khi chọn (label)
    templateNotResult: Function, // Khi không có kết quả
    freeText: {
        type: Boolean,
        default: false
    },
    emitItem: {
        type: Boolean,
        default: true
    }
})

const searchQuery = ref("")
const suggestList = ref([])
const searchFocus = ref(true)
const isClickingOnSuggest = ref(false)

const onSuggestMouseDown = () => {
    isClickingOnSuggest.value = true
}

const searchFocusOut = () => {
    setTimeout(() => {
        if (!isClickingOnSuggest.value) {
            const found = suggestList.value.find(item =>
                props.templateShow?.(item) === searchQuery.value
            )

            if (found) {
                modelValue.value = found.value ?? found.id
                if (props.emitItem) props.action?.(found)
            } else if (props.freeText) {
                modelValue.value = searchQuery.value
                if (props.emitItem) {
                    props.action?.({
                        value: null,
                        text: searchQuery.value,
                        isFreeText: true
                    })
                }
            } else {
                modelValue.value = null
                if (props.emitItem) props.action?.(null)
            }

            searchFocus.value = false
        }
        isClickingOnSuggest.value = false
    }, 0)
}

const handleFind = debounce(async () => {
    try {
        suggestList.value = await props.search(searchQuery.value)
    } catch (error) {
        suggestList.value = []
    }
}, 300)

const handleSuggestClick = (item, index) => {
    searchQuery.value = props.templateShow?.(item) ?? item.text ?? item.label ?? ''
    modelValue.value = item.value ?? item.id ?? item

    if (props.emitItem) {
        props.action?.(item)
    }

    suggestList.value = []
    searchFocus.value = false
}

// Sync input text khi v-model thay đổi từ bên ngoài
watch(modelValue, async (val) => {
    if (!val) {
        searchQuery.value = ""
        return
    }

    const list = await props.search("")
    const found = list.find(item => item.value === val || item.id === val)
    if (props.templateShow?.(found)) {
        searchQuery.value = props.templateShow?.(found) ?? found?.text ?? found?.label ?? String(val)
    } else {
        searchQuery.value = found?.text ?? found?.label ?? String(val)
    }
}, { immediate: true })
</script>

<style scoped>
.search-group {
    position: relative;
}

.suggest {
    position: absolute;
    max-height: 220px;
    top: calc(100% + 2px);
    width: 100%;
    background-color: #ffffff;
    z-index: 1060;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    scrollbar-width: thin;
    padding: 3px 0;
}

.suggest-item {
    display: flex;
    align-items: center;
    min-height: 28px;
    padding: 4px 10px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.75rem;
    color: #334155;
}

.suggest-item:last-child {
    border-bottom: none;
}

.suggest-item:hover {
    background-color: #f1f5f9;
    color: #0f172a;
}
</style>
