<template>
    <div>
        <!-- Các nút filter (chỉ hiện nếu filter = true và group=1) -->
        <div v-if="filter && group === 1" class="mb-2 flex-wrap">
            <button
                class="btn m-1 btn-filter"
                :class="{'btn-primary': currentFilter === null, 'btn-outline-primary': currentFilter !== null}"
                @click="chonFilter(null)">
                Tất cả ({{ flatItems.length }})
            </button>
            <button
                v-for="(g, index) in data"
                :key="index"
                class="btn m-1 btn-filter"
                :class="{'btn-primary': currentFilter === g.groupName, 'btn-outline-primary': currentFilter !== g.groupName}"
                @click="chonFilter(g.groupName)">
                {{ g.groupName }} ({{ g.items.length }})
            </button>
        </div>

        <!-- Select2 -->
        <select ref="select" :multiple="multiple" style="width: 100%">
            <!-- Chọn 1: select2 cần option rỗng đứng đầu thì placeholder/allowClear mới hoạt động -->
            <option v-if="!multiple" value=""></option>

            <!-- Nếu group=1 thì render optgroup -->
            <template v-if="group === 1">
                <optgroup
                    v-for="(g, index) in filteredData"
                    :key="index"
                    :label="`${g.groupName} (${g.items.length})`"
                >
                    <option
                        v-for="item in g.items"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.text }}
                    </option>
                </optgroup>
            </template>

            <!-- Nếu group=0 thì render option phẳng -->
            <template v-else>
                <option
                    v-for="item in flatItems"
                    :key="item.id"
                    :value="item.id"
                >
                    {{ item.text }}
                </option>
            </template>
        </select>
    </div>
</template>


<script>
export default {
    name: "Select2Group",
    props: {
        modelValue: {
            type: [Array, String, Number],
            default: () => []
        },
        data: {
            type: Array,
            default: () => []
        },
        options: {
            type: Object,
            default: () => ({})
        },
        filter: {
            type: Boolean,
            default: false
        },
        group: {
            type: Number, // 0: option phẳng, 1: optgroup
            default: 1
        },
        multiple: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            currentFilter: null
        };
    },
    computed: {
        filteredData() {
            if (this.group === 0) return [];
            if (!this.filter || this.currentFilter === null) return this.data;
            return this.data.filter(g => g.groupName === this.currentFilter);
        },
        flatItems() {
            if (this.group === 0) {
                return this.data.flatMap(g => g.items || []); // hỗ trợ cả khi vẫn gửi theo group
            }
            return this.data.flatMap(g => g.items);
        }
    },
    mounted() {
        this.initSelect2();
    },
    beforeUnmount() {
        this.destroySelect2();
    },
    watch: {
        modelValue(val) {
            $(this.$refs.select).val(val).trigger("change.select2");
        },
        filteredData() {
            if (this.group === 1) {
                this.$nextTick(() => {
                    this.destroySelect2();
                    this.initSelect2();
                });
            }
        },
        flatItems() {
            if (this.group === 0) {
                this.$nextTick(() => {
                    this.destroySelect2();
                    this.initSelect2();
                });
            }
        }
    },
    methods: {
        chonFilter(groupName) {
            this.currentFilter = groupName;
        },
        initSelect2() {
            // Trong modal: mặc định select2 gắn dropdown vào body, nằm ngoài modal
            // nên Bootstrap kéo focus về và không gõ được ô tìm kiếm. Neo vào modal để tránh.
            const modal = this.$refs.select.closest('.modal');
            const defaultOptions = {
                closeOnSelect: !this.multiple,
                placeholder: "Chọn...",
                allowClear: true,
                width: "100%",
                ...(modal ? { dropdownParent: $(modal) } : {})
            };

            $(this.$refs.select)
                .select2({ ...defaultOptions, ...this.options })
                .val(this.modelValue)
                .trigger("change.select2")
                .on("change", () => {
                    let value = $(this.$refs.select).val() || [];
                    if (!this.multiple) {
                        value = value ? value.toString() : null;
                    }
                    this.$emit("update:modelValue", value);
                });
        },
        destroySelect2() {
            if (this.$refs.select) {
                $(this.$refs.select).off().select2("destroy");
            }
        }
    }
};
</script>
