<template>
    <div class="button-help relative inline-block" ref="wrapper">
        <!-- Slot để truyền nút vào -->
        <div class="button-help" @click="toggleHelp">
            <slot name="trigger">
                <button class="bg-blue-500 text-white px-3 py-1 rounded">❓ Help</button>
            </slot>
        </div>

        <!-- Hộp trợ giúp -->
        <div v-if="showHelp" class="help-data absolute z-10 mt-2 p-3 bg-white text-normal text-md rounded shadow-lg">
            <slot name="content">
                <p class="text-sm text-gray-700">Nội dung trợ giúp mặc định.</p>
            </slot>
        </div>
    </div>
</template>

<script>
export default {
    name: "HelpWrapper",
    data() {
        return {
            showHelp: false,
        };
    },
    methods: {
        toggleHelp() {
            this.showHelp = !this.showHelp;
        },
        handleClickOutside(event) {
            if (this.showHelp && !this.$refs.wrapper.contains(event.target)) {
                this.showHelp = false;
            }
        },
    },
    mounted() {
        document.addEventListener("click", this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener("click", this.handleClickOutside);
    },
};
</script>
<style>
.button-help {
    position: relative !important;
}

.help-data {
    position: absolute;
    top: 50%;
    right: 50%;
    transform: translateX(0%);
    transform: translateY(-50%);
    z-index: 5000000000;
    width: 800px;
    user-select: none;
}

@media (max-width: 768px) {
    .help-data {
        position: fixed;
        top: 20px;
        transform: translateX(-50%);
        left: 50%;
        width: 90%;
        user-select: none;
    }
}
</style>