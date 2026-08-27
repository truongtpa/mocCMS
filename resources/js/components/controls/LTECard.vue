<template>
  <div class="card">
    <div class="card-header d-flex align-items-center" v-if="title || showBackButton || breadcrumbs || LTEBreadcrumb">
      <div v-if="showBackButton" class="btn-back pr-3 pl-1" @click="$router.back()">
        <i class="fas fa-arrow-left"></i>
      </div>

      <div class="bread-crumbs" v-if="$slots.breadCrumbs">
        <h3 class="card-title text-bold mb-0">
          <slot name="breadCrumbs"></slot>
        </h3>
      </div>

      <h3 class="card-title text-bold mb-0">
        {{ title }}
      </h3>

    </div>

    <!-- Slot cho card-body -->
    <div class="card-body">
      <slot name="content"></slot>
    </div>
  </div>
</template>

<script>

export default {
  methods: { },
  components: {},
  props: {
    title: String,
    showBackButton: Boolean,
    breadcrumbs: Boolean,
    LTEBreadcrumb: Boolean,
  },
}
</script>

<style scoped>
.btn-back {
  cursor: pointer;
}
</style>
