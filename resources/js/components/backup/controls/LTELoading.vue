<template>
    <div class="d-flex align-items-center">
        <div class="loader" :style="{
            width: `calc(100px * ${this.size})`,
            height: `calc(100px * ${this.size})`
        }">
            <div class="outer" :style="{
                borderTopWidth: `calc(4px * ${this.size})`,
                borderRightWidth: `calc(4px * ${this.size})`,
            }"></div>
            <div class="inner" :style="{
                borderBottomWidth: `calc(4px * ${this.size})`,
                borderLeftWidth: `calc(4px * ${this.size})`,
            }"></div>
        </div>
        <div class="ml-2 router-link-none" :style="{
            fontSize: `calc(28px * ${this.size})`,
        }">{{ this.text }}</div>
    </div>
</template>

<script>
export default {
    name: "LTELoader",
    props: {
        size: {
            type: Number,
            required: false,
            default: 0.6,
        },
        text: {
            type: String,
            required: false,
            default: 'Đang tải',
        }
    },
    computed: {
        extrasSize() {
            return this.size;
        },
    },
};
</script>

<style scoped>
.loader {
    /* width: 50px;
    height: 50px; */
    position: relative;
    /* margin: 50px auto; */
}

.outer,
.inner {
    position: absolute;
    border-radius: 50%;
    animation-duration: 2s;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
}

.outer {
    width: 100%;
    height: 100%;
    border-top: 2px solid #3498db;
    border-right: 2px solid #3498db;
    animation-name: spin-outer;
}

.inner {
    width: 60%;
    height: 60%;
    border-bottom: 2px solid #e74c3c;
    border-left: 2px solid #e74c3c;
    top: 20%;
    left: 20%;
    animation-name: spin-inner;
}

@keyframes spin-outer {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

@keyframes spin-inner {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(-360deg);
    }
}
</style>