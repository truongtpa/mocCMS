<template>
  <div class="card">

    <div class="card-header d-flex flex-wrap justify-content-between align-items-center" v-if="title">
      <h3 class="card-title text-bold d-flex">
        {{ title }}
      </h3>

        <div class="d-flex card-button flex-wrap">
            <slot name="button"></slot>
        </div>
    </div>

    <!-- Slot cho card-body -->
    <div class="card-body">
      <slot name="content"></slot>
    </div>

  </div>
</template>

<script>
export default {
  props: {
    title: String,
  }
}
</script>

<style scoped>
.card-header::after {
    content: none !important;
}
.card-button button {
    white-space: wrap !important;
}
</style>
