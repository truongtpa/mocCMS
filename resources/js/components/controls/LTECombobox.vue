<template>
    <div class="form-group position-relative">
        <label>{{ label }}</label>
        <div ref="combobox" class="input-group input-group" style="margin-bottom: 12px">
            <input
                class="form-control float-right border-right-0"
                ref="input"
                :placeholder="placeholder"
                :class="inputClass"
                :disabled="isDisabled"
                v-model="inputValue"
                @focus="showDropdown = true"
                @keydown.down.prevent="moveDown"
                @keydown.up.prevent="moveUp"
                @keydown.enter.prevent="selectHighlighted"
                @keydown.esc="showDropdown = false"
                @blur="$emit('blur', $event)"
            />
            <div class="input-group-append" v-if="showClearButton">
                <button type="button" class="btn btn-default bg-white border-left-0" :disabled="isDisabled"
                        @click="clearValue">
                    <i class="fas fa-backspace text-xs"></i>
                </button>
            </div>
        </div>
        <ul
            v-show="showDropdown && filteredOptions.length"
            class="dropdown-menu show w-100"
            style="max-height: 400px ;overflow-y: auto"
            @mouseenter="dropdownHovered = true"
            @mouseleave="dropdownHovered = false"
        >
            <li
                v-for="(option, index) in filteredOptions"
                :key="option.value"
                ref="dropdownItems"
                @mousedown.prevent="selectOption(option)"
                @mouseover="onMouseOver(index)"
                class="dropdown-item"
                :class="{ active: index === highlightedIndex }"
            >
                <span>{{ option.text }}</span>
                <span v-if="option.mo_ta" class="text-muted" style="white-space: nowrap">{{ option.mo_ta }}</span>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        label: {type: String, default: 'Chưa đặt tên'},
        placeholder: {type: String, default: 'Chọn một tùy chọn'},
        inputClass: {type: String, default: 'form-control'},
        modelValue: {type: [String, Number], default: ''},
        options: {type: Array, default: () => []},
        isDisabled: {
            type: Boolean,
            default: false,
        },
        showClearButton: {
            type: Boolean,
            default: true,
        }
    },
    data() {
        return {
            searchQuery: '',
            showDropdown: false,
            dropdownHovered: false,
            highlightedIndex: -1,
            isKeyboardNavigating: false,

        }
    },
    computed: {
        inputValue: {
            get() {
                return this.modelValue || ''
            },
            set(val) {
                this.$emit('update:modelValue', val)
                this.showDropdown = true
                this.highlightedIndex = -1
            }
        },
        filteredOptions() {
            if (!this.options.length) return []

            const keyword = this.removeAccents(this.modelValue || '').replace(/\s/g, '')

            const filtered = this.options.filter((option) => {
                const text = this.removeAccents(option.text).replace(/\s/g, '')
                const moTa = option.mo_ta ? this.removeAccents(option.mo_ta).replace(/\s/g, '') : ''
                return text.includes(keyword) || moTa.includes(keyword)
            })

            return filtered.length ? filtered : [{text: 'Không có dữ liệu', value: null}]
        }

    },
    watch: {
        selectOption(option) {
            const text = option.mo_ta ? `${option.text} ${option.mo_ta}` : option.text
            this.$emit('update:modelValue', text)   // store text only
            this.$emit('change', text)
            this.showDropdown = false
            this.isKeyboardNavigating = false
        },
        modelValue() {
            this.syncSearchQuery()
        },
        options() {
            // Nếu đang chọn (modelValue tồn tại) và không gõ tay (input chưa focus), mới sync lại
            if (this.modelValue && !this.showDropdown) {
                this.syncSearchQuery()
            }
        },
        showDropdown(newValue) {
            if (newValue) {
                document.addEventListener('click', this.clickOutside)
            } else {
                document.removeEventListener('click', this.clickOutside)
                this.highlightedIndex = -1
                this.isKeyboardNavigating = false
                this.$emit('blur')
            }
        },
    },
    mounted() {
        this.syncSearchQuery()
    },
    methods: {
        onInputChange(e) {
            this.searchQuery = e.target.value
            this.$emit('search', this.searchQuery)
            this.showDropdown = true
            this.highlightedIndex = -1
        },

        syncSearchQuery() {
            const selectedOption = this.options.find((option) => option.value === this.modelValue)
            this.searchQuery = selectedOption
                ? selectedOption.mo_ta
                    ? `${selectedOption.text} ${selectedOption.mo_ta}`
                    : selectedOption.text
                : ''
        },

        filterOptions() {
            this.showDropdown = true
            this.highlightedIndex = -1
        },

        selectOption(option) {
            this.searchQuery = option.mo_ta ? `${option.text} ${option.mo_ta}` : option.text
            this.$emit('update:modelValue', option.value)
            this.$emit('change', option)
            this.showDropdown = false
            this.isKeyboardNavigating = false
        },

        selectHighlighted() {
            if (this.highlightedIndex !== -1) {
                this.selectOption(this.filteredOptions[this.highlightedIndex])
            }
        },
        moveDown() {
            this.isKeyboardNavigating = true
            if (this.highlightedIndex < this.filteredOptions.length - 1) {
                this.highlightedIndex++
                this.scrollToHighlighted()
            }
        },
        moveUp() {
            this.isKeyboardNavigating = true
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--
                this.scrollToHighlighted()
            }
        },

        onMouseOver(index) {
            if (!this.isKeyboardNavigating) {
                this.highlightedIndex = index
            }
        },

        clickOutside(event) {
            if (this.$refs.combobox && !this.$refs.combobox.contains(event.target)) {
                this.showDropdown = false
            }
        },
        clearValue() {
            this.searchQuery = ''
            this.$emit('update:modelValue', null)
            this.showDropdown = true
            this.$refs.input?.focus()
        },

        removeAccents(str) {
            return str
                .toString()
                .normalize('NFD')                // tách dấu
                .replace(/[\u0300-\u036f]/g, '') // xoá dấu
                .replace(/đ/g, 'd')              // xử lý đặc biệt
                .replace(/Đ/g, 'D')
                .toLowerCase()
        },

        scrollToHighlighted() {
            this.$nextTick(() => {
                const items = this.$refs.dropdownItems
                const current = Array.isArray(items) ? items[this.highlightedIndex] : null

                if (current && current.scrollIntoView) {
                    current.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    })
                }
            })
        }
    },
}
</script>

<style scoped>
.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.2s ease,
    visibility 0.2s ease;
}

.dropdown-menu.show {
    visibility: visible;
    opacity: 1;
}

.dropdown-item {
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dropdown-item:hover,
.dropdown-item.active {
    background-color: #007bff;
    color: #fff;
}

</style>
