<template>
    <div class="day-block">
        <p v-if="showTitle"><b>{{ groups[0]?.content }}</b></p>
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
        day: {
            type: String,
            required: true,
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
            timeline: null,
        };
    },
    mounted() {
        const container = document.getElementById('timeline-' + this.timelineId);
        this.timeline = new Timeline(container);

        const dayStart = `${this.day} 00:00:00`;
        const dayEnd = `${this.day} 23:59:59`;

        const defaultOptions = {
            start: dayStart,
            end: dayEnd,
            minHeight: '100px',
            orientation: 'top',
            stack: true,
            zoomMin: 1000 * 60 * 60 * 24,
            zoomMax: 1000 * 60 * 60 * 24,
            moveable: false,
            zoomable: false,
            horizontalScroll: false,
            verticalScroll: true,
            timeAxis:  { scale: 'hour', step: 1 },
            editable: false,
            showCurrentTime: true,
            showMajorLabels: false,
            showMinorLabels: true,
            format: {
                minorLabels: {
                    minute: 'HH',
                    hour: 'HH',
                    day: 'dddd D/M',
                },
                majorLabels: {
                    day: 'MMMM YYYY',
                },
            },
            margin: {
                axis: 10,
                item: {
                    vertical: 10
                }
            }
        };

        const options = {
            ...defaultOptions,
            ...this.timelineOptions,
        };

        this.timeline.setOptions(options);

        this.timeline.on('select', (props) => {
            if (props.items.length > 0) {
                const selectedItemId = props.items[0];
                const selectedItem = this.items.find(item => item.id === selectedItemId);
                this.$emit('item-click', selectedItem);
            }
        });

        this.timeline.setGroups(this.groups);
        this.timeline.setItems(this.items);
    },
};
</script>

<style>

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


</style>
