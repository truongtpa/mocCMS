<template>
  <div class="card">
    <div class="card-header row">
<h3 class="text-bold col-6 d-flex align-items-center mb-0">
  <slot name="title">
    {{ title }}
  </slot>
</h3>

      <div class="d-flex col-6 card-button justify-content-end">
        <slot name="button"></slot>
      </div>
    </div>

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
</style>
