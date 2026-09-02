<template>
    <div
        class="multi-checkbox"
        :class="{ disabled }"
        @click="!disabled && nextState($event)"
        :tabindex="disabled ? -1 : 0"
        @keydown.space.prevent="!disabled && nextState($event)"
        @keydown.enter.prevent="!disabled && nextState($event)"
        aria-disabled="disabled"
    >
        <div class="box" :style="currentStateStyle">
            <span>{{ currentState.icon }}</span>
        </div>
        <span class="label" v-if="label || $slots.default">
            <slot>{{ label }}</slot>
        </span>
    </div>
</template>

<script>
export default {
    name: 'MultiStateCheckbox',
    props: {
        // modelValue giờ chấp nhận mọi kiểu dữ liệu
        modelValue: {
            type: [String, Number, Boolean],
            default: 'pending',
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: '',
        },
        options: {
            type: Array,
            // Default options bây giờ cần có thêm trường 'value'
            default: () => [
                { value: 'pending', icon: '…', bg: '#f4f4f4', color: '#666' },
                { value: 'approved', icon: '✓', bg: '#c8f7c5', color: '#0a7a00' },
                { value: 'rejected', icon: '✕', bg: '#f9c5c5', color: '#a00000' },
            ],
        },
    },
    emits: ['update:modelValue', 'change'],
    computed: {
        // Tìm option đang active dựa trên modelValue so sánh với option.value
        currentIndex() {
            const idx = this.options.findIndex((opt) => opt.value === this.modelValue)
            // Nếu không tìm thấy (ví dụ dữ liệu lạ), mặc định về 0
            return idx === -1 ? 0 : idx
        },
        currentState() {
            return this.options[this.currentIndex]
        },
        currentStateStyle() {
            const state = this.currentState
            return {
                backgroundColor: state.bg || 'transparent',
                color: state.color || 'inherit',
                borderColor: state.color || '#666',
                opacity: this.disabled ? 0.5 : 1,
            }
        },
    },
    methods: {
        nextState(e) {
            if (this.disabled) return
            // Logic: Lấy index hiện tại + 1, chia lấy dư cho độ dài mảng
            const nextIndex = (this.currentIndex + 1) % this.options.length

            // Lấy ra VALUE của option tiếp theo (chứ không phải lấy index)
            const nextValue = this.options[nextIndex].value

            this.$emit('update:modelValue', nextValue)
            this.$emit('change', nextValue, e)
        },
    },
}
</script>

<style scoped>
.multi-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    gap: 8px;
    user-select: none;
}

.multi-checkbox.disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.box {
    width: 22px;
    height: 22px;
    border: 2px solid #ccc;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    border-radius: 4px;
    transition: all 0.2s;
    font-weight: bold;
}
.label {
    /* font-size: 14px; */
}
</style>
