---
name: moc-cms
description: Quy ước bắt buộc khi viết code cho mocCMS dùng Laravel 11, Vue 3 Options API và bộ khung tự dựng trên Bootstrap 5.
---

# Quy ước dự án mocCMS

Trước khi tạo hoặc sửa file, đọc file cùng loại đã có và giữ đúng cấu trúc hiện tại. Viết cách xử lý đơn giản nhất, không thêm pattern hoặc thư viện khi chưa được yêu cầu.

Tên biến, hàm, bảng và cột dùng tiếng Việt không dấu. Nhãn và thông báo cho người dùng viết tiếng Việt có dấu. Không viết comment hoặc `try/catch` mặc định.

## Công nghệ

- Backend: Laravel 11, PHP 8.2.
- Frontend: Vue 3 Options API, vue-router.
- Giao diện: bộ khung tự dựng ở `resources/js/components/layout/`, Bootstrap 5.3, Bootstrap Icons.
- Build: Vite qua `laravel-vite-plugin`.
- Không dùng Composition API, `<script setup>`, TypeScript, Inertia, Repository hoặc Service cho nghiệp vụ thông thường.

## Controller

Tên method phải có tiền tố theo HTTP method:

| HTTP | Tiền tố | Ví dụ |
|---|---|---|
| GET | `get` | `getHoSo`, `getHoSoCT` |
| POST | `put` | `putHoSo` |
| PUT | `update` | `updateHoSo` |
| DELETE | `delete` | `deleteHoSo` |

POST dùng tiền tố `put`, không dùng `store`. Route API đặt trong `routes/api.php`, nhóm dưới prefix `/admin`, URL dùng kebab-case tiếng Việt không dấu và luôn đặt tên `<Controller>.<method>`.

```php
Route::group(['prefix' => '/admin'], function () {
    Route::group(['prefix' => '/ho-so'], function () {
        Route::get('/', [HoSoController::class, 'getHoSo'])->name('HoSoController.getHoSo');
        Route::post('/', [HoSoController::class, 'putHoSo'])->name('HoSoController.putHoSo');
        Route::put('/{id}', [HoSoController::class, 'updateHoSo'])->name('HoSoController.updateHoSo');
        Route::delete('/{id}', [HoSoController::class, 'deleteHoSo'])->name('HoSoController.deleteHoSo');
    });
});
```

Thứ tự xử lý: chuẩn hóa input, gom lỗi định dạng, kiểm tra nghiệp vụ, ghi dữ liệu, trả kết quả. Không dùng Form Request hoặc Repository nếu chưa có yêu cầu.

Danh sách phân trang bằng `ITEM_PER_PAGE`, tìm kiếm dùng tham số `s`. Thao tác nhiều bảng dùng `DB::transaction()`.

## Model và cơ sở dữ liệu

- Tên bảng và cột dùng snake_case tiếng Việt không dấu: `ho_so`, `id_ho_so`, `ngay_cap_nhat`.
- Khóa chính đặt `id_<ten_bang>` và khai báo `$primaryKey`.
- Quan hệ dùng camelCase tiếng Việt không dấu: `chiTiet()`, `taiKhoan()`.
- Model không dùng timestamps tự động nếu schema dùng `ngay_tao`, `ngay_cap_nhat`.
- Schema SQL đặt trong `database/sql/`, không tự tạo migration nếu dự án tiếp tục quản lý schema bằng SQL.

## URL chi tiết

Trang và API chi tiết dùng path phân cấp, không dùng query string để định danh bản ghi.

- Đúng: `/ho-so/12`, `/ho-so/12/van-bang/3`.
- Sai: `/ho-so-chi-tiet?id=12`.
- Vue Router ràng buộc id số bằng `:id(\\d+)`.
- Route name dùng dạng `router-hoso-chitiet`.
- Query string chỉ dùng cho phân trang, tìm kiếm và bộ lọc.

## Vue

- Page đặt trong `resources/js/pages/`.
- Tên page là `page<TenChucNang>.vue`; trang chi tiết thêm hậu tố `CT`.
- Dùng Options API theo thứ tự `name`, `components`, `data`, `computed`, `mounted`, `methods`.
- Import nội bộ qua alias `@/`.
- Tên method gọi API trùng tên method Controller.
- Không hardcode URL API khi đã có named route.
- Tên route Vue dùng dạng `router-<khoi><chucnang>`.

```vue
<template>
    <LteAppContent title="Hồ sơ">
        <LteCard title="Danh sách hồ sơ">
            Nội dung
        </LteCard>
    </LteAppContent>
</template>

<script>
export default {
    name: 'pageHoSo',
    data() {
        return {
            tableHoSo: {
                headers: [],
                pagination: {},
                list: [],
            },
            hoSoForm: {
                id_ho_so: '',
                ho_ten: '',
            },
        }
    },
}
</script>
```

## Giao diện

- Layout gốc dùng `AppLayout`, nội dung page dùng `AppContent`, khung có tiêu đề dùng `AppCard`.
- Dùng control có sẵn `AppTable`, `AppModal`, `AppConfirm` trước khi tự tạo component mới.
  Xem `resources/js/components/layout/README.md` và `resources/js/components/controls/README.md`.
- Bố cục dùng grid và utility của Bootstrap 5, không tự viết lại.
- Icon dùng Bootstrap Icons với tiền tố `bi-`.
- Màu, bo góc, kiểu chữ chỉnh bằng biến `--app-*` trong `resources/css/theme.css`,
  không đè selector rải rác từng component.
- Bố cục khung (sidebar, thanh trên, chân trang) nằm ở `resources/css/layout.css`.
- Không dùng jQuery, Bootstrap 4, Font Awesome hoặc asset AdminLTE cũ.

## Cách viết

- Một việc chỉ có một method; không tách tầng không cần thiết.
- Không viết comment, docblock, code cũ dạng comment hoặc TODO chung chung.
- Không bọc query bằng `try/catch` chỉ để đổi thông báo lỗi.
- Không viết `.catch()` ở Vue nếu không có xử lý khôi phục state cụ thể.
- Không thêm cache, queue, log, retry hoặc cấu hình chưa được yêu cầu.

## Checklist

- Controller đúng tiền tố `get`, `put`, `update`, `delete`.
- Route API có tên `<Controller>.<method>` và URL kebab-case.
- URL chi tiết dùng path phân cấp.
- Page dùng Options API và đúng tiền tố `page`.
- Giao diện dùng component trong `components/layout`, `components/controls` và Bootstrap 5.
- Không còn dependency hoặc markup AdminLTE.
- Không có comment và `try/catch` thừa.
