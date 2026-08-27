<template>
  <div class="form-group" v-if="visible">
    <label>{{ label }}</label>
    <div style="min-height: 10px;">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: 'Chưa đặt tên',
    },
    visible: {
      type: Boolean,
      default: true,
    },
  },
}
</script>
