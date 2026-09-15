<template>
    <!-- Tiêu đề nhóm: chỉ là nhãn, không bấm được -->
    <li v-if="item.type === 'header'" class="nav-header">
        {{ item.text }}
    </li>

    <!-- Mục có liên kết -->
    <li v-else-if="item.type === 'item'" class="nav-item">
        <component
            :is="linkComponent"
            v-bind="linkProps"
            :class="['nav-link', { active: isActive }]"
        >
            <i
                v-if="item.icon"
                :class="['nav-icon', 'bi', item.icon, item.iconColor && `text-${item.iconColor}`]"
                aria-hidden="true"
            ></i>
            <p>
                {{ item.text }}
                <span
                    v-if="item.badge != null"
                    :class="`nav-badge badge text-bg-${item.badgeColor || 'secondary'}`"
                >{{ item.badge }}</span>
            </p>
        </component>
    </li>

    <!-- Nhóm có menu con -->
    <li v-else :class="['nav-item', { 'menu-open': isOpen }]">
        <button
            type="button"
            class="nav-link"
            :aria-expanded="isOpen"
            v-bind="item.attrs"
            @click="isOpen = !isOpen"
        >
            <i v-if="item.icon" :class="['nav-icon', 'bi', item.icon]" aria-hidden="true"></i>
            <p>
                {{ item.text }}
                <i class="nav-arrow bi bi-chevron-right" aria-hidden="true"></i>
                <span
                    v-if="item.badge != null"
                    :class="`nav-badge badge text-bg-${item.badgeColor || 'secondary'} me-3`"
                >{{ item.badge }}</span>
            </p>
        </button>

        <Transition v-bind="transition">
            <ul v-show="isOpen" class="nav-treeview">
                <AppNavItem
                    v-for="child in item.children"
                    :key="keyOf(child)"
                    :item="child"
                    :current-path="currentPath"
                    :link-component="linkComponent"
                />
            </ul>
        </Transition>
    </li>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { collapseTransition } from './collapseTransition'

/**
 * Một dòng trong menu sidebar. Tự gọi lại chính nó cho menu nhiều cấp.
 *
 * Ba dạng item, phân biệt bằng `type`:
 *   { type: 'header', text }
 *   { type: 'item',   text, href, icon, iconColor, badge, badgeColor, target, attrs }
 *   { type: 'group',  text, icon, badge, badgeColor, children: [...], attrs }
 */
const props = defineProps({
    item: { type: Object, required: true },
    currentPath: { type: String, default: '/' },

    /** 'a' cho link thường, hoặc RouterLink để đi bằng vue-router */
    linkComponent: { type: [String, Object, Function], default: 'a' },
})

const transition = collapseTransition()

const keyOf = (node) => (node.type === 'item' ? node.href : `${node.type}:${node.text}`)

/**
 * '/' chỉ khớp đúng trang chủ, còn lại khớp cả trang con
 * ('/bai-viet' sáng khi đang ở '/bai-viet/12').
 */
function matches(href) {
    if (!href) return false
    if (href === '/') return props.currentPath === '/'
    return props.currentPath === href || props.currentPath.startsWith(`${href}/`)
}

function hasActiveDescendant(node) {
    if (node.type === 'item') return matches(node.href)
    if (node.type === 'group') return node.children.some(hasActiveDescendant)
    return false
}

const isActive = computed(() => props.item.type === 'item' && matches(props.item.href))

const groupActive = computed(
    () => props.item.type === 'group' && props.item.children.some(hasActiveDescendant)
)

// Nhóm chứa trang hiện tại thì mở sẵn; sau đó người dùng tự đóng/mở tùy ý
const isOpen = ref(groupActive.value)
watch(groupActive, (active) => {
    if (active) isOpen.value = true
})

const linkProps = computed(() => {
    if (props.item.type !== 'item') return {}
    const { href, target, attrs } = props.item
    // RouterLink nhận `to`, thẻ <a> nhận `href`
    const isTag = typeof props.linkComponent === 'string'
    return { ...attrs, [isTag ? 'href' : 'to']: href, target }
})
</script>
