<template>
  <LTESelect2Option
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :label="label"
    :label-class="labelClass"
    :placeholder="placeholder"
    :data="normalizedOptions"
    :multiple="multiple"
    :close-on-select="true"
    :allow-clear="allowClear"
    :class="class"
    :style="style"
    :is-disabled="isDisabled"
    :enable-data-watch="true"
  />
</template>

<script>
import LTESelect2Option from './LTESelect2Option.vue';

export default {
  name: 'LTESelectOption',
  components: { LTESelect2Option },
  props: {
    label: { type: String, default: '' },
    labelClass: { type: String, default: 'font-weight-bold small text-muted text-xs mb-1' },
    placeholder: { type: String, default: 'Chọn một tùy chọn' },
    modelValue: { type: [String, Number, Array], default: '' },
    options: { type: Array, default: () => [] },
    data: { type: Array, default: () => [] },
    multiple: { type: Boolean, default: false },
    allowClear: { type: Boolean, default: true },
    isDisabled: { type: Boolean, default: false },
    class: { type: String, default: '' },
    style: { type: [String, Object], default: '' },
  },
  computed: {
    normalizedOptions() {
      const src = (this.options && this.options.length) ? this.options : (this.data || []);
      return src.map(opt => {
        if (typeof opt === 'object' && opt !== null) {
          return {
            value: opt.value !== undefined ? opt.value : (opt.id !== undefined ? opt.id : opt.text),
            text: opt.text !== undefined ? opt.text : (opt.label !== undefined ? opt.label : (opt.name || opt.value))
          };
        }
        return { value: opt, text: opt };
      });
    }
  }
}
</script>
