<template>
    <Teleport to="body">
        <div class="modal fade" tabindex="-1" aria-hidden="true" ref="modal" style="overflow-y: auto;">
            <div class="modal-dialog" :class="modalSizeClass">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-bold">{{ title }}</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                            @click="handleClose(false)"
                        >
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                    <div class="modal-footer" v-if="showSaveButton">
                        <slot name="action"></slot>
                        <LTEButton 
                            variant="primary" 
                            icon="far fa-save" 
                            :text="save" 
                            class="btn-sm text-xs font-weight-bold px-3 shadow-sm" 
                            @click="confirm(true)" 
                        />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script>
import LTEButton from './LTEButton.vue';

export default {
    components: { LTEButton },
    data() {
        return {
            title: 'Không tiêu đề',
            save: 'Lưu thông tin',
        }
    },
    emits: ['save', 'close'],
    computed: {
        modalSizeClass() {
            return {
                'modal-sm': this.size === 'sm',
                'modal-md': this.size === 'md',
                'modal-lg': this.size === 'lg',
                'modal-xl': this.size === 'xl',
            }
        },
    },
    mounted() {
        const modalElement = this.$refs.modal
        this.modalInstance = new window.bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false,
        })
    },
    props: {
        showSaveButton: {
            type: Boolean,
            default: true,
        },
        size: {
            type: String,
            default: 'lg', // mặc định là modal-lg
            validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
        },
    },
    methods: {
        openModal() {
            this.modalInstance.show()
            return new Promise((resolve) => {
                this.resolveModal = resolve
            })
        },
        closeModal() {
            this.modalInstance.hide()
        },
        confirm(result) {
            

            if (this.resolveModal) {
                this.resolveModal(result)
            }

            // this.closeModal();
        },
        handleClose() {
            this.$emit('close')
            this.confirm(false)
        },
    },
}
</script>

<style scoped>
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-body {
    min-height: 200px; /* Đảm bảo modal không bị quá ngắn */
}
</style>

<style>
/* Đảm bảo khóa scroll trang chính và cho phép modal tự scroll */
body.modal-open {
    overflow: hidden !important;
    height: 100vh;
}

.modal {
    background: rgba(0, 0, 0, 0.5);
}
</style>
