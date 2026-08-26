---
name: hoso-sinhvien-vlute
description: Quy ước bắt buộc khi viết code cho dự án hoso-sinhvien.vlute.edu.vn (Laravel 11 + Vue 3 Options API + AdminLTE). Đọc trước khi tạo/sửa Controller, Model, route, page Vue hoặc component. Gồm quy tắc đặt tên get/put/update/delete, URL phân cấp /{id}/..., style giao diện LTE*, cấu trúc Vue theo mẫu có sẵn, và ba ràng buộc về cách viết: đơn giản nhất, không comment, không try/catch mặc định.
---

# Quy ước dự án hoso-sinhvien.vlute.edu.vn

> **Nguyên tắc số 1: KHÔNG viết lung tung.**
> Trước khi tạo bất kỳ file nào, mở file cùng loại đã có, đọc và bắt chước đúng cấu trúc đó.
> Không tự ý đưa vào thư viện, pattern, hay kiến trúc mới (Form Request, Resource, Repository,
> Composition API, Inertia, TypeScript, service layer...) nếu dự án chưa dùng.
> Mọi thứ viết bằng **tiếng Việt không dấu cho tên biến/hàm**, **tiếng Việt có dấu cho message**.

> **Nguyên tắc số 2: Viết cách xử lý ĐƠN GIẢN NHẤT chạy đúng.**
> Ít dòng, ít tầng, ít trừu tượng. Không tách hàm/lớp/hằng số khi chỉ dùng một chỗ.
> Không thêm cấu hình, tham số, hay nhánh xử lý cho tình huống chưa ai yêu cầu.
> Làm đúng phạm vi được giao, không "làm sẵn cho sau này".

> **Nguyên tắc số 3: KHÔNG viết comment.**
> Code phải tự đọc được qua tên biến và tên hàm. Chỉ viết comment khi đoạn logic thật sự phức tạp
> (thuật toán khó, quy tắc nghiệp vụ ngầm, workaround) — khi đó viết **một dòng ngắn giải thích *tại sao*,
> không mô tả lại code đang làm gì**. Không viết docblock `/** */` cho method/property thông thường,
> không đánh số bước, không comment tiêu đề chia khối, không để lại code cũ dạng comment.

> **Nguyên tắc số 4: KHÔNG bọc `try/catch` mặc định — chấp nhận để lỗi nổ ra.**
> Lỗi hệ thống (DB, S3, mạng) cứ để Laravel/Sentry bắt. `try/catch` chỉ dùng khi **thật sự
> xử lý được lỗi đó** (rollback thủ công, fallback có ý nghĩa), không dùng chỉ để đổi lỗi
> thành `Response::Error('Lỗi hệ thống', $e->getMessage())`.

## 1. Ngăn xếp & file tham chiếu

| Thành phần | Công nghệ | File mẫu phải đọc trước khi làm |
|---|---|---|
| Backend | Laravel 11, PHP 8.2 | `app/Http/Controllers/QuyenController.php` |
| Model | Eloquent, `$timestamps = false` | `app/Models/Quyen.php` |
| Route API | `routes/api.php`, có `->name()` | `routes/api.php` |
| Response | `App\Response::Success/Error` | `app/Response.php` |
| Frontend | Vue 3 **Options API**, vue-router, Ziggy | `resources/js/pages/pageQuyen.vue` |
| Trang chi tiết | route có `:id` | `resources/js/pages/pageQuyenNhomCT.vue` |
| Giao diện | AdminLTE 3 + Bootstrap 4 + Font Awesome | `resources/js/components/controls/` |
| Tiện ích | `$func`, `$axios`, `$showView`, `$store` | `resources/js/utils/` |

Không dùng Tailwind cho trang nghiệp vụ (dù có trong package.json) — toàn bộ UI chạy theo AdminLTE.

---

## 2. Quy tắc đặt tên Controller — tiền tố theo HTTP method

**Bắt buộc.** Tên method trong Controller phải mang tiền tố đúng với HTTP method của route:

| HTTP | Tiền tố method | Ý nghĩa | Ví dụ |
|---|---|---|---|
| `GET` | `get...` | Lấy danh sách / chi tiết | `getQuyen`, `getQuyenCT`, `getDsHanhDong` |
| `POST` | `put...` | **Thêm mới** | `putQuyen`, `putQuyenCT`, `putQuyenNhomTK` |
| `PUT` | `update...` | Cập nhật | `updateQuyen`, `updateQuyenCT` |
| `DELETE` | `delete...` | Xóa | `deleteQuyen`, `deleteQuyenCT` |

Lưu ý: **POST = `put...`** (thêm mới), **PUT = `update...`** (sửa). Đây là quy ước riêng của dự án,
không được đổi sang `store/update/destroy` kiểu resource controller của Laravel.

Hành động đặc biệt (không phải CRUD) thì đặt tên động từ tiếng Việt không dấu:
`dongBoQuyenNhomCT`, `getDsTaiKhoanChon`.

### 2.1 Khai báo route

Nhóm theo `prefix`, luôn có `->name('TenController.tenMethod')` để Ziggy sinh ra `route()` cho Vue:

```php
Route::group(['prefix' => '/admin', 'middleware' => ['isQuyen']], function () {
    Route::group(['prefix' => '/ho-so'], function () {
        Route::get('/',    [HoSoController::class, 'getHoSo'])->name('HoSoController.getHoSo');
        Route::post('/',   [HoSoController::class, 'putHoSo'])->name('HoSoController.putHoSo');
        Route::put('/',    [HoSoController::class, 'updateHoSo'])->name('HoSoController.updateHoSo');
        Route::delete('/', [HoSoController::class, 'deleteHoSo'])->name('HoSoController.deleteHoSo');
    });
});
```

- Prefix URL viết **kebab-case tiếng Việt không dấu**: `/quyen-nhom`, `/nhat-ky`, `/ho-so`.
- Tên route: `<TênController>.<tênMethod>` — không đặt kiểu `admin.hoso.index`.
- API nghiệp vụ đặt trong `routes/api.php` dưới `prefix /admin` + middleware `isQuyen`.
- `routes/web.php` chỉ có SSO và catch-all `{any}` trả SPA — **không thêm route nghiệp vụ vào đây.**

### 2.2 Khung sườn method Controller

Mỗi method viết theo đúng trình tự sau — không comment chia bước, không `try/catch`:

```php
public function putHoSo(Request $request)
{
    $ten = trim($request->ten ?? '');

    $errors = [];
    if (empty($ten)) {
        $errors[] = 'Tên không được bỏ trống';
    }
    if ($errors) {
        return Response::Error('Sai định dạng dữ liệu', $errors);
    }

    if (HoSo::where('ten', $ten)->exists()) {
        return Response::Error('Trùng dữ liệu', 'Tên đã tồn tại');
    }

    $hoSo = HoSo::create([
        'ten'           => $ten,
        'ngay_tao'      => now(),
        'ngay_cap_nhat' => now(),
    ]);

    return Response::Success(['id_ho_so' => $hoSo->id_ho_so], 'Thêm hồ sơ thành công');
}
```

Trình tự cố định: chuẩn hóa input → gom `$errors` định dạng → kiểm tra nghiệp vụ → ghi dữ liệu → trả kết quả.

Quy định kèm theo:

- **Luôn** trả về `Response::Success($data, $message)` hoặc `Response::Error($message, $errors)`.
  Không `return response()->json(...)` trực tiếp, không `abort()`.
- `Response::Error` chỉ dùng cho **lỗi nghiệp vụ đoán trước được** (thiếu dữ liệu, trùng, không tìm thấy).
  Lỗi hệ thống để nó nổ, không bắt lại.
- **Không** dùng `$request->validate()` / FormRequest — validate thủ công, gom `$errors` như trên.
- Danh sách luôn phân trang: `$perPage = intval(env('ITEM_PER_PAGE', 10));` và `->paginate($perPage)`.
- Tìm kiếm dùng tham số `s`: `$keyword = $request->input('s', '');` + `->when($keyword, fn($q) => ...)`.
- Cập nhật/xóa: kiểm tra số dòng bị ảnh hưởng, nếu `0` thì `Response::Error('Lỗi', 'Không tìm thấy ...')`.
- Tự tay set `ngay_tao` / `ngay_cap_nhat` bằng `now()` vì model tắt `$timestamps`.
- Không viết docblock mô tả method. Tên method đã nói rõ nó làm gì.

### 2.2.1 Thao tác nhiều bảng

Dùng closure `DB::transaction()` — tự rollback khi có exception, không cần `try/catch`:

```php
public function deleteHoSo(Request $request, $id)
{
    DB::transaction(function () use ($id) {
        HoSoChiTiet::where('id_ho_so', $id)->delete();
        HoSo::where('id_ho_so', $id)->delete();
    });

    return Response::Success('Thành công', 'Xóa hồ sơ thành công');
}
```

Không dùng bộ ba `beginTransaction()` / `commit()` / `rollBack()` thủ công cho code mới
(các controller cũ còn viết kiểu đó thì để nguyên).

### 2.3 Model

```php
class HoSo extends Model
{
    use HasFactory;
    protected $table = 'ho_so';
    protected $primaryKey = 'id_ho_so';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = ['ten', 'ngay_tao', 'ngay_cap_nhat'];

    public function chiTiet()
    {
        return $this->hasMany(HoSoChiTiet::class, 'id_ho_so', 'id_ho_so');
    }
}
```

- Tên bảng & cột: **snake_case tiếng Việt không dấu** (`ho_so`, `id_ho_so`, `ngay_cap_nhat`).
- Khóa chính luôn `id_<ten_bang>`, khai báo tường minh `$primaryKey`.
- Quan hệ đặt tên **camelCase tiếng Việt không dấu**: `chiTiet()`, `taiKhoan()`.
- Dự án **không dùng migration** (`database/migrations` rỗng); schema nằm ở `database/sql/*.sql`.
  Khi cần bảng mới thì bổ sung file `.sql` ở đó, không tự sinh migration.

---

## 3. URL phân cấp — trang chi tiết dùng `/{id}/...`, KHÔNG dùng query param

Đây là quy định bắt buộc cho **mọi trang chi tiết mới**.

### 3.1 Router phía Vue (`resources/js/routers.js`)

```js
{
    path: "ho-so",
    name: "router-hoso",
    component: () => import("@/pages/pageHoSo.vue"),
    meta: { title: 'Hồ sơ sinh viên' }
},
{
    path: "ho-so/:id(\\d+)",
    name: "router-hoso-chitiet",
    component: () => import("@/pages/pageHoSoCT.vue"),
    meta: { title: 'Chi tiết hồ sơ' }
},
{
    path: "ho-so/:id(\\d+)/van-bang",
    name: "router-hoso-vanbang",
    component: () => import("@/pages/pageHoSoVanBang.vue"),
    meta: { title: 'Văn bằng của hồ sơ' }
}
```

- ĐÚNG: `/ho-so/12`, `/ho-so/12/van-bang`, `/ho-so/12/van-bang/3`
- SAI: `/ho-so-chi-tiet?id=12`, `/ho-so?id_ho_so=12&tab=van-bang`
- Ràng buộc số: `:id(\\d+)`.
- `name` route đặt dạng `router-<khối><chức năng>` viết liền, không dấu: `router-hoso-chitiet`.
- Trang con đọc id bằng `this.$route.params.id` **trong `data()`**, giống `pageQuyenNhomCT.vue`:

```js
data() {
    return {
        id_ho_so: this.$route.params.id,
        ...
    };
}
```

- Điều hướng sang trang chi tiết bằng `name` + `params`, không ghép chuỗi query:

```js
this.$router.push({ name: 'router-hoso-chitiet', params: { id: item.raw.id_ho_so } });
```

### 3.2 API cho tài nguyên con

API mới của trang chi tiết cũng phân cấp theo path, id nằm trong URL:

```php
Route::group(['prefix' => '/ho-so'], function () {
    Route::get('/',            [HoSoController::class, 'getHoSo'])->name('HoSoController.getHoSo');
    Route::get('/{id}',        [HoSoController::class, 'getHoSoCT'])->name('HoSoController.getHoSoCT');
    Route::get('/{id}/van-bang',    [HoSoController::class, 'getVanBang'])->name('HoSoController.getVanBang');
    Route::post('/{id}/van-bang',   [HoSoController::class, 'putVanBang'])->name('HoSoController.putVanBang');
    Route::put('/{id}/van-bang/{idVanBang}',    [HoSoController::class, 'updateVanBang'])->name('HoSoController.updateVanBang');
    Route::delete('/{id}/van-bang/{idVanBang}', [HoSoController::class, 'deleteVanBang'])->name('HoSoController.deleteVanBang');
});
```

Method nhận id qua tham số, không lấy từ `$request`:

```php
public function getVanBang(Request $request, $id) { ... }
```

Bên Vue sinh URL bằng Ziggy có tham số:

```js
this.$axios.get(route('HoSoController.getVanBang', { id: this.id_ho_so }));
```

**Query string chỉ được dùng cho:** phân trang (`?page=`), tìm kiếm (`?s=`), và bộ lọc.
Không dùng query string để định danh bản ghi.

> Các endpoint cũ (`/quyen/chi-tiet?id_quyen=...`) giữ nguyên để không vỡ màn hình hiện có;
> chỉ khi được yêu cầu refactor mới chuyển sang dạng phân cấp. Code mới bắt buộc theo mục này.

---

## 4. Style thiết kế giao diện

Giao diện chạy AdminLTE 3 (`public/themes/css/lte.min.css`) + Bootstrap 4 + Font Awesome 5.
**Tuyệt đối không tự viết layout mới, không thêm CSS framework, không đổi bảng màu.**

### 4.1 Bộ component bắt buộc dùng lại

| Component | Dùng cho |
|---|---|
| `LTEContentWrapper` | Khung ngoài cùng của mọi page (slot `#content`) |
| `LTECardAddButton` | Card có tiêu đề + nút hành động (slot `#button`, `#content`) |
| `LTECard` | Card thường |
| `LTETableV2` | Bảng dữ liệu + phân trang + sắp xếp + slot `#actions` |
| `LTEModal` | Modal thêm/sửa (mở bằng `await this.$refs.x.openModal()`) |
| `YesNoModal` | Hộp thoại xác nhận trước khi xóa |
| `LTEInput`, `LTETextArea`, `LTESelectOption`, `Select2`, `LTECombobox` | Ô nhập liệu |
| `LTEModalUpload`, `LTEModalSelectUser` | Upload file, chọn người dùng |
| `LoadingSpinner` | Trạng thái chờ |

Cần control mới thì thêm vào `resources/js/components/controls/`, đặt tên tiền tố `LTE`,
và đăng ký trong `resources/js/components/Registers.js` nếu dùng ở nhiều nơi.
**Không** viết `<table>` thủ công khi `LTETableV2` đáp ứng được.

### 4.2 Khung một page chuẩn

```vue
<template>
    <LTEContentWrapper>
        <template #content>
            <div class="row">
                <div class="col-12">
                    <LTECardAddButton title="Hồ sơ sinh viên">
                        <template #button>
                            <button v-if="$showView('pageHoSo.them')" type="button" class="btn btn-primary" @click="themMoi">
                                <i class="fas fa-plus-circle"></i>  Thêm mới
                            </button>
                        </template>

                        <template #content>
                            <div class="row mb-3">
                                <div class="col-4"></div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control float-right" name="search"
                                               @keyup.enter="getHoSo(route('HoSoController.getHoSo'))"
                                               placeholder="Nhập từ khóa tìm kiếm và nhấn Enter ...">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor: pointer;"
                                                  @click="getHoSo(route('HoSoController.getHoSo'))">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <LTETableV2
                                :data="tableHoSo.list"
                                :headers="tableHoSo.headers"
                                :pagination="tableHoSo.pagination"
                                :fetchPage="getHoSo">
                                <template #actions="{ item }">
                                    <i v-if="$showView('pageHoSo.sua')" @click="chinhSua(item)" class="fas fa-edit icon-edit"></i>
                                    <i v-if="$showView('pageHoSo.xoa')" @click="deleteHoSo(item)" class="fas fa-trash icon-delete"></i>
                                </template>
                            </LTETableV2>
                        </template>
                    </LTECardAddButton>
                </div>
            </div>
        </template>
    </LTEContentWrapper>

    <YesNoModal ref="confirm"></YesNoModal>
    <LTEModal ref="mdThem"> ... </LTEModal>
</template>
```

### 4.3 Quy ước trình bày

- Bố cục bằng grid Bootstrap 4: `row` / `col-12` / `col-6` / `col-4`. Không dùng flex/grid CSS tự chế.
- Nút: `btn btn-primary` (hành động chính), `btn btn-default` (hủy), `btn btn-danger` (xóa),
  `btn btn-sm` trong modal/alert. Nút luôn có icon Font Awesome đứng trước nhãn.
- Icon hành động trong bảng: `<i class="fas fa-edit icon-edit">`, `<i class="fas fa-trash icon-delete">`
  kèm block `<style scoped>` icon tròn như `pageQuyen.vue`.
- Khoảng cách dùng lớp tiện ích Bootstrap: `mb-3`, `ml-2`, `mt-2` — không viết `style="margin..."`.
- Trạng thái/nhãn dùng `badge badge-<color>`; màu trạng thái lấy từ `store.js`, không hardcode.
- Cảnh báo trong modal dùng `alert alert-danger d-flex justify-content-between align-items-center`.
- Chỉ dùng `<style scoped>` cho tinh chỉnh nhỏ của riêng page; style dùng chung để ở
  `public/themes/css/global.css`.
- Tiêu đề trang đặt trong `meta.title` của route (router tự set `document.title`).
- Toàn bộ nhãn, placeholder, thông báo viết **tiếng Việt có dấu**.

---

## 5. Quy ước Vue (Options API)

### 5.1 Bắt buộc

- **Options API** (`export default { components, data, computed, mounted, methods }`).
  Không dùng `<script setup>` / Composition API.
- Page đặt tại `resources/js/pages/`, tên file `page<TênChứcNăng>.vue` (PascalCase sau `page`),
  trang chi tiết thêm hậu tố `CT`: `pageHoSo.vue`, `pageHoSoCT.vue`.
- Import component tường minh ở đầu `<script>` bằng alias `@/` kể cả khi đã đăng ký global
  (giữ nguyên thói quen của `pageQuyen.vue`).
- Gọi API bằng `this.$axios` + `route('Controller.method')` của Ziggy. **Không hardcode URL chuỗi.**
- Không dùng Vuex; Pinia đã cài nhưng dữ liệu tĩnh dùng chung để trong `resources/js/utils/store.js`.

### 5.2 Cấu trúc `data()`

State bảng gom thành object `table<Tên>` với đúng 3 khóa:

```js
tableHoSo: {
    headers: [
        { key: 'ho_ten', label: 'Họ tên', css: 'width: 400px' },
        { key: 'ngay_cap_nhat', label: 'Ngày cập nhật', css: 'width: 200px' }
    ],
    pagination: {},
    list: []
}
```

State form gom thành object `<tên>Form` với đầy đủ khóa khởi tạo rỗng.

### 5.3 Mẫu gọi API

Tên method Vue **trùng tên method Controller** (`getHoSo`, `putHoSo`, `updateHoSo`, `deleteHoSo`).
`get...` nhận tham số `url` để `LTETableV2` gọi lại được khi chuyển trang.

```js
mounted() {
    this.getHoSo(route('HoSoController.getHoSo'));
},
methods: {
    getHoSo(url) {
        const field = document.querySelector("input[name=search]").value || '';
        const params = { s: field };
        this.$axios.get(url, { params }).then((response) => {
            this.tableHoSo.list = [];
            const rData = response.data.data;
            rData.data.forEach((item) => {
                this.tableHoSo.list.push({
                    raw: item,
                    ho_ten: item.ho_ten,
                    ngay_cap_nhat: this.$func.formatDate(item.ngay_cap_nhat)
                });
            });
            this.tableHoSo.pagination = rData;
        });
    },

    async themMoi() {
        this.hoSoForm = { id_ho_so: '', ho_ten: '' };
        this.$refs.mdThem.$data.title = 'Thêm mới hồ sơ';
        this.$refs.mdThem.$data.save = 'Lưu thông tin';
        const result = await this.$refs.mdThem.openModal();
        if (!result) return;
        await this.putHoSo();
    },

    async putHoSo() {
        const params = { ho_ten: this.hoSoForm.ho_ten };
        await this.$axios.post(route('HoSoController.putHoSo'), params).then((response) => {
            if (response.data.status === 200) {
                this.getHoSo(route('HoSoController.getHoSo'));
                this.$func.toastSuccess(response.data.message);
                this.$refs.mdThem.closeModal();
            } else {
                this.$func.toastError(response.data);
            }
        });
    },

    async deleteHoSo(item) {
        const result = await this.$refs.confirm.openModal();
        if (!result) return;
        this.$axios.delete(route('HoSoController.deleteHoSo', { id: item.raw.id_ho_so }))
            .then((response) => {
                if (response.data.status === 200) {
                    this.getHoSo(route('HoSoController.getHoSo'));
                    this.$func.toastSuccess(response.data.message);
                } else {
                    this.$func.toastError(response.data);
                }
            });
    }
}
```

Ghi nhớ:

- Mỗi dòng trong `list` giữ bản gốc ở khóa `raw`, các khóa còn lại là chuỗi đã format để hiển thị.
- Luôn kiểm tra `response.data.status === 200`; nhánh còn lại gọi `this.$func.toastError(response.data)`.
- **Không viết `.catch()`** chỉ để toast lỗi chung — `utils/axios.js` đã bắt lỗi HTTP tập trung.
  Chỉ thêm `.catch()` khi thực sự cần rollback/đóng modal.
- Sau khi thêm/sửa/xóa thành công: nạp lại danh sách → toast → đóng modal.
- Ngày giờ format qua `this.$func.formatDate(...)` / `this.$func.fromNow(...)`, không dùng moment trực tiếp.

### 5.4 Phân quyền hiển thị (`$showView`)

- Mọi nút/hành động nhạy cảm bọc `v-if="$showView('<pageX>.<hanhdong>')"`.
- Mã view đặt dạng `page<Tên>.<hanhdong>` (`pageHoSo.them`, `pageHoSo.sua`, `pageHoSo.xoa`),
  menu dùng `menu<Tên>.xem`.
- Backend chặn thật ở middleware `isQuyen` theo `funcs` = `Controller.method`;
  `$showView` chỉ ẩn/hiện giao diện, **không được coi là biện pháp bảo mật**.
- Thêm chức năng mới thì khai báo `funcs` và `show_views` tương ứng trong màn hình Nhóm quyền.

### 5.5 Menu

Mục menu mới thêm vào mảng `menu` trong `resources/js/components/themes/LTESidebar.vue`,
đúng nhóm `header`, kèm `view`, `route`, `icon`, `label`.

---

## 6. Đơn giản hóa & không comment

### 6.1 Viết đơn giản nhất

- Một việc → một method. Không tách helper/trait/service nếu chỉ gọi ở một nơi.
- Không tạo hằng số, enum, config cho giá trị chỉ dùng một lần.
- Dùng thẳng Eloquent/Query Builder trong Controller — dự án không có repository, không có service layer
  (chỉ `S3Services` cho việc đẩy file lên S3).
- Không viết thêm tham số, cờ bật/tắt, hay nhánh `if` cho trường hợp chưa được yêu cầu.
- Không tự thêm cache, queue, log, retry nếu không có trong yêu cầu.
- Bên Vue: không tách computed/watch khi gán thẳng là đủ; không bọc `$axios` thêm một lớp wrapper.
- Sửa lỗi thì sửa đúng chỗ, không tiện tay refactor file xung quanh.

### 6.2 Không comment

- Mặc định **không viết comment nào**, kể cả docblock `/** */` và JSDoc.
- Chỉ được viết comment khi logic thực sự khó hiểu: thuật toán rắc rối, quy tắc nghiệp vụ ngầm,
  workaround cho lỗi thư viện. Viết **một dòng, giải thích lý do**, ví dụ:

```php
// Xóa chi tiết trước vì FK không có ON DELETE CASCADE
```

- Cấm các kiểu comment sau: mô tả lại code (`// Lấy danh sách`), đánh số bước (`// 1. ...`),
  tiêu đề chia khối (`// ===== FORM =====`), code cũ để lại dạng comment, `TODO` không có ngữ cảnh.
- Đặt tên rõ ràng thay cho comment: `dsQuyenChuaGan` tốt hơn `$ds` kèm một dòng giải thích.

### 6.3 Không try/catch mặc định

- **Không** bọc `try/catch` quanh `create/update/delete/query` chỉ để trả `Response::Error('Lỗi hệ thống', ...)`.
  Lỗi cứ để nổ, Sentry đã cấu hình sẵn trong `main.js` và `sentry-laravel`.
- Nhiều bảng thì dùng `DB::transaction(function () { ... })` — nó tự rollback, không cần `try/catch`.
- `try/catch` chỉ hợp lệ khi trong `catch` có hành động thật: dọn file tạm, gọi nguồn dự phòng,
  chuyển sang giá trị mặc định có ý nghĩa.
- Bên Vue: **không viết `.catch()`** — `resources/js/utils/axios.js` đã bắt lỗi HTTP tập trung và toast.
  Chỉ thêm `.catch()` khi cần rollback state hoặc đóng modal.
- Lỗi nghiệp vụ (thiếu dữ liệu, trùng, không tìm thấy) vẫn kiểm tra bằng `if` và trả `Response::Error`
  như bình thường — đây không phải ngoại lệ.

---

## 7. Checklist trước khi báo hoàn thành

- [ ] Method Controller đúng tiền tố: `get` / `put` (POST) / `update` (PUT) / `delete` (DELETE).
- [ ] Route có `->name('Controller.method')`, nằm trong `routes/api.php`, prefix kebab-case.
- [ ] Trang chi tiết dùng URL phân cấp `/{id}/...`, không định danh bằng query param.
- [ ] Trả về qua `Response::Success` / `Response::Error`, validate thủ công gom `$errors`.
- [ ] Danh sách có phân trang `ITEM_PER_PAGE` và tìm kiếm bằng `s`.
- [ ] Thao tác nhiều bảng có transaction.
- [ ] Page Vue dùng Options API, `LTEContentWrapper` + `LTECardAddButton` + `LTETableV2`.
- [ ] Gọi API qua `route()` của Ziggy, kiểm tra `status === 200`, toast bằng `$func`.
- [ ] Nút/hành động có `$showView`, khai báo `funcs` + `show_views` tương ứng.
- [ ] Không thêm thư viện, không đổi bảng màu, không tạo pattern mới ngoài mô tả trên.
- [ ] Không có comment thừa (chỉ giữ comment giải thích *tại sao* cho đoạn thật sự phức tạp).
- [ ] Không có `try/catch` / `.catch()` mặc định; nhiều bảng thì dùng `DB::transaction()`.
- [ ] Đã chọn cách viết đơn giản nhất: không tách hàm/lớp thừa, không thêm nhánh cho việc chưa ai yêu cầu.
