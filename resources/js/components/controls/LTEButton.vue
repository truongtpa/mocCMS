<template>
    <button
        type="button"
        class="btn text-nowrap"
        :class="[
            variant ? `btn-${variant}` : 'btn-primary',
            extraClasses
        ]"
        :disabled="isDisabled"
        :style="[isDisabled ? 'pointer-events: none; opacity: 0.5;' : '', { fontSize: textSize }]"
        @click="handleAction"
    >
        <i v-if="icon" :class="[icon, text || $slots.default ? 'mr-1.5' : '']"></i>
        <slot>{{ text }}</slot>
    </button>
</template>

<script>
export default {
    name: 'LTEButton',
    emits: ['click'],
    props: {
        variant: {
            type: String,
            default: 'primary',
        },
        icon: {
            type: String,
            default: '',
        },
        text: {
            type: String,
            default: '',
        },
        textSize: {
            type: String,
            default: '',
        },
        action: {
            type: Function,
            default: null,
        },
        class: {
            type: [String, Array, Object],
            default: '',
        },
        isDisabled: {
            type: [Boolean, Number],
            default: false,
        },
    },
    computed: {
        extraClasses() {
            return this.class ? this.class : ''
        },
    },
    methods: {
        handleAction(event) {
            if (this.isDisabled) {
                if (event) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                return
            }
            this.$emit('click', event)
            if (this.action) this.action(event)
        },
    },
}
</script>
