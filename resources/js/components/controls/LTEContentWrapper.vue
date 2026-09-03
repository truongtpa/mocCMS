<template>
    <div class="content-wrapper">
        <!-- Slot cho content-header -->
        <div class="content-header py-2 px-3" v-if="header && title">
            <div class="container-fluid p-0">
                <div class="row mb-0">
                    <div class="col-sm-6">
                        <h1 style="font-size: 20px" class="text-primary font-weight-bold m-0">
                            {{ title }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slot cho content -->
        <section class="content p-3">
            <div class="container-fluid p-0">
                <slot name="content"></slot>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        header: {
            type: Boolean,
            default: true,
        },
    },
}
</script>

<style scoped>
.content-wrapper {
    min-height: calc(100vh - 57px) !important;
}

@media (max-width: 768px) {
    .content {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: 0.5rem !important;
    }
    .content-wrapper {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .container-fluid {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .col-12, [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
}
</style>
