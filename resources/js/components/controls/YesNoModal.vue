<template>
  <div class="modal fade" tabindex="-1" aria-hidden="true" ref="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
          <button type="button" class="close" aria-label="Close" @click="handleClose">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          {{ message }}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger float-left" @click="confirm(false, $event)">
            <i class="fas fa-times"></i>
            {{ noLabel }}
          </button>
          <button type="button" class="btn btn-success float-right" @click="confirm(true, $event)">
            <i class="fas fa-check"></i>
            {{ yesLabel }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    yesLabel: {
      type: String,
      default: 'Đồng ý'
    },
    noLabel: {
      type: String,
      default: 'Hủy'
    }
  },
  mounted() {
    const modalElement = this.$refs.modal;
    this.modalInstance = new window.bootstrap.Modal(modalElement, {
      backdrop: 'static',
      keyboard: false
    });
  },
  data() {
    return {
      title: '',
      message: ''
    };
  },
  methods: {
    openModal(title='Bạn có chắc chắn?', message='Bạn có muốn thực hiện hành động này không?') {
      this.title = title;
      this.message = message;

      this.modalInstance = new window.bootstrap.Modal(this.$refs.modal, {
        backdrop: 'static',
        keyboard: false
      });

      this.modalInstance.show();
      return new Promise((resolve) => {
        this.resolveModal = resolve;
      });
    },
    closeModal() {
      this.modalInstance.hide();
    },
    confirm(result, event) {
      if (event && event.currentTarget) {
        event.currentTarget.blur();
      }
      if (this.resolveModal) {
        this.resolveModal(result);
      }
      this.closeModal();
    },
    handleClose() {
      this.confirm(false);
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
.modal {
    z-index: 2000 !important;
}
.modal-backdrop {
    z-index: 1990 !important;
}

</style>
