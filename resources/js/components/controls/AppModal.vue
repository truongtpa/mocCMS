<template>
    <Teleport to="body">
        <div
            ref="el"
            class="modal fade"
            tabindex="-1"
            :aria-labelledby="titleId"
            aria-hidden="true"
        >
            <div class="modal-dialog" :class="dialogClass">
                <div class="modal-content">
                    <div v-if="!hideHeader" class="modal-header">
                        <slot name="header" :close="cancel">
                            <h5 :id="titleId" class="modal-title">
                                <i v-if="icon" :class="['bi', icon, 'me-2', iconClass]" aria-hidden="true"></i>
                                {{ title }}
                            </h5>
                        </slot>
                        <button
                            v-if="closable"
                            type="button"
                            class="btn-close"
                            aria-label="Đóng"
                            :disabled="busy"
                            @click="cancel"
                        ></button>
                    </div>

                    <div class="modal-body" :class="bodyClass">
                        <slot :close="cancel" />
                    </div>

                    <div v-if="!hideFooter" class="modal-footer">
                        <slot name="footer" :ok="confirm" :cancel="cancel" :busy="busy">
                            <slot name="actions" />

                            <button
                                v-if="showCancel"
                                type="button"
                                class="btn btn-secondary"
                                :disabled="busy"
                                @click="cancel"
                            >
                                {{ cancelLabel }}
                            </button>

                            <button
                                v-if="showOk"
                                type="button"
                                class="btn"
                                :class="`btn-${okVariant}`"
                                :disabled="busy || okDisabled"
                                @click="confirm"
                            >
                                <span
                                    v-if="busy"
                                    class="spinner-border spinner-border-sm me-1"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                <i v-else-if="okIcon" :class="['bi', okIcon, 'me-1']" aria-hidden="true"></i>
                                {{ okLabel }}
                            </button>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Modal } from 'bootstrap'

/**
 * AppModal — modal dùng chung.
 *
 * So với LTEModal/LTEModal2 cũ:
 *  - title/nhãn nút là props, cha không còn phải gán thẳng vào data của con.
 *  - Teleport ra body nên không bị vỡ khi modal nằm trong card/table có overflow.
 *  - Markup Bootstrap 5 (btn-close) thay cho .close/data-dismiss của BS4.
 *  - dispose() khi unmount, tránh rò rỉ instance như bản cũ.
 *  - Hỗ trợ cả v-model lẫn API promise open()/close() để dùng như code cũ.
 */
const props = defineProps({
    /** Dùng được v-model:show, hoặc bỏ qua và gọi open() */
    show: { type: Boolean, default: undefined },

    title: { type: String, default: '' },
    icon: { type: String, default: '' },
    iconClass: { type: String, default: '' },

    size: { type: String, default: 'lg', validator: (v) => ['sm', 'md', 'lg', 'xl', 'fullscreen'].includes(v) },
    centered: { type: Boolean, default: false },
    scrollable: { type: Boolean, default: true },

    okLabel: { type: String, default: 'Lưu thông tin' },
    okIcon: { type: String, default: 'bi-check-lg' },
    okVariant: { type: String, default: 'primary' },
    okDisabled: { type: Boolean, default: false },
    cancelLabel: { type: String, default: 'Hủy' },

    showOk: { type: Boolean, default: true },
    showCancel: { type: Boolean, default: true },
    hideHeader: { type: Boolean, default: false },
    hideFooter: { type: Boolean, default: false },

    /**
     * false = không tự đóng sau khi bấm OK (để cha gọi API rồi tự close()).
     * Dùng kèm setBusy(true/false) để khoá nút trong lúc lưu.
     */
    autoClose: { type: Boolean, default: true },

    /** Cho phép đóng bằng nút X / ESC / click nền */
    closable: { type: Boolean, default: true },
    /** true = không đóng khi click nền hoặc ESC */
    persistent: { type: Boolean, default: true },

    bodyClass: { type: String, default: '' },

    /**
     * Guard trước khi đóng/xác nhận.
     * Trả về false (hoặc Promise<false>) để chặn. Nhận 'ok' | 'cancel'.
     */
    beforeClose: { type: Function, default: null },
})

const emit = defineEmits(['update:show', 'ok', 'cancel', 'shown', 'hidden'])

const el = ref(null)
const busy = ref(false)
const titleId = `app-modal-title-${Math.random().toString(36).slice(2, 9)}`

let instance = null
let resolver = null
let lastFocused = null

const dialogClass = computed(() => [
    props.size === 'fullscreen' ? 'modal-fullscreen' : `modal-${props.size}`,
    { 'modal-dialog-centered': props.centered, 'modal-dialog-scrollable': props.scrollable },
])

/* ------------------------------------------------------------------ */
/* Vòng đời                                                            */
/* ------------------------------------------------------------------ */
onMounted(() => {
    instance = new Modal(el.value, {
        backdrop: props.persistent ? 'static' : true,
        keyboard: !props.persistent && props.closable,
    })

    el.value.addEventListener('shown.bs.modal', onShown)
    el.value.addEventListener('hidden.bs.modal', onHidden)

    if (props.show) instance.show()
})

onBeforeUnmount(() => {
    el.value?.removeEventListener('shown.bs.modal', onShown)
    el.value?.removeEventListener('hidden.bs.modal', onHidden)

    // Bản cũ không dispose -> instance và backdrop bị treo lại trong DOM
    instance?.dispose()
    instance = null

    settle(false)
})

function onShown() {
    emit('shown')
    // Đưa focus vào phần tử nhập liệu đầu tiên cho thao tác bàn phím
    el.value
        ?.querySelector('[autofocus], .modal-body input:not([type=hidden]), .modal-body select, .modal-body textarea')
        ?.focus()
}

function onHidden() {
    busy.value = false
    emit('update:show', false)
    emit('hidden')
    settle(false)
    lastFocused?.focus?.()
    lastFocused = null
}

/* ------------------------------------------------------------------ */
/* Đồng bộ v-model                                                     */
/* ------------------------------------------------------------------ */
watch(
    () => props.show,
    (v) => {
        if (v === undefined || !instance) return
        v ? instance.show() : instance.hide()
    },
)

/* ------------------------------------------------------------------ */
/* API                                                                 */
/* ------------------------------------------------------------------ */
function settle(value) {
    if (!resolver) return
    const r = resolver
    resolver = null
    r(value)
}

/** Mở modal. Trả Promise<boolean>: true nếu bấm OK, false nếu huỷ/đóng. */
function open() {
    lastFocused = document.activeElement
    emit('update:show', true)
    instance?.show()

    return new Promise((resolve) => {
        settle(false) // huỷ promise cũ nếu còn treo
        resolver = resolve
    })
}

function close(result = false) {
    settle(result)
    instance?.hide()
}

async function runGuard(action) {
    if (typeof props.beforeClose !== 'function') return true

    busy.value = true
    try {
        return (await props.beforeClose(action)) !== false
    } finally {
        busy.value = false
    }
}

async function confirm() {
    if (busy.value) return
    if (!(await runGuard('ok'))) return

    emit('ok')
    settle(true)

    // autoClose=false khi cha muốn tự đóng sau khi lưu xong
    if (props.autoClose !== false) instance?.hide()
}

async function cancel() {
    if (busy.value || !props.closable) return
    if (!(await runGuard('cancel'))) return

    emit('cancel')
    close(false)
}

/** Cho cha bật/tắt trạng thái đang xử lý (khoá nút, hiện spinner) */
function setBusy(v) {
    busy.value = !!v
}

defineExpose({ open, close, setBusy, confirm, cancel })
</script>

<style scoped>
.modal-header {
    align-items: center;
}

.modal-title {
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.modal-footer {
    gap: 0.5rem;
}

/* Bootstrap tự thêm margin cho con của modal-footer, đã có gap nên bỏ đi */
.modal-footer > :deep(*) {
    margin: 0;
}
</style>
