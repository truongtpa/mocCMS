<template>
    <AppModal
        ref="modal"
        :title="state.title"
        :icon="tone.icon"
        :icon-class="tone.iconClass"
        size="sm"
        centered
        :scrollable="false"
        :ok-label="state.okLabel"
        :ok-icon="tone.okIcon"
        :ok-variant="tone.variant"
        :cancel-label="state.cancelLabel"
        @ok="$emit('ok')"
        @cancel="$emit('cancel')"
    >
        <p class="mb-0 app-confirm-message">{{ state.message }}</p>
        <p v-if="state.detail" class="mb-0 mt-2 small text-secondary">{{ state.detail }}</p>
    </AppModal>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import AppModal from './AppModal.vue'

/**
 * AppConfirm — hộp thoại xác nhận, kế thừa YesNoModal cũ.
 *
 * Khác bản cũ: có variant theo mức độ nguy hiểm, nội dung truyền qua tham số
 * của open() thay vì gán trực tiếp vào data của component con.
 *
 *   const ok = await this.$refs.confirm.open({
 *       title: 'Xoá bài viết?',
 *       message: 'Hành động này không thể hoàn tác.',
 *       variant: 'danger',
 *   })
 */
defineEmits(['ok', 'cancel'])

const modal = ref(null)

const state = reactive({
    title: 'Bạn có chắc chắn?',
    message: 'Bạn có muốn thực hiện hành động này không?',
    detail: '',
    variant: 'primary',
    okLabel: 'Đồng ý',
    cancelLabel: 'Hủy',
})

const TONES = {
    primary: { variant: 'primary', icon: 'bi-question-circle-fill', iconClass: 'text-primary', okIcon: 'bi-check-lg' },
    danger: { variant: 'danger', icon: 'bi-exclamation-octagon-fill', iconClass: 'text-danger', okIcon: 'bi-trash3' },
    warning: { variant: 'warning', icon: 'bi-exclamation-triangle-fill', iconClass: 'text-warning', okIcon: 'bi-check-lg' },
    success: { variant: 'success', icon: 'bi-check-circle-fill', iconClass: 'text-success', okIcon: 'bi-check-lg' },
}

const tone = computed(() => TONES[state.variant] || TONES.primary)

/** Trả Promise<boolean> giống YesNoModal cũ */
function open(options = {}) {
    const opts = typeof options === 'string' ? { message: options } : options

    Object.assign(state, {
        title: 'Bạn có chắc chắn?',
        message: 'Bạn có muốn thực hiện hành động này không?',
        detail: '',
        variant: 'primary',
        okLabel: 'Đồng ý',
        cancelLabel: 'Hủy',
        ...opts,
    })

    return modal.value.open()
}

defineExpose({ open })
</script>

<style scoped>
.app-confirm-message {
    white-space: pre-line;
}
</style>
