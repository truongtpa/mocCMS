CREATE TABLE IF NOT EXISTS tai_khoan (
    id_tai_khoan SERIAL PRIMARY KEY,
    ho_ten TEXT NULL,
    email TEXT NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quyen (
    id_quyen SERIAL PRIMARY KEY,
    ten_nhom TEXT NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quyen_chi_tiet (
    id_quyen_chi_tiet SERIAL PRIMARY KEY,
    id_quyen INT NULL,
    tieu_de TEXT NULL,
    funcs TEXT NULL,
    show_views TEXT NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quyen_nhom (
    id_quyen_nhom SERIAL PRIMARY KEY,
    tieu_de TEXT NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    mac_dinh INT NULL
);

CREATE TABLE IF NOT EXISTS quyen_nhom_chi_tiet (
    id_quyen_nhom_chi_tiet SERIAL PRIMARY KEY,
    id_quyen_nhom INT NULL,
    id_quyen_chi_tiet INT NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quyen_nhom_tai_khoan (
    id_quyen_nhom_tai_khoan SERIAL PRIMARY KEY,
    id_tai_khoan TEXT NULL,
    id_quyen_nhom INT NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DỮ LIỆU MẶC ĐỊNH
INSERT INTO quyen (id_quyen, ten_nhom, ngay_tao, ngay_cap_nhat) VALUES (1, 'Bảng điều khiển', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (1, 'Xem bảng điều khiển', 'StudentPortalController.getDashboardStats,StudentPortalController.updateBookingStatus', 'menuDashboard.xem', NOW(), NOW()) ON CONFLICT DO NOTHING;

INSERT INTO quyen (id_quyen, ten_nhom, ngay_tao, ngay_cap_nhat) VALUES (2, 'Thông tin cá nhân', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (2, 'Xem thông tin cá nhân', 'StudentPortalController.getProfileData', 'menuProfile.xem', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (2, 'Cập nhật thông tin cá nhân', 'StudentPortalController.updateProfileData,StudentPortalController.addEavAttribute,StudentPortalController.forceSyncStudentApi', 'pageProfile.sua', NOW(), NOW()) ON CONFLICT DO NOTHING;

INSERT INTO quyen (id_quyen, ten_nhom, ngay_tao, ngay_cap_nhat) VALUES (3, 'Đặt lịch hẹn giảng viên', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (3, 'Xem danh sách lịch hẹn', 'StudentPortalController.getBookingsList,StudentPortalController.getBookingLecturers', 'menuBooking.xem', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (3, 'Tạo lịch hẹn mới', 'StudentPortalController.createBooking', 'pageBooking.them', NOW(), NOW()) ON CONFLICT DO NOTHING;

INSERT INTO quyen (id_quyen, ten_nhom, ngay_tao, ngay_cap_nhat) VALUES (4, 'Giải thưởng & Thành tích', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (4, 'Xem danh sách thành tích', 'StudentPortalController.getAchievementsList', 'menuThanhTich.xem', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (4, 'Khai báo thành tích mới', 'StudentPortalController.addAchievement', 'pageThanhTich.them', NOW(), NOW()) ON CONFLICT DO NOTHING;

INSERT INTO quyen (id_quyen, ten_nhom, ngay_tao, ngay_cap_nhat) VALUES (5, 'Đối tượng & Thuộc tính động', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (5, 'Xem danh mục đối tượng động', 'DynamicObjectController.getTypes,DynamicObjectController.getFields,DynamicObjectController.getRecords', 'menuDoiTuongDong.xem', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (5, 'Thêm mới loại đối tượng / thuộc tính / bản ghi', 'DynamicObjectController.saveType,DynamicObjectController.saveField,DynamicObjectController.saveRecord', 'pageDoiTuongDong.them', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (5, 'Cập nhật loại đối tượng / thuộc tính / bản ghi', 'DynamicObjectController.saveType,DynamicObjectController.saveField,DynamicObjectController.saveRecord,DynamicObjectController.reorderFields', 'pageDoiTuongDong.sua', NOW(), NOW()) ON CONFLICT DO NOTHING;
INSERT INTO quyen_chi_tiet (id_quyen, tieu_de, funcs, show_views, ngay_tao, ngay_cap_nhat) 
VALUES (5, 'Xóa loại đối tượng / thuộc tính / bản ghi', 'DynamicObjectController.deleteType,DynamicObjectController.deleteField,DynamicObjectController.deleteRecord', 'pageDoiTuongDong.xoa', NOW(), NOW()) ON CONFLICT DO NOTHING;

-- 6. Nhóm quyền mặc định hệ thống (Admin & Mặc định)
INSERT INTO quyen_nhom (id_quyen_nhom, tieu_de, ngay_tao, ngay_cap_nhat, mac_dinh) 
VALUES (1, 'Quản trị viên hệ thống', NOW(), NOW(), 1) ON CONFLICT DO NOTHING;

-- Gán toàn bộ 11 quyền chi tiết vào nhóm mặc định id_quyen_nhom = 1
INSERT INTO quyen_nhom_chi_tiet (id_quyen_nhom, id_quyen_chi_tiet, ngay_tao, ngay_cap_nhat)
SELECT 1, id_quyen_chi_tiet, NOW(), NOW() FROM quyen_chi_tiet ON CONFLICT DO NOTHING;
