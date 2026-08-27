<template>
    <div class="form-group  flex-nowrap">
        <label v-if="label">{{ label }}</label>

        <div class="input-group">
            <div v-if="start" class="input-group-prepend">
                <button class="btn btn-default" :style="{ cursor: 'default' }" v-html="start"></button>
            </div>
            <input ref="flatpickrInput" type="text" class="form-control" :class="selectClass" :id="selectId" />
            <div class="input-group-append">
                <button class="btn btn-default" @click="openFlatpickr">
                    <i class="fas fa-calendar-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { DateTime } from "luxon";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Vietnamese } from "flatpickr/dist/l10n/vn.js";


const props = defineProps({
    label: String,
    selectClass: {
        type: String,
        default: "form-control",
    },
    selectId: String,
    modelValue: {
        type: String,
        default: "",
    },
    start: String,
});

const emit = defineEmits(["update:modelValue"]);
const flatpickrInput = ref(null);
let picker = null;


function formatDateForDisplay(isoDate) {
    return isoDate ? DateTime.fromISO(isoDate).toFormat("dd/MM/yyyy") : "";
}


function parseDateFromDisplay(dateStr) {
    const dt = DateTime.fromFormat(dateStr, "dd/MM/yyyy");
    return dt.isValid ? dt.toISODate() : null;
}


onMounted(() => {
    const customVietnamese = {
        ...Vietnamese,
        months: {
            shorthand: Array.from({ length: 12 }, (_, i) => `T ${i + 1}`),
            longhand: Array.from({ length: 12 }, (_, i) => `Tháng ${i + 1}`),
        },
    };

    picker = flatpickr(flatpickrInput.value, {
        locale: customVietnamese,
        dateFormat: "d/m/Y",
        allowInput: true,
        defaultDate: props.modelValue && DateTime.fromISO(props.modelValue).isValid
            ? DateTime.fromISO(props.modelValue).toJSDate()
            : null,
        onChange: ([selectedDate]) => {
            const iso = DateTime.fromJSDate(selectedDate).toFormat("yyyy-MM-dd");
            emit("update:modelValue", iso);
        },
        onClose: () => {
            const parsed = parseDateFromDisplay(flatpickrInput.value.value);
            if (parsed) emit("update:modelValue", parsed);
            else flatpickrInput.value.value = formatDateForDisplay(props.modelValue);
        },
    });

});


watch(
    () => props.modelValue,
    (newVal) => {
        if (picker && newVal) {
            picker.setDate(DateTime.fromISO(newVal).toJSDate(), false);
        }
    }
);


function getVietnamTime() {
    return DateTime.now().setZone("Asia/Ho_Chi_Minh").toFormat("yyyy-MM-dd");
}

function openFlatpickr() {
    if (picker) picker.open();
}
</script>
<style>
.flatpickr-current-month {
    font-size: 1.1rem !important;
}

.flatpickr-monthDropdown-months {
    font-size: 1.1rem !important;
}

.flatpickr-months .numInput {
    font-size: 1.1rem !important;
    height: 28px !important;
}
</style>