<template>
  <div class="modal fade" tabindex="-1" aria-hidden="true" ref="modal" >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-bold">{{ title }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body" :class="noPadding ? 'no-padding' : ''">
          <slot></slot>
        </div>
        <div class="modal-footer" v-if="$slots.buttons">
            <slot name="buttons"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    props: {
        noPadding: {
            type: Boolean,
            default: false
        },
        isFooter: {
            type: Boolean,
            default: false
        }
    },
  data(){
    return {
      title : 'Không tiêu đề',
      close: 'Đóng'
    }
  },
  mounted() {
    const modalElement = this.$refs.modal;
    this.modalInstance = new window.bootstrap.Modal(modalElement, {
      backdrop: 'static',
      keyboard: false
    });
  },
  methods: {
    openModal() {
      this.modalInstance.show();
    },
    closeModal() {
      this.modalInstance.hide();
    }
  }
}
</script>

<style scoped>
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.no-padding {
    padding: 0px !important;
}
</style>
