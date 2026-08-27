<template>
    <div class="form-group">
        <label v-if="label">{{ label }}</label>
        <select
            ref="select2"
            class="form-control custom-select"
            :multiple="multiple"
            :data-placeholder="placeholder"
            :style="{ width: '100%', ...style }"
        >
            <option v-for="item in data" :key="item.value" :value="item.value">{{ item.text }}</option>
        </select>
    </div>
</template>

<script>
export default {
    props: {
        data: Array,
        modelValue: [Array, String, Number],
        placeholder: {
            type: String,
            required: false,
            default: 'Hãy chọn 1 lựa chọn',
        },
        label: {
            type: String,
            required: false,
            default: null,
        },
        style: {
            type: String,
            required: false,
            default: '',
        },
        multiple: {
            type: Boolean,
            required: false,
            default: true,
        },
        closeOnSelect: {
            type: Boolean,
            required: false,
            default: false,
        },
        allowClear: {
            type: Boolean,
            required: false,
            default: true,
        },
        initValue: {
            type: [Array, String, Number],
            required: false,
            default: null,
        },
        dropdownAutoWidth: {
            type: Boolean,
            required: false,
            default: false,
        },
        minimumResultsForSearch: {
            type: Number,
            required: false,
            default: 0,
        },
        dropdownParent: {
            type: [String, HTMLElement],
            default: null,
        },
        isDisabled: {
            type: [Boolean, Number],
            required: false,
            default: false,
        },
        enableDataWatch: {
            type: Boolean,
            default: false,
        },
    },
    mounted() {
        this.$nextTick(() => {
            if (this.$refs.select2) {
                $(this.$refs.select2).select2({
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                    allowClear: this.allowClear,
                    closeOnSelect: this.closeOnSelect,
                    // dropdownParent: $(this.$refs.select2).parent(),
                    dropdownParent: this.getDropdownParent(),
                    dropdownAutoWidth: this.dropdownAutoWidth,
                    minimumResultsForSearch: this.minimumResultsForSearch,
                })

                $(this.$refs.select2).on('select2:open', () => {
                    setTimeout(() => {
                        const searchField = document.querySelector('.select2-search__field')
                        if (searchField) searchField.focus()
                    }, 50)
                })

                if (this.isDisabled) {
                    $(this.$refs.select2).prop('disabled', true)
                }

                if (this.initValue !== undefined && this.initValue !== null) {
                    $(this.$refs.select2).val(this.initValue).trigger('change.select2')
                } else {
                    $(this.$refs.select2).val(null).trigger('change.select2')
                }

                $(this.$refs.select2).on('change', (event) => {
                    const selectedValues = $(this.$refs.select2).val()
                    this.$emit('update:modelValue', selectedValues)
                    this.$emit('update:selectedValues', selectedValues, event)
                })
            } else {
            }

            // document.querySelector('.select2-search__field').setAttribute('readonly', true);
        })
    },
    watch: {
        initValue(newVal) {
            this.$nextTick(() => {
                $(this.$refs.select2).val(newVal).trigger('change.select2')
            })
        },
        //modelValue(newVal) {
        //    this.$nextTick(() => {
        //        $(this.$refs.select2).val(newVal).trigger("change");
        //    });
        //},
        isDisabled(newVal) {
            this.$nextTick(() => {
                $(this.$refs.select2).prop('disabled', newVal)
            })
        },
        data: {
            handler() {
                if (!this.enableDataWatch) return

                this.$nextTick(() => {
                    if (this.$refs.select2) {
                        $(this.$refs.select2).off().select2('destroy')

                        $(this.$refs.select2).select2({
                            width: $(this).data('width')
                                ? $(this).data('width')
                                : $(this).hasClass('w-100')
                                  ? '100%'
                                  : 'style',
                            allowClear: this.allowClear,
                            closeOnSelect: this.closeOnSelect,
                            dropdownParent: this.getDropdownParent(),
                            dropdownAutoWidth: this.dropdownAutoWidth,
                            minimumResultsForSearch: this.minimumResultsForSearch,
                        })

                        if (this.initValue !== undefined && this.initValue !== null) {
                            $(this.$refs.select2).val(this.initValue).trigger('change.select2')
                        } else {
                            $(this.$refs.select2).val(null).trigger('change.select2')
                        }

                        $(this.$refs.select2).on('change', (event) => {
                            const selectedValues = $(this.$refs.select2).val()
                            this.$emit('update:modelValue', selectedValues)
                            this.$emit('update:selectedValues', selectedValues, event)
                        })

                        $(this.$refs.select2).on('select2:open', () => {
                            setTimeout(() => {
                                const searchField = document.querySelector('.select2-search__field')
                                if (searchField) searchField.focus()
                            }, 50)
                        })
                    }
                })
            },
            deep: true,
        },
    },
    data() {
        return {}
    },
    methods: {
        getDropdownParent() {
            if (this.dropdownParent instanceof HTMLElement) {
                return $(this.dropdownParent)
            } else if (typeof this.dropdownParent === 'string') {
                return $(this.dropdownParent)
            } else {
                return $(this.$refs.select2).parent()
            }
        },
    },
}
</script>

<style>
.select2-results__options {
    max-height: 400px !important;
    overflow-y: auto !important;
    scrollbar-width: thin;
}

.select2-selection__clear {
    /* margin-left: 15px !important; */
    padding-left: 9px !important;
    padding-right: 9px !important;
    border-radius: 5px;
    /* font-size: 20px; */
}

.select2-selection__clear:hover {
    background-color: #ccc;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    margin-top: -4px;
}

.select2-container .select2-dropdown {
    box-shadow:
        rgba(0, 0, 0, 0.25) 0px 54px 55px,
        rgba(0, 0, 0, 0.12) 0px -12px 30px,
        rgba(0, 0, 0, 0.12) 0px 4px 6px,
        rgba(0, 0, 0, 0.17) 0px 12px 13px,
        rgba(0, 0, 0, 0.09) 0px -3px 5px;
}

@media (max-width: 768px) {
    .select2-container .select2-results__option {
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .select2-container--default .select2-selection--single {
        height: 31px !important;
        padding: 4px 8px !important;
        font-size: 0.875rem !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 3px !important;
        right: 3px !important;
    }
}
</style>
