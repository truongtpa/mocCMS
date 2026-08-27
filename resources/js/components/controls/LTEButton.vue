<template>
    <button
        class="btn btn-primary btn text-nowrap"
        :class="extraClasses"
        :style="[isDisabled ? 'pointer-events: none; opacity: 0.5;' : '', { fontSize: textSize }]"
        @click="handleAction"
    >
        <slot></slot>
        <i v-if="icon" :class="icon"></i>{{ icon && text ? '&nbsp;&nbsp;' : '' }}{{ text }}
    </button>
</template>

<script>
export default {
    name: 'IconTextButton',
    props: {
        icon: {
            type: String,
            required: false,
        },
        text: {
            type: String,
            required: false,
            default: '',
        },
        textSize: {
            type: String,
            required: false,
            default: '',
        },
        action: {
            type: Function,
            required: true,
        },
        class: {
            type: [String, Array, Object],
            required: false,
        },
        isDisabled: {
            type: [Boolean, Number],
            required: false,
            default: false,
        },
    },
    data() {
        return {
            windowWidth: window.innerWidth,
        }
    },
    computed: {
        extraClasses() {
            const classes = []

            if (this.class) {
                classes.push(this.class)
            }

            if (this.windowWidth < 768) {
                classes.push('btn-sm')
            }

            return classes
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
            if (this.action) this.action(event)
        },
        updateWindowWidth() {
            this.windowWidth = window.innerWidth
        },
    },
    mounted() {
        window.addEventListener('resize', this.updateWindowWidth)
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.updateWindowWidth)
    },
}
</script>

<style scoped>
.btn-custom {
    font-size: var(--font-size);
    background-color: var(--primary-color);
    color: white;
}
</style>
