<template>
  <div :class="[hasLabel ? 'form-group mb-2' : 'm-0 p-0', extraClasses]">
    <label v-if="hasLabel" :class="labelClass">{{ label }}</label>
    <textarea 
      :placeholder="placeholder" 
      :class="textareaClass" 
      :style="textareaStyle" 
      :id="textareaId"
      :disabled="isDisabled" 
      :value="modelValue" 
      :rows="rows"
      @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: '',
    },
    labelClass: {
      type: String,
      default: 'font-weight-bold small text-muted text-xs mb-1',
    },
    placeholder: {
      type: String,
      default: '',
    },
    textareaClass: {
      type: String,
      default: 'form-control form-control-sm text-xs font-mono',
    },
    textareaStyle: {
      type: String,
      default: '',
    },
    textareaId: {
      type: String,
      default: '',
    },
    class: {
      type: String,
      default: '',
    },
    modelValue: {
      type: String,
      default: '',
    },
    rows: {
      type: Number,
      default: 3,
    },
    isDisabled: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    hasLabel() {
      return this.label && String(this.label).trim() !== '';
    },
    extraClasses() {
      return this.class || '';
    }
  }
};
</script>
