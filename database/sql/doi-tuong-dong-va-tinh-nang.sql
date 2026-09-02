CREATE TABLE IF NOT EXISTS sinh_vien (
    id_sinh_vien SERIAL PRIMARY KEY,
    ma_sv VARCHAR(50) UNIQUE NOT NULL,
    ho_ten VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    ngay_sinh DATE,
    gioi_tinh VARCHAR(10),
    lop_hoc VARCHAR(100),
    khoa VARCHAR(100),
    nganh VARCHAR(100),
    so_dien_thoai VARCHAR(20),
    dia_chi TEXT,
    trang_thai VARCHAR(50) DEFAULT 'Đang học',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS giang_vien (
    id SERIAL PRIMARY KEY,
    ma_gv VARCHAR(50) UNIQUE NOT NULL,
    ho_ten VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    ten_khoa VARCHAR(100),
    hoc_vi VARCHAR(50),
    chuyen_mon TEXT,
    so_dien_thoai VARCHAR(20),
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS loai_doi_tuong (
    id SERIAL PRIMARY KEY,
    ma_loai VARCHAR(100) UNIQUE NOT NULL,
    ten_loai VARCHAR(255) NOT NULL,
    mo_ta TEXT,
    trang_thai INT DEFAULT 1,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS danh_muc_truong (
    id SERIAL PRIMARY KEY,
    id_loai_doi_tuong INT NOT NULL REFERENCES loai_doi_tuong(id) ON DELETE CASCADE,
    ma_danh_muc VARCHAR(100) NOT NULL,
    ten_danh_muc VARCHAR(255) NOT NULL,
    kieu_du_lieu VARCHAR(50) NOT NULL DEFAULT 'text',
    cau_hinh JSONB,
    bat_buoc INT DEFAULT 0,
    thu_tu INT DEFAULT 0,
    trang_thai INT DEFAULT 1,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uk_loai_ma_danh_muc UNIQUE(id_loai_doi_tuong, ma_danh_muc)
);

CREATE TABLE IF NOT EXISTS doi_tuong (
    id SERIAL PRIMARY KEY,
    id_loai_doi_tuong INT NOT NULL REFERENCES loai_doi_tuong(id) ON DELETE CASCADE,
    ma_doi_tuong VARCHAR(100),
    ten_doi_tuong VARCHAR(255),
    trang_thai INT DEFAULT 1,
    id_nguoi_tao INT,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gia_tri_thong_tin (
    id SERIAL PRIMARY KEY,
    id_doi_tuong INT NOT NULL REFERENCES doi_tuong(id) ON DELETE CASCADE,
    id_danh_muc_truong INT NOT NULL REFERENCES danh_muc_truong(id) ON DELETE CASCADE,
    gia_tri TEXT,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uk_doi_tuong_truong UNIQUE(id_doi_tuong, id_danh_muc_truong)
);

CREATE TABLE IF NOT EXISTS thanh_tich (
    id SERIAL PRIMARY KEY,
    id_sinh_vien INT NOT NULL,
    ten_thanh_tich VARCHAR(255) NOT NULL,
    loai_thanh_tich VARCHAR(100) NOT NULL,
    cap_khen_thuong VARCHAR(100),
    ngay_dat DATE,
    mo_ta TEXT,
    file_minh_chung VARCHAR(500),
    trang_thai VARCHAR(50) DEFAULT 'Chờ duyệt',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS lich_hen (
    id SERIAL PRIMARY KEY,
    id_sinh_vien INT NOT NULL,
    id_giang_vien INT NOT NULL,
    tieu_de VARCHAR(255) NOT NULL,
    noi_dung TEXT,
    thoi_gian_bat_dau TIMESTAMP NOT NULL,
    thoi_gian_ket_thuc TIMESTAMP NOT NULL,
    dia_diem VARCHAR(255),
    trang_thai VARCHAR(50) DEFAULT 'Chờ xác nhận',
    ghi_chu TEXT,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
