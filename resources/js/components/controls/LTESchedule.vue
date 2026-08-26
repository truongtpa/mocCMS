<template>
    <div class="table-responsive table-wrapper table-bordered" ref="scrollContainer">
        <table>
            <thead>
            <tr class="text-center">
                <th class="sticky-column"></th>
                <th
                    v-for="timeSlot in timeSlots"
                    :key="timeSlot"
                    class=""
                >
                    {{ timeSlot }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(dailyEvent, index) in weeklyEvents" :key="index">
                <td class=" sticky-column p-2 text-center" v-html="dailyEvent.weekDay + '<br>' + dailyEvent.date"></td>
                <td colspan="24" style="position: relative;" :ref="dailyEvent.weekDay === 'Thứ 2' ? 'dayCell' : null">
                <template v-for="event in dailyEvent.events">
                    <div style="height: 5.8rem; position: relative; overflow: hidden">
                        <div :style="getEventStyle(event)" class="event-badge" @click="$emit('event-clicked', event)">
                            <div class="event-title"> {{ event.title }}</div>
                            <div class="event-time">{{ formatToHHMM(event.start) + ' - ' + formatToHHMM(event.end)}}</div>
                            <div class="event-content"> {{ event.extendedProps.nguoi_chu_tri.ho_ten }}</div>
                        </div>
                    </div>
                </template>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { defineProps, computed, ref, onMounted, onBeforeUnmount, defineExpose } from 'vue'

const dayCell = ref(null)
const dayCellWidth = ref(0)
const scrollContainer = ref(null)
defineExpose({scrollContainer})
let observer

onMounted(() => {
    if (dayCell.value) {
        const firstRow = dayCell.value[0]
        observer = new ResizeObserver(entries => {
            dayCellWidth.value = entries[0].contentRect.width
        })
        observer.observe(firstRow)
    }
}
)

onBeforeUnmount(() => {
    observer?.disconnect()
})

// Accept the day name as a prop
const props = defineProps({
    weeklyEvents: {
        type: Array,
        default: []
    },
})


// Generate time slots: 00:00 to 24:00 (1-hour intervals)
const timeSlots = Array.from({ length: 24 }, (_, i) =>
    (i).toString().padStart(2, '0')
)

const formatToHHMM = (input) => {
    const d = new Date(input);
    const hours = d.getHours().toString().padStart(2, '0');
    const minutes = d.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
}
const getEventStyle = (event) => {
    const start = new Date(event.start)
    const end = new Date(event.end)
    const startMin = start.getHours() * 60 + start.getMinutes()
    const endMin = end.getHours() * 60 + end.getMinutes()
    let duration = (endMin - startMin) // in minutes
    const pxPerMinute = dayCellWidth.value / (24 * 60)
    const leftPos = startMin * pxPerMinute
    const width = duration * pxPerMinute
    // if (width < 10)
    //     return {
    //         left: `${leftPos}px`,
    //         width: `${width}px`,
    //         padding: '0 !important'
    //     }
    return {
        left: `${leftPos}px`,
        width: `${width}px`
    }
}

</script>


<style scoped>
table {
    width: 100%;
}
.table-wrapper {
    overflow-x: auto;
}
thead {
    background-color: var(--primary) !important;
    color: white;
}

thead tr th {
    padding: 10px 0;
}
tbody tr td:first-child{
    width: 100px;
}
tbody tr td,th {
    position: relative;
    padding: 0;
}
.event-badge {
    position: absolute;
    top: 5%;
    height: 90%;
    background-color: lightgrey;
    border-radius: 4px;
    word-wrap: unset !important;
    padding: 8px;
    white-space: nowrap;
    cursor: pointer;
}
.event-badge div {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.event-badge:hover {
    background-color: darkgrey;
}
</style>

