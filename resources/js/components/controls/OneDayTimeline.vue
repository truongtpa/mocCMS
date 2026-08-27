<template>
    <div class="day-block">
        <p v-if="showTitle"><b>{{ groups[0].content }}</b></p>
        <div :id="'timeline-' + timelineId"></div>
    </div>
</template>

<script>
import { Timeline } from 'vis-timeline/standalone';
import 'vis-timeline/styles/vis-timeline-graph2d.min.css';

export default {
    name: 'OneDayTimeline',
    props: {
        items: {
            type: Array,
            default: () => [],
        },
        groups: {
            type: Array,
            default: () => [],
        },
        timelineId: {
            type: String,
            required: true,
        },
        showTitle: {
            type: Boolean,
            default: false,
        },
        timelineOptions: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            title: '',
        };
    },
    mounted() {
        console.log(this.timelineId);
        const container = document.getElementById('timeline-' + this.timelineId);
        const timeline = new Timeline(container);
        const dayStart = '2025-05-19 06:00:00';
        const dayEnd = '2025-05-19 18:00:00';

        // Các tùy chọn mặc định
        const defaultOptions = {
            start: dayStart,
            end: dayEnd,
            minHeight: '300px',
            orientation: 'top',
            stack: true,
            zoomMin: 1000 * 60 * 60 * 12,
            zoomMax: 1000 * 60 * 60 * 12,
            moveable: false,
            zoomable: false,
            horizontalScroll: false,
            verticalScroll: true,
            timeAxis: { scale: 'hour', step: 1 },
            editable: false,
            showCurrentTime: false,
        };

        // Hợp nhất tùy chọn mặc định với timelineOptions từ props
        const options = {
            ...defaultOptions,
            ...this.timelineOptions,
        };

        timeline.setOptions(options);

        timeline.on('select', (props) => {
            if (props.items.length > 0) {
                const selectedItemId = props.items[0];
                const selectedItem = this.items.find((item) => item.id === selectedItemId);
                this.$emit('item-click', selectedItem);
            }
        });

        timeline.setGroups(this.groups);
        timeline.setItems(this.items);
    },
};
</script>

<style>
.day-block {
    margin-bottom: 40px;
}

.timeline-wrap-text {
    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: break-word !important;
    display: block !important;
    line-height: 1.4;
}

.vis-timeline {
    overflow-x: auto;
}

.vis-labelset .vis-label {
    width: 60px !important;
    min-width: 60px !important;
    text-align: left;
    padding: 5px;
    box-sizing: border-box;
    font-weight: bold;
}

.vis-timeline .vis-panel.vis-top {
    background-color: #ffffff !important;
    color: white !important;
    font-size: 16px !important;
    text-align: center !important;
    font-weight: bold;
}

div.vis-panel.vis-top > div > div.vis-text.vis-major.vis-h21.vis-odd > div {
    display: none;
}
</style>
