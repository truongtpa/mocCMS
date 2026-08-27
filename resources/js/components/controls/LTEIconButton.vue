<template>
    <span class="d-flex align-items-center justify-content-center p-2 position-relative icon-button"
        :class="extraClasses"
        :style="[extraStyle, isDisabled ? 'pointer-events: none; opacity: 0.5;' : '', { height: `calc(36px * ${size}`, width: `calc(36px * ${size}` }]"
        @click="handleAction">
        <i :class="icon" :style="{
            fontSize: `calc(20px * ${size}`,
        }"></i>
        <span v-if="isBadge" class="position-absolute badge badge-success badge-label">{{ badge }}</span>
    </span>
</template>

<script>
export default {
    name: "LTEIconButton",
    props: {
        icon: {
            type: String,
            required: true,
            default: "fa-solid fa-filter-list",
        },
        badge: {
            type: [Number, String],
            required: false,
            default: 0,
        },
        isBadge: {
            type: Boolean,
            required: false,
            default: false,
        },
        action: {
            type: Function,
            required: false,
        },
        class: {
            type: [String, Array, Object],
            required: false,
        },
        style: {
            type: [String, Array, Object],
            required: false,
        },
        isDisabled: {
            type: [Boolean, Number],
            required: false,
            default: false
        },
        size: {
            type: [Number],
            required: false,
            default: 1
        },
    },
    computed: {
        extraClasses() {
            return this.class;
        },
        extraStyle() {
            return this.style;
        },
    },
    methods: {
        handleAction(event) {
            if (this.isDisabled) {
                event.preventDefault();
                event.stopPropagation();
                return;
            }
            if (this.action) this.action(event);
        }
    },
};
</script>
<style>
.icon-button {
    border-radius: 4px;
    cursor: pointer;
}

.icon-button:hover {
    background-color: var(--vlute-btn-additive-background) !important;
}

.badge-label {
    top: 0;
    right: -2px;
}
</style>
