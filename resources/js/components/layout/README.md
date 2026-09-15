# Bộ khung giao diện

Thay cho `@adminlte/vue`. Dự án chỉ dùng đúng 3 component của AdminLTE
(`LteDashboardLayout`, `LteAppContent`, `LteCard`) nên gói 2.1 MB kèm 377 KB CSS
không đáng giữ lại.

Đã đăng ký toàn cục trong `resources/js/main.js` (`app.use(AppLayoutPlugin)`),
dùng thẳng trong template không cần import.

| Component | Thay cho | Dùng khi |
|---|---|---|
| `AppLayout` | `LteDashboardLayout` | Khung ngoài cùng, đặt trong `App.vue` |
| `AppContent` | `LteAppContent` | Thân một trang: dải tiêu đề + nội dung |
| `AppCard` | `LteCard` | Khung nội dung có tiêu đề |
| `AppSidebar` / `AppTopbar` / `AppNavItem` | — | Bộ phận của `AppLayout`, ít khi dùng trực tiếp |

Phần nhìn chia làm hai file:

- `resources/css/layout.css` — **bố cục**: lưới trang, sidebar, thanh trên, chân trang.
- `resources/css/theme.css` — **màu và kiểu chữ**, qua biến `--bs-*` của Bootstrap.

Thứ tự nạp trong `main.js` là `bootstrap → layout.css → theme.css`, nên theme
luôn đè được lên layout.

---

## AppLayout

```vue
<AppLayout
    :menu-items="menu"
    :current-path="$route.path"
    :link-component="RouterLink"
    :user="user"
    logo="/favicon.ico"
    brand-text="mocCMS"
    @logout="dangXuat"
>
    <router-view />

    <template #footer><strong>mocCMS</strong></template>
</AppLayout>
```

### Props

| Prop | Mặc định | Ý nghĩa |
|---|---|---|
| `menu-items` | `[]` | Cấu trúc menu, xem phần dưới |
| `current-path` | `'/'` | Thường là `$route.path`, dùng để tô sáng mục đang mở |
| `link-component` | `'a'` | Truyền `RouterLink` để menu đi bằng vue-router, không tải lại trang |
| `user` | `null` | `{ name, image, role }`. `null` thì ẩn hẳn menu tài khoản |
| `logo` / `logo-href` / `brand-text` | `''` / `'/'` / `''` | Phần thương hiệu góc trên trái |
| `sidebar-theme` | `'dark'` | `'light'` nếu muốn sidebar sáng theo trang |
| `color-mode-toggle` / `fullscreen` | `true` | Ẩn/hiện hai nút trên thanh trên |
| `footer-right-text` | `''` | Chữ góc phải chân trang |
| `sidebar-breakpoint` | `992` | Ngưỡng px chuyển sang kiểu mobile |
| `initial-color-mode` | `'auto'` | Dùng khi người dùng chưa từng chọn |

### Slots

`#default` (nội dung trang), `#footer`, `#footer-right`, `#topbar-start`,
`#topbar-end`, `#sidebar` (thay cả cây menu), `#sidebar-brand`, `#logo`,
và bộ `#user-menu` / `#user-header` / `#user-body` / `#user-footer` cho
dropdown tài khoản.

### Events

`@logout`, `@profile` — phát khi bấm hai mục mặc định trong dropdown tài khoản.

---

## Cấu trúc menu

Ba dạng, phân biệt bằng `type`. Menu lồng bao nhiêu cấp cũng được.

```js
menu: [
    { type: 'item', text: 'Bảng điều khiển', href: '/', icon: 'bi-speedometer' },

    { type: 'header', text: 'Nội dung' },

    {
        type: 'group',
        text: 'Bài viết',
        icon: 'bi-newspaper',
        badge: 3,                 // tùy chọn
        badgeColor: 'danger',     // tên màu Bootstrap, mặc định 'secondary'
        children: [
            { type: 'item', text: 'Danh sách', href: '/bai-viet', icon: 'bi-list-ul' },
            { type: 'item', text: 'Chuyên mục', href: '/chuyen-muc', icon: 'bi-tag' },
        ],
    },
]
```

Mục `item` nhận thêm `iconColor`, `target`, và `attrs` (object đổ thẳng vào thẻ link).

Cách tô sáng: `/` chỉ khớp đúng trang chủ, các href khác khớp cả trang con —
`/bai-viet` vẫn sáng khi đang ở `/bai-viet/12`. Nhóm chứa trang hiện tại thì tự
mở sẵn lúc vào trang.

---

## AppContent

```vue
<AppContent title="Bài viết" :breadcrumbs="[{ label: 'Trang chủ', href: '/' }, { label: 'Bài viết' }]">
    …nội dung…
</AppContent>
```

Không truyền `title`, `breadcrumbs` lẫn slot `#header` thì dải tiêu đề bị bỏ hẳn,
nội dung sát lên đầu. `:fluid="false"` để bó bề ngang theo `container-lg`.

## AppCard

```vue
<AppCard title="Danh sách bài viết" icon="bi-newspaper" collapsible>
    …nội dung…
    <template #tools><button class="btn btn-tool">…</button></template>
    <template #footer>…</template>
</AppCard>
```

Props: `title`, `icon`, `theme`, `collapsible`, `default-collapsed`, `removable`,
`header-class`, `body-class`, `footer-class`.

---

## Composable

Dùng trong bất kỳ component nào nằm dưới `AppLayout`:

```js
import { useSidebar, useColorMode } from '@/components/layout'

const { isCollapsed, isMobileOpen, isMobile, toggle, closeMobile } = useSidebar()
const { colorMode, resolvedMode, setColorMode } = useColorMode()
```

---

## Những chỗ cần biết khi sửa

- **Ngưỡng mobile nằm ở hai nơi**: prop `sidebar-breakpoint` (mặc định 992) và
  media query `991.98px` trong `layout.css`. Đổi thì đổi cả hai.
- **Chế độ màu ghi ra `data-bs-theme` trên `<html>`**, khóa localStorage là
  `moccms.theme`. Khóa này còn được đọc bởi đoạn script inline trong
  `resources/views/app.blade.php` để trang không nháy nền sáng trước khi Vue mount —
  sửa khóa thì sửa cả hai chỗ.
- **Dropdown chạy bằng JS của Bootstrap** (`data-bs-toggle="dropdown"`), nên
  `main.js` phải còn dòng `import 'bootstrap'`.
- **Menu dùng `display: block`, không mượn class `.nav`** của Bootstrap — `.nav`
  là flex nên mục con sẽ xếp ngang và co theo chữ.
