# Controls dùng chung

Đã đăng ký toàn cục trong `resources/js/main.js` (`app.use(AppControls)`) nên dùng
thẳng trong template, không cần import từng trang.

| Component | Thay cho bản cũ | Dùng khi |
|---|---|---|
| `AppTable` | `TablePro`, `AppTable`, `LTETable`, `LTETableV2` | Danh sách có phân trang |
| `AppModal` | `LTEModal`, `LTEModal2` | Form / xem chi tiết |
| `AppConfirm` | `YesNoModal` | Hỏi xác nhận trước hành động |
| `AppImage` | — | Mọi chỗ hiện ảnh từ dữ liệu |

---

## AppTable

```vue
<AppTable
    :items="table.list"
    :columns="columns"
    :pagination="table.pagination"
    item-key="id_tin_tuc"
    selectable
    v-model:sort-key="boLoc.sortKey"
    v-model:sort-order="boLoc.sortOrder"
    @sort-change="getData(1)"
    @page-change="getData"
    @selection-change="daChon = $event"
>
    <template #toolbar>…ô tìm kiếm, nút thêm mới…</template>
    <template #cell-xuat_ban="{ value }">
        <span class="badge">{{ value ? 'Đã xuất bản' : 'Bản nháp' }}</span>
    </template>
    <template #actions="{ item }">
        <button class="btn btn-sm btn-outline-danger" @click="xoa(item)">Xóa</button>
    </template>
</AppTable>
```

### Cấu hình cột

JS chỉ giữ **dữ liệu và hành vi**. Phần **trình bày** (bề rộng, căn lề, xuống dòng)
làm bằng CSS cho dễ chỉnh:

```js
columns: [
    { key: 'tieu_de', label: 'Tiêu đề', sortable: true },
    { key: 'tac_gia.ho_ten', label: 'Tác giả' },            // hỗ trợ key lồng
    { key: 'ngay_tao', label: 'Ngày tạo', formatter: (v) => formatNgay(v) },
]
```

Thuộc tính cột: `key, label, sortable, formatter(value, item), class, headerClass`.

AppTable tự gắn class `app-col-<key>` lên **cả `<th>` lẫn `<td>`**, nên chỉnh cột
ở một chỗ duy nhất trong `<style>` của trang:

```vue
<style scoped>
/* dùng :deep() vì th/td do component con render */
:deep(.app-col-tieu_de)  { width: 26%; }
:deep(.app-col-ngay_tao) { width: 130px; white-space: nowrap; }
:deep(.app-col-xuat_ban) { width: 120px; text-align: center; }
</style>
```

Các cột dựng sẵn cũng có class riêng để chỉnh tương tự:
`app-table-check-col`, `app-table-actions-col`.

### Props chính

| Prop | Mặc định | Ghi chú |
|---|---|---|
| `items` / `columns` / `pagination` | `[]` / `[]` / `{}` | `pagination` nhận thẳng paginator của Laravel |
| `itemKey` | `'id'` | Khoá định danh dòng |
| `sortMode` | `'server'` | `'server'` phát sự kiện; `'local'` tự sort trong trang |
| `selectable` / `selectMode` | `false` / `'multiple'` | `'single'` dùng radio |
| `preserveSelection` | `false` | Giữ lựa chọn khi đổi trang |
| `minWidth` | `'860px'` | Hẹp hơn thì cuộn ngang thay vì bóp nát cột |
| `bordered` | `false` | Mặc định chỉ kẻ ngang cho khớp theme; bật để kẻ cả dọc |
| `highlight` | `''` | Làm nổi bật từ khoá (an toàn, không dùng `v-html`) |
| `stickyHeader`, `groupBy`, `rowClass`, `striped`, `small` | | |

### Slot

`toolbar`, `selection-actions`, `header-<key>`, `cell-<key>`, `actions`, `empty`, `group-header`, `footer`.

### Giao diện header

Header bảng (nền xanh, chữ trắng) do theme global quy định trong
`resources/css/theme.css`, không phải prop của component. Đổi màu bằng
`--app-accent-rgb` ở `:root` — sửa một chỗ, mọi bảng đổi theo.

### Sự kiện

`page-change`, `sort-change`, `selection-change`, `row-click`, và `update:page` / `update:sortKey` / `update:sortOrder` cho `v-model`.

> **Lưu ý khi sort server-side:** controller phải whitelist tên cột.
> Xem `BaiVietController::COT_SAP_XEP`.

---

## AppModal

```vue
<AppModal ref="modalForm" title="Thêm bài viết" size="lg"
          :auto-close="false" @ok="luu">
    <input v-model="form.tieu_de" class="form-control" autofocus>
</AppModal>
```

```js
// API promise, giống LTEModal cũ
const ok = await this.$refs.modalForm.open()

// Gọi API rồi tự đóng (dùng với :auto-close="false")
async luu() {
    this.$refs.modalForm.setBusy(true)
    try {
        await this.$axios.post(...)
        this.$refs.modalForm.close(true)
    } finally {
        this.$refs.modalForm.setBusy(false)
    }
}
```

Hoặc dùng `v-model:show="hienModal"`.

Props: `title, icon, size(sm|md|lg|xl|fullscreen), centered, scrollable, okLabel, okIcon, okVariant, okDisabled, cancelLabel, showOk, showCancel, hideHeader, hideFooter, closable, persistent, autoClose, beforeClose`.

Slot: mặc định, `header`, `footer`, `actions`. Sự kiện: `ok, cancel, shown, hidden`.

`beforeClose(action)` trả `false` để chặn đóng — dùng cho cảnh báo mất dữ liệu:

```js
beforeClose: (action) => action !== 'cancel' || !this.coThayDoi || confirm('Bỏ thay đổi?')
```

---

## AppConfirm

Đặt **một** instance cho mỗi trang, gọi nhiều lần:

```vue
<AppConfirm ref="xacNhan" />
```

```js
const dongY = await this.$refs.xacNhan.open({
    title: 'Xóa bài viết?',
    message: item.tieu_de,
    detail: 'Hành động này không thể hoàn tác.',
    variant: 'danger',      // primary | danger | warning | success
    okLabel: 'Xóa',
})
if (dongY) { … }
```

Gọi nhanh: `await this.$refs.xacNhan.open('Bạn có chắc không?')`.

---

## AppImage

Ảnh tải trễ, link hỏng thì rơi về ảnh mặc định, không có ảnh mặc định thì hiện ô icon.

```vue
<AppImage :src="item.anh_bia" alt="Ảnh bìa" width="120" height="80" />
<AppImage :src="item.anh_dai_dien" fallback="/asset/img/nguoi-dung.png" class="rounded-circle" width="40" height="40" />
```

Thứ tự rơi về: `src` → `fallback` → ô icon. Đổi `src` lúc chạy thì tự chạy lại từ đầu.

| Prop | Mặc định | Ghi chú |
|---|---|---|
| `src` / `alt` | `''` / `''` | `src` rỗng thì hiện luôn ô icon |
| `fallback` | `''` | Ảnh mặc định khi `src` lỗi; ảnh này lỗi nốt thì về ô icon |
| `icon` | `'bi-image'` | Icon của ô thay thế |
| `width` / `height` | `''` | Số hiểu là px. Nên truyền để khung ảnh không nhảy khi tải |
| `fit` | `'cover'` | `object-fit` của ảnh |
| `lazy` | `true` | Gắn `loading="lazy"`; đặt `false` cho ảnh nằm ngay đầu trang |

Bo góc, đổ bóng... đặt class thẳng lên component (`class="rounded-circle"`), khung ngoài
đã `overflow: hidden` nên ảnh bị cắt theo.

---

## Khác biệt so với bản trong git history

- `TablePro` sort/search trên đúng 10 dòng của trang hiện tại trong khi phân trang
  là server-side → kết quả sai. Nay mặc định `sortMode="server"`, phát sự kiện ra
  ngoài để controller sort toàn bảng.
- `<style>` của `TablePro` không `scoped`, override `.table` toàn cục. Nay scoped hết.
- Màu hardcode (`#007bff`, `#fafbfc`) vỡ ở dark mode. Nay dùng biến `--bs-*`.
- Icon FontAwesome và markup AdminLTE 3/BS4 (`data-dismiss`, `.close`,
  `input-group-append`). Nay là bootstrap-icons + Bootstrap 5.
- `highlight()` cũ dùng `v-html` trên dữ liệu DB → rủi ro XSS. Nay tô bằng `<mark>`.
- Modal cũ để `title`/`save` trong `data()`, cha phải gán vào internals của con.
  Nay là props.
- Modal cũ không `dispose()` → rò rỉ instance/backdrop. Nay dispose khi unmount.
- Modal nay `Teleport` ra `body` nên không vỡ khi nằm trong card/table có `overflow`.
- Bổ sung: trạng thái rỗng, `aria-sort`/`scope`/nhãn cho
  screen reader, khôi phục focus sau khi đóng modal.
