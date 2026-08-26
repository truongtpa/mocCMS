create table tai_khoan
(
    id_tai_khoan int auto_increment
        primary key,
    ho_ten       text                                null,
    email        text                                null,
    ngay_tao     timestamp default CURRENT_TIMESTAMP null
)


create table quyen
(
    id_quyen      int auto_increment
        primary key,
    ten_nhom      text                                null,
    ngay_tao      timestamp default CURRENT_TIMESTAMP null,
    ngay_cap_nhat timestamp default CURRENT_TIMESTAMP null
);

create table quyen_chi_tiet
(
    id_quyen_chi_tiet int auto_increment
        primary key,
    id_quyen          int                                 null,
    tieu_de           text                                null,
    funcs             text                                null,
    show_views        text                                null,
    ngay_tao          timestamp default CURRENT_TIMESTAMP null,
    ngay_cap_nhat     timestamp default CURRENT_TIMESTAMP null
);

create table quyen_nhom
(
    id_quyen_nhom int auto_increment
        primary key,
    tieu_de       text                                null,
    ngay_tao      timestamp default CURRENT_TIMESTAMP null,
    ngay_cap_nhat timestamp default CURRENT_TIMESTAMP null,
    mac_dinh      int                                 null
);

create table quyen_nhom_chi_tiet
(
    id_quyen_nhom_chi_tiet int auto_increment
        primary key,
    id_quyen_nhom          int                                 null,
    id_quyen_chi_tiet      int                                 null,
    ngay_tao               timestamp default CURRENT_TIMESTAMP null,
    ngay_cap_nhat          timestamp default CURRENT_TIMESTAMP null
);

create table quyen_nhom_tai_khoan
(
    id_quyen_nhom_tai_khoan int auto_increment
        primary key,
    id_tai_khoan            text                                null,
    id_quyen_nhom           int                                 null,
    ngay_tao                timestamp default CURRENT_TIMESTAMP null,
    ngay_cap_nhat           timestamp default CURRENT_TIMESTAMP null
);

