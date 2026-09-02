<template>
    <div :class="[hasLabel ? 'form-group mb-2' : 'm-0 p-0', extraClasses]">
        <label v-if="hasLabel" :class="labelClass">{{ label }}</label>
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
    name: 'LTESelect2Option',
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
        labelClass: {
            type: String,
            default: 'font-weight-bold small text-muted text-xs mb-1',
        },
        class: {
            type: String,
            default: '',
        },
        style: {
            type: [String, Object],
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
            default: 'body',
        },
        isDisabled: {
            type: [Boolean, Number],
            required: false,
            default: false,
        },
        enableDataWatch: {
            type: Boolean,
            default: true,
        },
    },
    computed: {
        hasLabel() {
            return this.label && String(this.label).trim() !== '';
        },
        extraClasses() {
            return this.class || '';
        }
    },
    mounted() {
        this.$nextTick(() => {
            if (this.$refs.select2) {
                $(this.$refs.select2).select2({
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                    allowClear: this.allowClear,
                    closeOnSelect: this.closeOnSelect,
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
            }
        })
    },
    watch: {
        initValue(newVal) {
            this.$nextTick(() => {
                if (this.$refs.select2) {
                    $(this.$refs.select2).val(newVal).trigger('change.select2')
                }
            })
        },
        modelValue(newVal) {
            this.$nextTick(() => {
                if (this.$refs.select2) {
                    $(this.$refs.select2).val(newVal).trigger('change.select2')
                }
            })
        },
        isDisabled(newVal) {
            this.$nextTick(() => {
                if (this.$refs.select2) {
                    $(this.$refs.select2).prop('disabled', newVal)
                }
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
    methods: {
        getDropdownParent() {
            if (this.dropdownParent instanceof HTMLElement) {
                return $(this.dropdownParent)
            } else if (typeof this.dropdownParent === 'string' && this.dropdownParent.trim() !== '') {
                return $(this.dropdownParent)
            } else {
                return $('body')
            }
        },
    },
}
</script>

<style>
/* Global Select2 styling matching form-control-sm text-xs across desktop and mobile */
.select2-container--default .select2-selection--single {
    height: 31px !important;
    padding: 2px 6px !important;
    font-size: 0.75rem !important;
    line-height: 1.5 !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    background-color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 0.75rem !important;
    color: #495057 !important;
    padding-left: 2px !important;
    padding-right: 18px !important;
    margin-top: 0 !important;
    line-height: 27px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 29px !important;
    top: 1px !important;
    right: 3px !important;
}

/* Select2 Multiple Selection styling */
.select2-container--default .select2-selection--multiple {
    min-height: 31px !important;
    padding: 1px 4px !important;
    font-size: 0.75rem !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    font-size: 0.75rem !important;
    padding: 1px 6px !important;
    margin-top: 2px !important;
    margin-bottom: 2px !important;
    background-color: #e9ecef !important;
    border: 1px solid #adb5bd !important;
    border-radius: 0.2rem !important;
    color: #212529 !important;
}

/* Select2 Dropdown List styling attached to body */
.select2-container--open {
    z-index: 999999 !important;
}

.select2-container .select2-dropdown {
    font-size: 0.75rem !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 0.25rem !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
    z-index: 999999 !important;
    background-color: #ffffff !important;
}

.select2-results__options {
    max-height: 250px !important;
    overflow-y: auto !important;
    scrollbar-width: thin;
}

.select2-container--default .select2-results__option {
    padding: 5px 10px !important;
    font-size: 0.75rem !important;
    line-height: 1.4 !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #007bff !important;
    color: #ffffff !important;
}

.select2-search--dropdown {
    padding: 4px !important;
}

.select2-search--dropdown .select2-search__field {
    padding: 3px 6px !important;
    font-size: 0.75rem !important;
    height: 26px !important;
    border-radius: 0.2rem !important;
    border: 1px solid #ced4da !important;
}

.select2-selection__clear {
    padding-left: 6px !important;
    padding-right: 6px !important;
    border-radius: 4px;
    font-size: 0.75rem !important;
    line-height: 27px !important;
}

.select2-selection__clear:hover {
    background-color: #e2e8f0;
}
</style>
