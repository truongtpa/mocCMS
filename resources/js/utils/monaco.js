import * as monaco from 'monaco-editor'
import JSON5 from 'json5'
import func from './func'
if (typeof window !== 'undefined') {
    window.MonacoEnvironment = {
        getWorker(_moduleId, label) {
            return {
                then: () => {},
                postMessage: () => {},
                terminate: () => {},
                onmessage: null,
            }
        },
    }
    // window.MonacoEnvironment = {
    //     getWorker(_moduleId, label) {
    //         return new Worker(URL.createObjectURL(new Blob([`onmessage = () => {}`])))
    //     },
    // }
}

monaco.editor.defineTheme('vs-light-override', {
    base: 'vs',
    inherit: true,
    rules: [
        { token: 'regexpc', foreground: 'ff0000' }, //a626a4
        // { token: 'keyword', foreground: 'af00db' },
        { token: 'myFunc', foreground: 'af00db' },
        { token: 'variable', foreground: '007acc' },
    ],
    colors: {
        'editor.background': '#ffffff',
        'editor.foreground': '#000000',
    },
})

monaco.editor.setTheme('vs-light-override')

monaco.languages.registerCompletionItemProvider('php', {
    provideCompletionItems: function (model, position) {
        const suggestions = [
            {
                label: 'if',
                kind: monaco.languages.CompletionItemKind.Snippet,
                insertText: 'if ($1) {\n\t$2\n}',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Câu điều kiện if',
            },
            {
                label: 'in_array',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'in_array($item, $array)',
                documentation: 'Lọc phần tử của mảng theo điều kiện',
            },
            {
                label: 'array_filter',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'array_filter($array, function ($item) {\n\treturn $item;\n});',
                documentation: 'Lọc phần tử của mảng theo điều kiện',
            },
            {
                label: 'array_map',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'array_map(function ($item) {\n\treturn $1;\n}, $array);',
                documentation: 'Biến đổi mảng bằng hàm callback',
            },
            {
                label: 'array_reduce',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'array_reduce($array, function ($carry, $item) {\n\treturn $1;\n}, $2);',
                documentation: 'Tính toán kết quả tích lũy từ mảng',
            },
            {
                label: 'isset',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'isset($1)',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Kiểm tra biến có tồn tại hay không',
            },
            {
                label: 'empty',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'empty($1)',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Kiểm tra giá trị rỗng',
            },
            {
                label: '$xepLoaiHPTheoNienKhoa',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: '\\$xepLoaiHPTheoNienKhoa($1)',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Hàm dùng để xếp loại học phần theo năm học',
                detail: 'function $xepLoaiHPTheoNienKhoa(float: $dtb): (object) Xếp loại',
            },
            {
                label: '$dtb',
                kind: monaco.languages.CompletionItemKind.Variable,
                insertText: '$dtb',
            },
            {
                label: '$dtbCaiThien',
                kind: monaco.languages.CompletionItemKind.Variable,
                insertText: '$dtbCaiThien',
            },
            {
                label: '$coCaiThien',
                kind: monaco.languages.CompletionItemKind.Variable,
                insertText: '$coCaiThien',
            },
            {
                label: '$hocPhan',
                kind: monaco.languages.CompletionItemKind.Variable,
                insertText: '$hocPhan',
            },
            {
                label: '$nienKhoa',
                kind: monaco.languages.CompletionItemKind.Variable,
                insertText: '$nienKhoa',
            },
            {
                label: 'function',
                kind: monaco.languages.CompletionItemKind.Function,
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                insertText: `function(\\$dtb, \\$dtbCaiThien, \\$coCaiThien, \\$hocPhan,  \\$nienKhoa) use(\\$xepLoaiHPTheoNienKhoa) {
    \\$hoc_phan_xl = ['SP1412', 'SP1413', 'SP1416', 'SP1418', 'SP1419', 'SP1420', 'SP1421', 'SP1422' ];
    if (in_array(\\$hocPhan->ma_hoc_phan, \\$hoc_phan_xl) || preg_match('/^TC.{4}$/', \\$hocPhan->ma_hoc_phan)) {
        $1
    }

    if (\\$coCaiThien) {

    } 

    return $xepLoaiHPTheoNienKhoa(\\$dtb);
}`,
            },
            {
                label: 'preg_match',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: "preg_match('/pattern/', $subject)",
                documentation: 'So khớp chuỗi với biểu thức chính quy.',
            },
            {
                label: 'array_merge',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'array_merge($array1, $array2)',
                documentation: 'Gộp nhiều mảng lại với nhau.',
            },
            {
                label: 'strlen',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'strlen($string)',
                documentation: 'Trả về độ dài chuỗi.',
            },
            {
                label: 'strpos',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'strpos($haystack, $needle)',
                documentation: 'Tìm vị trí đầu tiên của chuỗi con.',
            },
            {
                label: 'substr',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'substr($string, $start, $length)',
                documentation: 'Cắt chuỗi theo vị trí.',
            },
            {
                label: 'explode',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'explode($delimiter, $string)',
                documentation: 'Tách chuỗi thành mảng.',
            },
            {
                label: 'implode',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'implode($glue, $array)',
                documentation: 'Nối mảng thành chuỗi.',
            },
            {
                label: 'is_array',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'is_array($value)',
                documentation: 'Kiểm tra biến có phải là mảng không.',
            },
            {
                label: 'json_encode',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'json_encode($data)',
                documentation: 'Chuyển mảng/đối tượng PHP thành JSON.',
            },
            {
                label: 'json_decode',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'json_decode($json, true)',
                documentation: 'Chuyển JSON thành mảng/đối tượng PHP.',
            },
            {
                label: 'min',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'min($array)',
                documentation: 'Trả về giá trị nhỏ nhất.',
            },
            {
                label: 'max',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'max($array)',
                documentation: 'Trả về giá trị lớn nhất.',
            },
        ]

        return { suggestions }
    },
})
const schema = {
    chuc_danh: ['id_chuc_danh', 'ten_hang_chuc_danh', 'vi_tri_viec_lam'],
    chuc_vu: ['id_chuc_vu', 'ten_chuc_vu'],
    chung_chi: ['id_chung_chi', 'ten_chung_chi', 'mo_ta', 'ngay_tao', 'ngay_cap_nhat', 'ma_chung_chi', 'loai'],
    chung_chi_sinh_vien_dot: [
        'id_chung_chi_sinh_vien_dot',
        'id_chung_chi',
        'ma_dot',
        'ten_dot',
        'ngay_cap',
        'ngay_ky',
        'nguoi_ky',
        'so_quyet_dinh',
        'ngay_tao',
        'ngay_cap_nhat',
        'ghi_chu',
    ],
    chung_chi_sinh_vien: [
        'id_chung_chi_sinh_vien',
        'id_chung_chi_sinh_vien_dot',
        'cccd',
        'ho_ten',
        'ngay_sinh',
        'noi_sinh',
        'gioi_tinh',
        'so_hieu',
        'so_vao_so',
        'ngay_tao',
        'ngay_cap_nhat',
        'ghi_chu',
        'ky_nhan',
    ],
    chuong_trinh_dt_chung_chi: [
        'id_chuong_trinh_dt_chung_chi',
        'id_chuong_trinh_dt_chuyen_nganh',
        'id_chung_chi',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    chuong_trinh_dt_loai_hinh_dt: [
        'id_chuong_trinh_dt_loai_hinh_dt',
        'ma_loai_hinh_dt',
        'ten_loai_hinh_dt',
        'mo_ta',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    dan_toc: ['id_dan_toc', 'dan_toc', 'ngay_tao', 'ngay_cap_nhat'],
    de_xuat_hp: ['id_de_xuat_hp', 'ten_dot', 'id_hocky_namhoc', 'ngay_tao', 'ngay_cap_nhat'],
    don_vi: ['id_don_vi', 'ma_don_vi', 'ten_don_vi', 'gioi_thieu', 'ngay_tao', 'ngay_cap_nhat'],
    giang_vien: [
        'id_giang_vien',
        'id_don_vi',
        'id_chuc_vu',
        'id_hoc_vi',
        'ho_ten',
        'email',
        'sdt',
        'dia_chi',
        'anh',
        'trang_thai',
        'ngay_sinh',
        'noi_sinh',
        'ghi_chu',
        'ngay_tao',
        'ngay_cap_nhat',
        'cccd',
        'gioi_tinh',
        'id_chuc_danh',
    ],
    bao_cao: [
        'id_bao_cao',
        'tieu_de',
        'cau_truy_van',
        'parm',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_giang_vien',
        'danh_muc',
    ],
    giang_vien_login: [
        'id_giang_vien_login',
        'id_giang_vien',
        'device_id',
        'device_name',
        'platform',
        'app_version',
        'ip_address',
        'login_at',
        'is_success',
    ],
    he_dao_tao: ['id_he_dao_tao', 'ma_he_dao_tao', 'ten_he_dao_tao', 'ngay_tao', 'ngay_cap_nhat'],
    hinh_thuc_thi: ['id_hinh_thuc_thi', 'ma_hinh_thuc_thi', 'ten_hinh_thuc_thi', 'ngay_tao', 'ngay_cap_nhat'],
    hoc_phan: [
        'id_hoc_phan',
        'ma_hoc_phan',
        'ten_hoc_phan',
        'tin_chi_lt',
        'tin_chi_th',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_don_vi',
    ],
    chuong_trinh_dt_hoc_phan: [
        'id_chuong_trinh_dt_hoc_phan',
        'id_hoc_phan',
        'bat_buoc',
        'tich_luy',
        'nhom_lua_chon',
        'ngay_tao',
        'ngay_cap_nhat',
        'nhom',
        'path_s3',
        'id_chuong_trinh_dt_chuyen_nganh',
        'id_hinh_thuc_thi',
        'ty_le_truc_tuyen',
    ],
    chuong_trinh_dt_hoc_phan_mon_tquyet: [
        'id_chuong_trinh_dt_hoc_phan_mon_tquyet',
        'id_chuong_trinh_dt_hoc_phan',
        'id_hoc_phan',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    chuong_trinh_dt_mon_thay_the: [
        'id_chuong_trinh_dt_mon_thay_the',
        'id_chuong_trinh_dt',
        'id_hoc_phan',
        'id_hoc_phan_thay_the',
        'ghi_chu',
        'nhom_lua_chon',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    de_xuat_hp_ngoai_ctdt: [
        'id_de_xuat_hp_ngoai_ctdt',
        'id_hoc_phan',
        'id_de_xuat_hp',
        'ghi_chu',
        'ngay_tao',
        'ngay_cap_nhat',
        'cho_dk_khong_can_dxuat',
    ],
    hoc_vi: ['id_hoc_vi', 'ten_hoc_vi', 'ten_viet_tat', 'ngay_tao', 'ngay_cap_nhat'],
    hocky_namhoc: [
        'id_hocky_namhoc',
        'ten_hoc_ky',
        'ten_nam_hoc',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ngay_tao',
        'ngay_cap_nhat',
        'tu_viet_tat',
    ],
    dang_ky_hp: ['id_dang_ky_hp', 'ten_dot', 'id_hocky_namhoc', 'ngay_tao', 'ngay_cap_nhat'],
    hocky_namhoc_dongia_giogiang: [
        'id_hocky_namhoc_dongia_giogiang',
        'id_hocky_namhoc',
        'id_chuc_danh',
        'id_hoc_vi',
        'don_gia_tiet_day',
        'don_gia_gio_day',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    hocky_namhoc_ngay_le: [
        'id_hocky_namhoc_ngay_le',
        'id_hocky_namhoc',
        'tieu_de',
        'ngay_le',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    hocky_namhoc_nien_khoa_hang_tn: [
        'id_hocky_namhoc_nien_khoa_hang_tn',
        'id_hocky_namhoc',
        'ten_nien_khoa_hang_tn',
        'diem_dau',
        'diem_cuoi',
        'hang_hoc_luc',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    hocky_namhoc_template_diem: [
        'id_hocky_namhoc_template_diem',
        'id_hocky_namhoc',
        'tieu_de',
        'tin_chi_bd',
        'tin_chi_kt',
        'cong_thuc_diem',
        'ghi_chu',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
        'ma_template',
        'tc_lt',
        'tc_th',
        'loai_phieu_diem',
        'xuat_diem_tphan',
    ],
    hocky_namhoc_template_diem_ct: [
        'id_hocky_namhoc_template_diem_ct',
        'id_hocky_namhoc_template_diem',
        'tieu_de',
        'ngay_tao',
        'ngay_cap_nhat',
        'ma_template_ct',
        'lop_ly_thuyet',
        'thu_tu',
        'column_name',
        'cho_nhap_le',
        'hien_thi_gv',
        'diem_cai_thien',
    ],
    hocky_namhoc_template_diem_test: [
        'id_hocky_namhoc_template_diem_test',
        'id_hocky_namhoc_template_diem',
        'ket_qua_ct',
        'ket_qua_mong_muon',
        'cong_thuc_diem',
        'trang_thai',
        'ngay_tao',
    ],
    hocky_namhoc_template_diem_thu_cong: [
        'id_hocky_namhoc_template_diem_thu_cong',
        'id_hocky_namhoc_template_diem',
        'id_hoc_phan',
        'ngoai_tru_lhp',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    hocky_namhoc_xep_loai_hl_dac_biet: [
        'id_hocky_namhoc_xep_loai_hl_dac_biet',
        'id_hocky_namhoc',
        'function',
        'mo_ta',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_nien_khoa',
    ],
    lop_hoc_phan: [
        'id_lop_hoc_phan',
        'id_hoc_phan',
        'id_hocky_namhoc',
        'lop_ly_thuyet',
        'ma_lop_hp',
        'ngay_tao',
        'ngay_cap_nhat',
        'trang_thai',
        'so_luong_tda',
        'id_hocky_namhoc_template_diem',
        'id_giang_vien',
        'da_nhap_diem',
        'tong_gio_giang',
        'tong_gio_xep_tkb',
        'han_nhap_diem',
        'ghi_chu',
    ],
    lop_hoc_phan_lop_chuyen_nganh: [
        'id_lop_hoc_phan_lop_chuyen_nganh',
        'id_lop_hoc_phan',
        'id_lop',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    lop_hoc_phan_sinh_vien: [
        'id_lop_hoc_phan_sinh_vien',
        'id_lop_hoc_phan',
        'id_sinh_vien',
        'ngay_tao',
        'ngay_cap_nhat',
        'diem_tb',
        'diem_chu',
        'diem_4',
        'diem_tp1',
        'diem_tp2',
        'diem_tp3',
        'diem_tp4',
        'diem_tp5',
        'diem_tp6',
        'diem_tp7',
        'diem_tp8',
        'diem_tp9',
        'diem_tp10',
        'ghi_chu',
        'id_dang_ky_hp_nien_khoa',
    ],
    lop_hoc_phan_tkb: [
        'id_lop_hoc_phan_tkb',
        'id_lop_hoc_phan',
        'id_thoi_gian_hoc',
        'id_giang_vien',
        'id_phong',
        'tuan',
        'ngay_tao',
        'ngay_cap_nhat',
        'ngay_hoc',
        'ngoai_gio',
        'code_diem_danh',
    ],
    lop_thong_bao: [
        'id_lop_thong_bao',
        'id_lop',
        'id_tai_khoan',
        'id_lop_thong_bao_danh_muc',
        'tieu_de',
        'noi_dung',
        'ghim',
        'xuat_ban',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    lop_thong_bao_danh_muc: ['id_lop_thong_bao_danh_muc', 'ten_danh_muc', 'ngay_tao', 'ngay_cap_nhat'],
    lop_thong_bao_files: [
        'id_lop_thong_bao_files',
        'id_lop_thong_bao',
        'path_s3',
        'type',
        'size',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    nganh: [
        'id_nganh',
        'ma_nganh',
        'ten_nganh',
        'id_don_vi',
        'ngay_tao',
        'ngay_cap_nhat',
        'trang_thai',
        'id_he_dao_tao',
    ],
    hocky_namhoc_hoc_phi: [
        'id_hocky_namhoc_hoc_phi',
        'id_hocky_namhoc',
        'id_chuong_trinh_dt_loai_hinh_dt',
        'id_nganh',
        'don_gia',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    ngay_trong_tuan: [
        'id_ngay_trong_tuan',
        'ten_ngay_trong_tuan',
        'thu_tu',
        'con_su_dung',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    nien_khoa: [
        'id_nien_khoa',
        'ma_nien_khoa',
        'ten_nien_khoa',
        'tg_bat_dau',
        'tg_ket_thuc',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_he_dao_tao',
    ],
    chuong_trinh_dt: [
        'id_chuong_trinh_dt',
        'ten_chuong_trinh_dt',
        'id_nien_khoa',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_nganh',
        'ngay_ban_hanh',
        'mo_ta',
        'path_s3',
        'khoa_cap_nhat',
    ],
    chuong_trinh_dt_chuyen_nganh: [
        'id_chuong_trinh_dt_chuyen_nganh',
        'id_chuong_trinh_dt',
        'ma_chuyen_nganh',
        'ten_chuyen_nganh',
        'mo_ta',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_chuong_trinh_dt_loai_hinh_dt',
    ],
    chuong_trinh_dt_chuyen_nganh_nhom_lua_chon: [
        'id_chuong_trinh_dt_chuyen_nganh_nhom_lua_chon',
        'id_chuong_trinh_dt_chuyen_nganh',
        'ten_nhom',
        'mo_ta',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    chuong_trinh_dt_chuyen_nganh_lua_chon: [
        'id_chuong_trinh_dt_chuyen_nganh_lua_chon',
        'id_chuong_trinh_dt_chuyen_nganh_nhom_lua_chon',
        'ten_lua_chon',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    chuong_trinh_dt_chuyen_nganh_lua_chon_ctiet: [
        'id_chuong_trinh_dt_chuyen_nganh_lua_chon_ctiet',
        'id_chuong_trinh_dt_chuyen_nganh_lua_chon',
        'id_hoc_phan',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    dang_ky_hp_nien_khoa: [
        'id_dang_ky_hp_nien_khoa',
        'id_dang_ky_hp',
        'ngay_bd',
        'ngay_kt',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_nien_khoa',
    ],
    de_xuat_hp_nien_khoa: [
        'id_de_xuat_hp_nien_khoa',
        'id_nien_khoa',
        'id_de_xuat_hp',
        'ngay_bd',
        'ngay_kt',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    hocky_namhoc_xep_loai_hl: [
        'id_hocky_namhoc_xep_loai_hl',
        'id_hocky_namhoc',
        'diem_dau',
        'diem_cuoi',
        'ngay_tao',
        'ngay_cap_nhat',
        'xep_loai',
        'ghi_chu',
        'diem_chu',
        'diem_he_4',
        'id_nien_khoa',
    ],
    lop: [
        'id_lop',
        'id_nien_khoa',
        'id_nganh',
        'ten_lop',
        'ngay_tao',
        'ngay_cap_nhat',
        'ma_lop',
        'id_chuong_trinh_dt_loai_hinh_dt',
        'ghi_chu',
    ],
    de_xuat_hp_cvht: [
        'id_de_xuat_hp_cvht',
        'id_hoc_phan',
        'id_giang_vien',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_de_xuat_hp',
        'id_lop',
    ],
    lop_quan_ly: ['id_lop_quan_ly', 'id_lop', 'id_giang_vien', 'chuc_vu', 'ngay_tao', 'ngay_cap_nhat'],
    quyen_acl_template: ['id_quyen_acl_template', 'tieu_de', 'url', 'trang_thai', 'ngay_tao', 'ngay_cap_nhat', 'route'],
    quyen_acl: [
        'id_quyen_acl',
        'is_read',
        'is_write',
        'is_update',
        'is_delete',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_quyen',
        'parms',
        'id_quyen_acl_template',
    ],
    quyen_giang_vien: ['id_quyen_giang_vien', 'id_giang_vien', 'id_quyen', 'ngay_tao', 'ngay_cap_nhat'],
    schedule_chi_tiet: ['id_schedule_chi_tiet', 'id_schedule', 'log', 'ngay_tao'],
    schedules: ['id_schedule', 'tieu_de', 'mo_ta', 'ngay_tao', 'ngay_cap_nhat', 'log', 'state', 'id_giang_vien'],
    sinh_vien: [
        'id_sinh_vien',
        'ho',
        'ten',
        'email',
        'sdt',
        'dia_chi',
        'cmnd',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
        'mssv',
        'anh',
        'ngay_sinh',
        'noi_sinh',
        'gioi_tinh',
        'ghi_chu',
        'ho_ten_cha',
        'nghe_nghiep_cha',
        'sdt_cha',
        'ho_ten_me',
        'nghe_nghiep_me',
        'sdt_me',
        'id_dan_toc',
    ],
    de_xuat_hp_sinh_vien: [
        'id_de_xuat_hp_ct',
        'id_de_xuat_hp',
        'id_sinh_vien',
        'id_hoc_phan',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_de_xuat_hp_nien_khoa',
    ],
    hocky_namhoc_hoc_phi_chi_tiet: [
        'id_hocky_namhoc_hoc_phi_chi_tiet',
        'id_hocky_namhoc',
        'id_sinh_vien',
        'tong_hoc_phi',
        'tong_tin_chi',
        'tong_hoc_phan',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    hocky_namhoc_hoc_phi_dong_tien: [
        'id_hocky_namhoc_hoc_phi_dong_tien',
        'id_hocky_namhoc',
        'id_sinh_vien',
        'so_tien',
        'ngay_thanh_toan',
        'ngan_hang',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    hocky_namhoc_hoc_phi_mien_giam: [
        'id_hocky_namhoc_hoc_phi_mien_giam',
        'id_hocky_namhoc',
        'id_sinh_vien',
        'ghi_chu',
        'so_tien',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    lich_thi_sinh_vien: [
        'id_lich_thi_sinh_vien',
        'id_sinh_vien',
        'id_lich_thi',
        'ghi_chu',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    lop_hoc_phan_du_lop_ct: [
        'id_lop_hoc_phan_du_lop_ct',
        'id_sinh_vien',
        'vang_hoc',
        'mo_ta',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_lop_hoc_phan_tkb',
        'loai_diem_danh',
    ],
    lop_sinh_vien: [
        'id_lop_sinh_vien',
        'id_lop',
        'id_sinh_vien',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_chuong_trinh_dt_chuyen_nganh',
    ],
    sinh_vien_diem: [
        'id_sinh_vien_diem',
        'id_sinh_vien',
        'id_hoc_phan',
        'id_hocky_namhoc',
        'ngay_tao',
        'ngay_cap_nhat',
        'diem_tb',
        'diem_chu',
        'diem_he_4',
        'ma_lop_hoc_phan',
        'diem_chi_tiet',
        'ghi_chu',
    ],
    sinh_vien_diem_xep_loai: [
        'id_sinh_vien_diem_xep_loai',
        'id_sinh_vien',
        'id_hoc_ky',
        'tong_tin_chi',
        'diem_tb_tich_luy',
        'xep_loai',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    sinh_vien_login: [
        'id_sinh_vien_login',
        'id_sinh_vien',
        'device_id',
        'device_name',
        'platform',
        'app_version',
        'ip_address',
        'login_at',
        'logout_at',
    ],
    thoi_gian_hoc: [
        'id_thoi_gian_hoc',
        'ten_thoi_gian_hoc',
        'tg_bat_dau',
        'tg_ket_thuc',
        'con_su_dung',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_hocky_namhoc',
        'tong_gio_th',
        'tong_tiet_lt',
        'loai_thoi_gian_hoc',
    ],
    tinh: ['id_tinh', 'ten_tinh', 'con_su_dung', 'ngay_tao', 'ngay_cap_nhat'],
    toa_nha: ['id_toa_nha', 'ten_toa_nha', 'ngay_tao', 'ngay_cap_nhat'],
    phong_hoc: [
        'id_phong_hoc',
        'id_toa_nha',
        'ten_phong',
        'so_luong',
        'loai_phong',
        'trang_thai',
        'ngay_tao',
        'ngay_cap_nhat',
    ],
    lich_thi: [
        'id_lich_thi',
        'id_hoc_phan',
        'id_hocky_namhoc',
        'id_giang_vien_1',
        'id_giang_vien_2',
        'id_giang_vien_3',
        'id_phong_hoc',
        'id_thoi_gian_hoc',
        'ngay_thi',
        'ma_lich_thi',
        'ngay_tao',
        'ngay_cap_nhat',
        'id_hinh_thuc_thi',
    ],
}
monaco.languages.registerCompletionItemProvider('sql', {
    triggerCharacters: ['.', ' '],

    provideCompletionItems: function (model, position) {
        const word = model.getWordUntilPosition(position)
        const range = {
            startLineNumber: position.lineNumber,
            endLineNumber: position.lineNumber,
            startColumn: word.startColumn,
            endColumn: word.endColumn,
        }

        const fullText = model.getValue()
        const textBefore = model.getValueInRange({
            startLineNumber: 1,
            startColumn: 1,
            endLineNumber: position.lineNumber,
            endColumn: position.column,
        })

        // ----------- 1. Parse alias -------------
        const aliasMap = {} // ví dụ: { sv: 'sinh_vien' }
        const aliasRegex = /\bFROM\s+(\w+)\s+(\w+)|\bJOIN\s+(\w+)\s+(\w+)/gi
        let match
        while ((match = aliasRegex.exec(fullText))) {
            const table = match[1] || match[3]
            const alias = match[2] || match[4]
            if (table && alias) aliasMap[alias] = table
        }

        // ----------- 2. Gợi ý theo alias hoặc tên bảng gốc -------------
        const tableDotMatch = /(\w+)\.$/.exec(textBefore.trim())
        if (tableDotMatch) {
            const tableOrAlias = tableDotMatch[1]
            const realTable = aliasMap[tableOrAlias] || tableOrAlias // Nếu không có alias thì dùng trực tiếp

            const columns = schema[realTable] || []
            const suggestions = columns.map((col) => ({
                label: col,
                kind: monaco.languages.CompletionItemKind.Field,
                insertText: col,
                range,
            }))
            return { suggestions }
        }

        // Nếu không có dấu chấm → gợi ý từ khóa, hàm, bảng
        const staticSuggestions = [
            {
                label: 'array_agg',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'array_agg($1)',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Gộp nhiều dòng thành mảng',
            },
            {
                label: 'string_agg',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: "string_agg($1, ',' ORDER BY $2)",
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Gộp chuỗi từ nhiều dòng, cách nhau bằng dấu phẩy',
            },
            {
                label: 'NgayKy',
                kind: monaco.languages.CompletionItemKind.Variable,
                insertText: `TO_CHAR(NOW(), '"ngày" DD "tháng" MM "năm" YYYY') AS "NgayKy"`,
            },
            {
                label: 'SELECT',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'SELECT ',
            },
            {
                label: 'FROM',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'FROM ',
            },
            {
                label: 'WHERE',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'WHERE ',
            },
            {
                label: 'JOIN',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'JOIN ',
            },
            {
                label: 'LEFT JOIN',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'LEFT JOIN ',
            },
            {
                label: 'RIGHT JOIN',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'RIGHT JOIN ',
            },
            {
                label: 'AND',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'AND ',
            },
            {
                label: 'OR',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'OR ',
            },
            {
                label: 'NOT',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'NOT ',
            },
            {
                label: 'IN',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'IN ',
            },
            {
                label: 'NOT IN',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'NOT IN ',
            },
            {
                label: 'IS NULL',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'IS NULL ',
            },
            {
                label: 'COALESCE',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'COALESCE(${1:expression}, ${2:default_value})',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                detail: 'Trả về giá trị đầu tiên không null',
            },
            {
                label: 'NOW()',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: 'NOW()',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Lấy thời gian hiện tại',
            },
            {
                label: 'TO_CHAR',
                kind: monaco.languages.CompletionItemKind.Function,
                insertText: "TO_CHAR($1, 'DD-MM-YYYY')",
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Định dạng ngày tháng',
            },
            {
                label: 'ILIKE',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: "ILIKE '%$1%' $2",
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'So sánh không phân biệt chữ hoa/thường',
            },
            {
                label: 'LIKE',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: "LIKE '%$1%' $2",
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'So sánh phân biệt chữ hoa/thường',
            },
            {
                label: 'BETWEEN',
                kind: monaco.languages.CompletionItemKind.Keyword,
                insertText: 'BETWEEN $1 AND $2',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
            },
            {
                label: 'CASE',
                kind: monaco.languages.CompletionItemKind.Snippet,
                insertText: [
                    'CASE',
                    '\tWHEN ${1:điều_kiện} THEN ${2:giá_trị_nếu_đúng}',
                    '\tELSE ${3:giá_trị_nếu_sai}',
                    'END',
                ].join('\n'),
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Câu lệnh điều kiện CASE trong SQL',
            },
            {
                label: 'CASE ()',
                kind: monaco.languages.CompletionItemKind.Snippet,
                insertText: ['CASE ${1:tham_số}', '\tWHEN ${2:giá_trị} THEN ${3:hành_động}', 'END'].join('\n'),
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Câu lệnh CASE viết 1 dòng',
            },
            {
                label: 'WHEN',
                kind: monaco.languages.CompletionItemKind.Snippet,
                insertText: 'WHEN ${1:điều_kiện} THEN ${2:đúng}',
                insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet,
                documentation: 'Câu lệnh CASE viết 1 dòng',
            },
        ]

        // Thêm gợi ý bảng
        const tableSuggestions = Object.keys(schema).map((table) => ({
            label: table,
            kind: monaco.languages.CompletionItemKind.Class,
            insertText: table,
            range,
        }))

        return {
            suggestions: [...staticSuggestions, ...tableSuggestions],
        }
    },
})

monaco.languages.register({ id: 'jsonc' })
monaco.languages.setLanguageConfiguration('jsonc', {
    brackets: [
        ['{', '}'],
        ['[', ']'],
    ],
    autoClosingPairs: [
        { open: '{', close: '}' },
        { open: '[', close: ']' },
        { open: '"', close: '"' },
    ],
    surroundingPairs: [
        { open: '{', close: '}' },
        { open: '[', close: ']' },
        { open: '"', close: '"' },
    ],
})

monaco.languages.setMonarchTokensProvider('jsonc', {
    brackets: [
        { open: '{', close: '}', token: 'delimiter.curly' },
        { open: '[', close: ']', token: 'delimiter.square' },
    ],
    tokenizer: {
        root: [
            [/{/, { token: 'delimiter.curly', bracket: '@open' }],
            [/}/, { token: 'delimiter.curly', bracket: '@close' }],
            [/\[/, { token: 'delimiter.square', bracket: '@open' }],
            [/\]/, { token: 'delimiter.square', bracket: '@close' }],
            [/"[^"]*"/, 'string'],
            [/[-+]?\d+(\.\d+)?/, 'number'],
            [/(true|false|null)/, 'keyword'],
            [/,/, 'delimiter.comma'],
            [/:/, 'delimiter.colon'],
            [/\/\/.*$/, 'comment'],
        ],
    },
})
monaco.languages.registerDocumentFormattingEditProvider('jsonc', {
    provideDocumentFormattingEdits(model) {
        try {
            const text = model.getValue()
            const json = JSON5.parse(text)
            const formatted = JSON.stringify(json, null, 4)
            return [
                {
                    range: model.getFullModelRange(),
                    text: formatted,
                },
            ]
        } catch (err) {
            console.error('Formatter error:', err.message)
            func.toastError('Lỗi format JSON')
            return []
        }
    },
})

monaco.languages.register({ id: 'php' })

monaco.languages.setLanguageConfiguration('php', {
    brackets: [
        ['{', '}'],
        ['[', ']'],
        ['(', ')'],
    ],
    autoClosingPairs: [
        { open: '{', close: '}' },
        { open: '[', close: ']' },
        { open: '(', close: ')' },
        { open: '"', close: '"' },
        { open: "'", close: "'" },
    ],
})

monaco.languages.setMonarchTokensProvider('php', {
    defaultToken: '',
    tokenPostfix: '.php',

    keywords: [
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'do',
        'else',
        'elseif',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'extends',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'match',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'readonly',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
        'null',
        'preg_match',
        'preg_replace',
        'in_array',
        'array_merge',
        'array_map',
        'array_filter',
        'array_reduce',
        'count',
        'strlen',
        'strpos',
        'str_replace',
        'substr',
        'trim',
        'explode',
        'implode',
        'is_array',
        'is_string',
        'is_numeric',
        'is_null',
        'empty',
        'min',
        'max',
        'abs',
        'round',
        'floor',
        'ceil',
        'json_encode',
        'json_decode',
        'print_r',
        'var_dump',
        'date',
        'time',
        'file_get_contents',
        'file_put_contents',
        'fopen',
        'fclose',
        'fwrite',
        'fread',
    ],

    operators: [
        '=',
        '>',
        '<',
        '!',
        '~',
        '?',
        ':',
        '==',
        '<=',
        '>=',
        '!=',
        '&&',
        '||',
        '++',
        '--',
        '+',
        '-',
        '*',
        '/',
        '&',
        '|',
        '^',
        '%',
        '<<',
        '>>',
        '**',
    ],

    brackets: [
        { open: '{', close: '}', token: 'delimiter.curly' },
        { open: '[', close: ']', token: 'delimiter.square' },
        { open: '(', close: ')', token: 'delimiter.parenthesis' },
    ],

    tokenizer: {
        root: [
            // regex pattern
            [/\/\^.*\$\/[gimsuy]*/, 'regexpc'],
            [/\/.*?\/[gimsuy]*/, 'regexpc'],
            [/'\/\^.*\$\/[gimsuy]*'/, 'regexpc'],

            // Keywords
            [
                /[a-zA-Z_]\w*/,
                {
                    cases: {
                        '@keywords': 'keyword',
                        '@default': 'identifier',
                    },
                },
            ],

            [/\$xepLoaiHPTheoNienKhoa\b/, 'myFunc'],
            // Variables
            [/\$[a-zA-Z_]\w*/, 'variable'],

            // Strings
            [/"/, { token: 'string.quote', next: '@string_double' }],
            [/'/, { token: 'string.quote', next: '@string_single' }],

            // Numbers
            [/\d+/, 'number'],

            // Brackets
            [/[{}()\[\]]/, '@brackets'],

            // Operators
            [/(\+\+|--|\*\*|==|!=|<=|>=|&&|\|\||<<|>>|[-+*/=<>!~?:&|^%])/, 'operator'],

            // Delimiters
            [/[;,.]/, 'delimiter'],
        ],

        string_double: [
            [/[^\\"]+/, 'string'],
            [/\\./, 'string.escape'],
            [/"/, { token: 'string.quote', next: '@pop' }],
        ],

        string_single: [
            [/[^\\']+/, 'string'],
            [/\\./, 'string.escape'],
            [/'/, { token: 'string.quote', next: '@pop' }],
        ],
    },
})

monaco.languages.registerDocumentFormattingEditProvider('php', {
    provideDocumentFormattingEdits: function (model, options, token) {
        const lines = model.getLinesContent()
        const edits = []
        let indentLevel = 0

        const indent = (level) => ' '.repeat(level * 4)

        for (let i = 0; i < lines.length; i++) {
            let line = lines[i].trim()

            // Kiểm tra các dòng đóng khối
            if (line.match(/^}/)) {
                indentLevel = Math.max(indentLevel - 1, 0)
            }

            // Format dòng với indent
            edits.push({
                range: new monaco.Range(i + 1, 1, i + 1, lines[i].length + 1),
                text: indent(indentLevel) + line,
            })

            // Nếu dòng mở khối, tăng indent
            if (line.match(/{\s*$/)) {
                indentLevel++
            }
        }

        return edits
    },
})
