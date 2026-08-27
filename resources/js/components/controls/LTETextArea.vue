<template>
  <div class="form-group">
    <label>{{ label }}</label>
    <textarea :placeholder="placeholder" :class="textareaClass" :style="textareaStyle" :id="textareaId"
      :disabled="isDisabled" :value="modelValue" :rows="rows"
      @input="$emit('update:modelValue', $event.target.value)"></textarea>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: 'Chưa đặt tên',
    },
    placeholder: {
      type: String,
      default: 'Chưa đặt tên',
    },
    textareaClass: {
      type: String,
      default: 'form-control',
    },
    textareaStyle: {
      type: String,
      default: '',
    },
    textareaId: {
      type: String,
      default: '',
    },
    modelValue: {
      type: String,
      default: '',
    },
    rows: {
      type: Number,
      default: 2,
    },
    isDisabled: {
      type: Boolean,
      default: false,
    },
  },
};
</script>
