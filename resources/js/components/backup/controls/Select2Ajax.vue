<template>
    <div class="form-group">
        <label v-if="label">{{ label }}</label>
        <select ref="select2" class="form-control custom-select" :multiple="multiple" :data-placeholder="placeholder"
            :style="{ width: '100%', ...style }">
            <option v-for="item in localOptions" :key="item.value" :value="item.value" selected>
                {{ item.text }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: [Array, String, Number],
        label: String,
        placeholder: {
            type: String,
            default: 'Hãy chọn 1 lựa chọn'
        },
        style: {
            type: [String, Object],
            default: () => ({})
        },
        multiple: {
            type: Boolean,
            default: true
        },
        allowClear: {
            type: Boolean,
            default: true
        },
        closeOnSelect: {
            type: Boolean,
            default: false
        },
        dropdownParent: {
            type: [String, HTMLElement],
            default: null
        },
        isDisabled: {
            type: Boolean,
            default: false
        },
        dropdownAutoWidth: {
            type: Boolean,
            default: false
        },
        minimumResultsForSearch: {
            type: Number,
            default: 0
        },
        minimumInputLength: {
            type: Number,
            default: 1
        },
        ajaxUrl: {
            type: String,
            required: true
        },
        fetchSelectedUrl: {
            type: String,
            default: ''
        },
        additionalParams: {
            type: Object,
            default: () => ({})
        }
    },

    data() {
        return {
            localOptions: []
        };
    },

    mounted() {
        this.initSelect2();
    },

    watch: {
        modelValue() {
            this.setSelect2Value();
        }
    },

    methods: {
        getDropdownParent() {
            if (this.dropdownParent instanceof HTMLElement) {
                return $(this.dropdownParent);
            } else if (typeof this.dropdownParent === 'string') {
                return $(this.dropdownParent);
            } else {
                return $(this.$refs.select2).parent();
            }
        },

        initSelect2() {
            const self = this;
            const $select = $(this.$refs.select2);

            $select.select2({
                ajax: {
                    url: this.ajaxUrl,
                    dataType: 'json',
                    delay: 250,
                    data(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
                            ...self.additionalParams
                        };
                    },
                    processResults(data, params) {
                        params.page = params.page || 1;

                        const items = (data.data || []).map((item) => ({
                            id: item.value,
                            text: item.text
                        }));

                        return {
                            results: items,
                            pagination: {
                                more: data.current_page < data.last_page
                            }
                        };
                    },
                    cache: true
                },
                width: '100%',
                placeholder: this.placeholder,
                allowClear: this.allowClear,
                closeOnSelect: this.closeOnSelect,
                dropdownParent: this.getDropdownParent(),
                dropdownAutoWidth: this.dropdownAutoWidth,
                minimumResultsForSearch: this.minimumResultsForSearch,
                minimumInputLength: this.minimumInputLength
            });

            if (this.isDisabled) {
                $select.prop('disabled', true);
            }

            $select.on('change', () => {
                const val = $select.val();
                const parsedVal = this.multiple ? val : (Array.isArray(val) ? val[0] : val);
                this.$emit('update:modelValue', parsedVal);
            });

            this.fetchSelectedItems();
        },

        async fetchSelectedItems() {
            const value = this.modelValue;
            if (!value || !this.fetchSelectedUrl) return;

            try {
                const ids = Array.isArray(value) ? value : [value];
                const response = await fetch(
                    `${this.fetchSelectedUrl}?ids=${encodeURIComponent(ids.join(','))}`
                );
                const items = await response.json();

                this.localOptions = items.map((item) => ({
                    value: item.value,
                    text: item.text
                }));

                this.$nextTick(() => {
                    $(this.$refs.select2).val(ids).trigger('change.select2');
                });
            } catch (err) {
                console.error('Lỗi khi fetch dữ liệu được chọn:', err);
            }
        },

        setSelect2Value() {
            const value = this.modelValue;
            const $select = $(this.$refs.select2);
            const val = this.multiple ? (Array.isArray(value) ? value : [value]) : value;
            $select.val(val).trigger('change.select2');
        }
    }
};
</script>

<style scoped>
.select2-results__options {
    max-height: 400px !important;
    overflow-y: auto !important;
    scrollbar-width: thin;
}

.select2-selection__clear {
    padding-left: 9px !important;
    padding-right: 9px !important;
    border-radius: 5px;
}

.select2-selection__clear:hover {
    background-color: #ccc;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    margin-top: -4px;
}

.select2-container .select2-dropdown {
    box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px,
        rgba(0, 0, 0, 0.12) 0px -12px 30px,
        rgba(0, 0, 0, 0.12) 0px 4px 6px,
        rgba(0, 0, 0, 0.17) 0px 12px 13px,
        rgba(0, 0, 0, 0.09) 0px -3px 5px;
}
</style>
