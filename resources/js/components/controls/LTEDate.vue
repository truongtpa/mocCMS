<template>
    <div class="form-group">
        <label v-if="label">{{ label }}</label>
        <div class="input-group"
             ref="pickerWrapper"
             data-td-target-toggle="nearest">
            <input
                type="text"
                class="form-control"
                :value="modelValue"
                :placeholder="placeholder"
                @change="$emit('update:modelValue', $event.target.value)"
            />
            <span class="input-group-text"
                  data-td-target="#datetimepicker1"
                  data-td-toggle="datetimepicker"
                  id="datetimepicker1">
    <i class="fa fa-calendar"></i>
  </span>
        </div>
    </div>

</template>

<script>
import { TempusDominus } from '@eonasdan/tempus-dominus'
import '@eonasdan/tempus-dominus/dist/css/tempus-dominus.min.css'
import moment from 'moment'
import 'moment/locale/vi'

export default {
    name: 'LTEDate',
    props: {
        label: {
            type: String,
            default: '',
        },
        modelValue: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: 'dd/MM/yyyy',
        },
        format: {
            type: String,
            default: 'dd/MM/yyyy',
        }
    },
    mounted() {
        const picker = new TempusDominus(this.$refs.pickerWrapper, {
            allowInputToggle: false,
            display: {
                viewMode: 'calendar',
                theme: 'light',
                buttons: {
                    today: true,
                    clear: true,
                    close: false
                },
                components: {
                    calendar: true,
                    date: true,
                    month: true,
                    year: true,
                    decades: false,
                    clock: false,
                },
                icons: {
                    type: 'icons',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-calendar-check',
                    clear: 'fa fa-trash',
                    close: 'fa fa-times',
                    time: 'fa fa-clock',
                    date: 'fa fa-calendar',
                    up: 'fa fa-arrow-up',
                    down: 'fa fa-arrow-down',
                },
            },
            localization: {
                locale: 'vi',
                today: 'Ngày hôm nay',
                clear: 'Xóa lựa chọn',
                format: this.format,
                dayViewHeaderFormat: { month: 'short', year: 'numeric' }
            },
            useCurrent: false
        })

        this.$refs.pickerWrapper.addEventListener('hide.td', (e) => {
            const date = e.detail.date;
            const currentValue = this.modelValue;
            if (date instanceof Date) {
                const formatted = moment(date).format('DD/MM/YYYY');
                if (formatted !== currentValue) {
                    this.$emit('update:modelValue', formatted);
                }
            }
        });
    }
}
</script>
