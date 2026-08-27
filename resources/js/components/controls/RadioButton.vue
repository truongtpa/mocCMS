<template>
    <div class="custom-radio" @click="selectOption" :class="{ disabled }">
        <div class="box" :style="boxStyle">
            <span v-if="isChecked" class="inner-dot">●</span>
        </div>

        <span class="label" v-if="label || $slots.default">
            <slot>{{ label }}</slot>
        </span>
    </div>
</template>

<script>
export default {
    name: 'CustomRadio',
    props: {
        modelValue: {
            type: [String, Number, Boolean],
            default: null,
        },
        value: {
            type: [String, Number, Boolean],
            required: true,
        },
        label: {
            type: String,
            default: '',
        },
        // Màu mặc định khi chọn (xanh lá giống checkbox 'approved')
        activeColor: {
            type: String,
            default: '#0a7a00',
        },
        activeBg: {
            type: String,
            default: '#c8f7c5',
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['update:modelValue', 'change'],
    computed: {
        isChecked() {
            return this.modelValue === this.value
        },
        boxStyle() {
            if (this.disabled) {
                return {
                    backgroundColor: '#f5f5f5',
                    color: '#aaa',
                    borderColor: '#ccc',
                    cursor: 'not-allowed',
                    opacity: 0.8,
                }
            }

            if (this.isChecked) {
                return {
                    backgroundColor: this.activeBg,
                    color: this.activeColor,
                    borderColor: this.activeColor,
                }
            }
            return {
                backgroundColor: '#ffffff',
                color: 'transparent', // Ẩn dot khi chưa chọn
                borderColor: '#666',
            }
        },
    },
    methods: {
        selectOption() {
            if (this.disabled) return
            if (!this.isChecked) {
                this.$emit('update:modelValue', this.value)
                this.$emit('change', this.value)
            }
        },
    },
}
</script>

<style scoped>
.custom-radio {
    display: flex;
    align-items: center;
    cursor: pointer;
    gap: 8px;
    user-select: none;
    margin-left: 0 !important;
}

.custom-radio.disabled {
    cursor: not-allowed;
    opacity: 0.8;
}

.box {
    /* Kích thước 26px bằng với checkbox cũ */
    width: 20px;
    height: 20px;

    /* QUAN TRỌNG: Biến thành hình tròn */
    border-radius: 50%;

    border: 2px solid #ccc;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: all 0.2s;

    /* Font size này để chỉnh kích thước dấu chấm bên trong */
    font-size: 12px;
    font-weight: bold;
}

/* Canh chỉnh dấu chấm nằm chính giữa */
.inner-dot {
    /* Dùng font-size to hơn một chút cho dấu chấm rõ ràng */
    font-size: 18px;
    line-height: 0; /* Loại bỏ height do dòng text gây ra để canh giữa chuẩn */
    padding-bottom: 2px; /* Tinh chỉnh nhỏ để chấm tròn vào đúng tâm */
}

.label {
    /* font-size: 14px; */
}
</style>
