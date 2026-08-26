<template>
  <div class="card">

    <div class="card-header" v-if="title">
      <h3 class="card-title text-bold">
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
  props: {
    title: String,
  }
}
</script>

<style scoped>
</style>