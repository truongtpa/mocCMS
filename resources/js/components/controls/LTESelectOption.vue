<template>
  <div class="form-group">
    <label>{{ label }}</label>
    <select
        :class="selectClass"
        :id="selectId"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
      <option disabled value="">{{ placeholder }}</option>
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.text }}
      </option>
    </select>
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
      default: 'Chọn một tùy chọn',
    },
    selectClass: {
      type: String,
      default: 'form-control',
    },
    selectId: {
      type: String,
      default: '',
    },
    modelValue: {
      type: [String, Number],
      default: '',
    },
    options: {
      type: Array,
      default: () => [],
    },

  isDisabled: {
      type: Boolean,
      default: false,
  }
  },
};
</script>
