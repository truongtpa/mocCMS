-- Backup 4 bang ACL truoc khi DROP
-- 2026-08-18 14:00:53

DROP TABLE IF EXISTS `acl`;
CREATE TABLE `acl` (
  `id_acl` int(11) NOT NULL AUTO_INCREMENT,
  `ten_acl` text COLLATE utf8mb4_unicode_ci,
  `trang_thai` int(11) DEFAULT NULL,
  `mac_dinh` int(11) NOT NULL DEFAULT '0',
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_acl`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `acl` (`id_acl`,`ten_acl`,`trang_thai`,`mac_dinh`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('10','Quản trị','1','0','2025-05-12 16:34:05','2025-05-12 16:34:05');
INSERT INTO `acl` (`id_acl`,`ten_acl`,`trang_thai`,`mac_dinh`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('12','Giảng viên (Mặc định)','1','1','2025-05-16 14:31:49','2025-08-13 22:27:38');
INSERT INTO `acl` (`id_acl`,`ten_acl`,`trang_thai`,`mac_dinh`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('14','TCHC - Trưởng phòng','1','0','2025-07-08 06:49:08','2025-08-13 22:05:42');
INSERT INTO `acl` (`id_acl`,`ten_acl`,`trang_thai`,`mac_dinh`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('15','TCHC -  Văn thư','1','0','2025-07-08 06:54:43','2025-08-13 22:05:50');
INSERT INTO `acl` (`id_acl`,`ten_acl`,`trang_thai`,`mac_dinh`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('16','Trưởng các đơn vị','1','0','2025-08-13 21:40:25','2025-08-13 21:40:25');
INSERT INTO `acl` (`id_acl`,`ten_acl`,`trang_thai`,`mac_dinh`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('17','Hiệu trưởng','1','0','2025-08-14 08:45:54','2025-08-14 08:45:54');
INSERT INTO `acl` (`id_acl`,`ten_acl`,`trang_thai`,`mac_dinh`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('19','Trình ký VB','1','0','2026-08-04 08:25:56','2026-08-04 08:25:56');

DROP TABLE IF EXISTS `acl_template`;
CREATE TABLE `acl_template` (
  `id_acl_template` int(11) NOT NULL AUTO_INCREMENT,
  `tieu_de` text COLLATE utf8mb4_unicode_ci,
  `url` text COLLATE utf8mb4_unicode_ci,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `nhom_tieu_de` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_acl_template`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('35','Trang chủ','/admin/home','2025-05-12 16:30:35','2026-08-14 09:51:17','Trang chủ');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('36','Quản lý tài khoản','/admin/tai-khoan','2025-05-12 16:30:35','2025-07-07 10:00:28','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('37','Quản lý đơn vị','/admin/don-vi','2025-05-12 16:30:35','2025-07-07 10:01:11','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('38','Quản lý loại văn bản','/admin/loai-van-ban','2025-05-12 16:30:35','2025-07-07 10:00:48','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('39','Quản lý phòng ban','/admin/phong','2025-05-12 16:30:35','2025-07-07 10:00:38','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('40','Quản lý văn bản','/admin/vanban','2025-05-12 16:30:35','2025-05-12 16:30:35','Văn bản');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('42','Chi tiết văn bản Admin','/admin/van-ban/chi-tiet','2025-05-12 16:30:35','2025-07-07 09:59:25','Văn bản');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('43','Tất cả thông báo','/admin/thong-bao/tat-ca-thong-bao','2025-05-12 16:30:35','2025-07-07 09:59:40','Thông báo');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('44','Danh mục thông báo','/admin/thong-bao/danh-muc-thong-bao','2025-05-12 16:30:35','2025-07-07 09:59:59','Thông báo');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('45','Chi tiết thông báo','/admin/thong-bao/chi-tiet-thong-bao','2025-05-12 16:30:35','2025-07-07 10:00:07','Thông báo');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('48','Quản lý quyền','/admin/quyen/template','2025-05-12 16:30:35','2025-07-07 10:01:23','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('50','Quản lý chi tiết template quyền','/admin/quyen/template/chi-tiet','2025-05-12 16:30:35','2026-08-14 10:21:34','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('51','Quản lý quyền tài khoản','/admin/gan-quyen-tai-khoan','2025-05-12 16:30:35','2025-07-07 10:01:05','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('52','Danh mục báo cáo','/admin/danh-muc-bao-cao','2025-05-12 16:30:35','2025-07-07 10:02:31','Báo cáo');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('53','Đợt báo cáo','/admin/dot-bao-cao','2025-05-12 16:30:35','2025-07-07 10:02:23','Báo cáo');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('54','File báo cáo','/admin/file-bao-cao','2025-05-12 16:30:35','2025-07-07 14:24:02','Báo cáo');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('58','Chi tiết văn bản Nguời dùng','/admin/van-ban/chi-tiet-user','2025-05-12 16:30:35','2025-07-07 09:59:19','Văn bản');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('64','Quản lý loại phê duyệt','/admin/loai-phe-duyet','2025-06-24 14:07:42','2025-07-07 10:00:58','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('79','Chi tiết lịch công tác','/admin/chi-tiet-lich-cong-tac','2025-07-07 09:51:10','2025-07-07 09:54:37','Công tác');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('80','Quản lý lịch công tác','/admin/lich-cong-tac','2025-07-07 09:53:07','2025-07-07 09:54:32','Công tác');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('81','Xem lịch công tác','/admin/cong-tac/user/lich-cong-tac','2025-07-07 09:53:45','2025-07-07 09:54:28','Công tác');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('84','Xem văn bản','/admin/van-ban/all-van-ban','2025-07-07 09:56:54','2025-07-07 09:58:49','Văn bản');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('85','Thêm văn bản','/admin/van-ban/them','2025-07-07 09:57:21','2025-07-07 09:58:33','Văn bản');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('86','Xem văn bản xử lý','/admin/van-ban/van-ban-xu-ly','2025-07-07 09:57:33','2025-07-07 09:58:37','Văn bản');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('92','Xét duyệt văn bản','/admin/van-ban/van-ban-xet-duyet','2025-08-21 17:37:05','2025-08-21 17:37:19','Văn bản');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('94','Quản lý chức vụ','/admin/chuc-vu','2025-08-21 18:03:26','2025-08-21 18:03:41','Quản lý thông tin');
INSERT INTO `acl_template` (`id_acl_template`,`tieu_de`,`url`,`ngay_tao`,`ngay_cap_nhat`,`nhom_tieu_de`) VALUES ('99','Trình ký văn bản','/admin/trinh-ky','2026-08-04 08:02:40','2026-08-14 04:05:45','Trình ký văn bản');

DROP TABLE IF EXISTS `acl_chi_tiet`;
CREATE TABLE `acl_chi_tiet` (
  `id_acl_chi_tiet` int(11) NOT NULL AUTO_INCREMENT,
  `id_acl` int(11) DEFAULT NULL,
  `id_acl_template` int(11) DEFAULT NULL,
  `parms` text COLLATE utf8mb4_unicode_ci,
  `is_read` int(11) DEFAULT '1',
  `is_write` int(11) DEFAULT '1',
  `is_delete` int(11) DEFAULT '1',
  `is_update` int(11) DEFAULT '1',
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_acl_chi_tiet`)
) ENGINE=InnoDB AUTO_INCREMENT=381 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('53','10','35',NULL,'1','1','1','1','2025-05-12 16:34:05','2025-05-12 16:34:47');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('54','10','36',NULL,'1','1','1','1','2025-05-12 16:34:05','2025-05-12 16:34:33');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('55','10','37',NULL,'1','1','1','1','2025-05-12 16:34:05','2025-05-12 16:34:50');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('56','10','38',NULL,'1','1','1','1','2025-05-12 16:34:05','2025-05-12 16:34:39');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('57','10','39',NULL,'1','1','1','1','2025-05-12 16:34:05','2025-05-12 16:34:36');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('58','10','40',NULL,'1','1','1','1','2025-05-12 16:34:06','2025-05-12 16:34:23');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('60','10','42',NULL,'1','1','1','1','2025-05-12 16:34:06','2025-05-12 16:35:04');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('61','10','43',NULL,'1','1','1','1','2025-05-12 16:34:06','2025-05-12 16:34:31');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('62','10','44',NULL,'1','1','1','1','2025-05-12 16:34:06','2025-05-12 16:34:58');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('63','10','45',NULL,'1','1','1','1','2025-05-12 16:34:06','2025-05-12 16:35:06');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('66','10','48',NULL,'1','1','1','1','2025-05-12 16:34:09','2025-05-12 16:34:54');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('68','10','50',NULL,'1','1','1','1','2025-05-12 16:34:09','2025-05-12 16:35:07');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('69','10','51',NULL,'1','1','1','1','2025-05-12 16:34:09','2025-05-12 16:34:49');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('70','10','52',NULL,'1','1','1','1','2025-05-12 16:34:09','2025-05-12 16:34:59');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('71','10','53',NULL,'1','1','1','1','2025-05-12 16:34:09','2025-05-12 16:35:01');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('72','10','54',NULL,'1','1','1','1','2025-05-12 16:34:09','2025-05-12 16:35:02');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('76','10','58',NULL,'1','1','1','1','2025-05-12 16:34:10','2025-05-12 16:35:03');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('107','12','35',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('108','12','36',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('109','12','37',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('110','12','38',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('111','12','39',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('112','12','40',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('114','12','42',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('115','12','43',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('116','12','44',NULL,'0','0','0','0','2025-05-16 14:31:49','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('117','12','45',NULL,'0','0','0','0','2025-05-16 14:31:50','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('120','12','48',NULL,'0','0','0','0','2025-05-16 14:31:50','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('122','12','50',NULL,'0','0','0','0','2025-05-16 14:31:50','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('123','12','51',NULL,'0','0','0','0','2025-05-16 14:31:50','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('124','12','52',NULL,'0','0','0','0','2025-05-16 14:31:50','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('125','12','53',NULL,'0','0','0','0','2025-05-16 14:31:50','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('126','12','54',NULL,'0','0','0','0','2025-05-16 14:31:50','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('130','12','58',NULL,'0','0','0','0','2025-05-16 14:31:51','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('157','10','64',NULL,'1','1','1','1','2025-06-24 14:07:43','2025-06-24 14:07:52');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('169','10','79',NULL,'1','1','1','1','2025-07-07 09:51:10','2025-07-07 10:26:12');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('171','12','79',NULL,'0','0','0','0','2025-07-07 09:51:10','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('173','10','80',NULL,'1','1','1','1','2025-07-07 09:53:07','2025-07-07 10:26:12');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('175','12','80',NULL,'0','0','0','0','2025-07-07 09:53:07','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('177','10','81',NULL,'1','1','1','1','2025-07-07 09:53:45','2025-07-07 10:26:11');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('179','12','81',NULL,'0','0','0','0','2025-07-07 09:53:45','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('189','10','84',NULL,'1','1','1','1','2025-07-07 09:56:54','2025-07-07 10:26:15');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('191','12','84',NULL,'0','0','0','0','2025-07-07 09:56:54','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('193','10','85',NULL,'1','1','1','1','2025-07-07 09:57:21','2025-07-07 10:26:17');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('195','12','85',NULL,'0','0','0','0','2025-07-07 09:57:21','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('197','10','86',NULL,'1','1','1','1','2025-07-07 09:57:33','2025-07-07 10:26:19');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('199','12','86',NULL,'0','1','0','1','2025-07-07 09:57:33','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('201','14','35',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('202','14','36',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('203','14','37',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('204','14','38',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('205','14','39',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('206','14','40',NULL,'1','1','0','0','2025-07-08 06:49:08','2025-07-08 06:50:42');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('207','14','42',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('208','14','43',NULL,'1','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:14');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('209','14','44',NULL,'1','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:14');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('210','14','45',NULL,'1','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:13');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('211','14','48',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('213','14','50',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('214','14','51',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('215','14','52',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('216','14','53',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('217','14','54',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('219','14','58',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('220','14','64',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('221','14','79',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('222','14','80',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('223','14','81',NULL,'0','0','0','0','2025-07-08 06:49:08','2025-07-08 06:49:08');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('224','14','84',NULL,'1','1','0','1','2025-07-08 06:49:08','2025-07-08 06:50:32');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('225','14','85',NULL,'1','1','0','1','2025-07-08 06:49:08','2025-07-08 06:50:31');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('226','14','86',NULL,'1','1','0','1','2025-07-08 06:49:08','2025-07-08 06:50:34');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('227','15','35',NULL,'1','0','0','0','2025-07-08 06:54:43','2025-07-08 06:55:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('228','15','36',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-07-08 06:54:43');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('229','15','37',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-10-01 10:16:23');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('230','15','38',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-10-01 10:16:28');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('231','15','39',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-10-01 10:16:13');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('232','15','40',NULL,'1','1','0','0','2025-07-08 06:54:43','2025-10-01 10:08:10');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('233','15','42',NULL,'1','0','0','0','2025-07-08 06:54:43','2025-10-30 08:41:30');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('234','15','43',NULL,'1','0','0','0','2025-07-08 06:54:43','2025-07-08 06:54:50');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('235','15','44',NULL,'1','0','0','0','2025-07-08 06:54:43','2025-07-08 06:54:50');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('236','15','45',NULL,'1','0','0','0','2025-07-08 06:54:43','2025-07-08 06:54:49');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('237','15','48',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-07-08 06:54:43');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('239','15','50',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-07-08 06:54:43');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('240','15','51',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-07-08 06:54:43');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('241','15','52',NULL,'1','0','0','0','2025-07-08 06:54:43','2026-04-06 15:01:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('242','15','53',NULL,'1','0','0','0','2025-07-08 06:54:43','2026-04-06 15:01:57');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('243','15','54',NULL,'1','0','0','0','2025-07-08 06:54:43','2026-04-06 15:01:57');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('245','15','58',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-10-01 10:01:17');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('246','15','64',NULL,'0','0','0','0','2025-07-08 06:54:43','2025-07-08 06:54:43');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('247','15','79',NULL,'1','0','0','0','2025-07-08 06:54:43','2026-04-06 15:02:14');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('248','15','80',NULL,'1','0','0','0','2025-07-08 06:54:43','2026-04-06 15:02:13');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('249','15','81',NULL,'1','0','0','0','2025-07-08 06:54:43','2026-04-06 15:02:13');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('250','15','84',NULL,'1','0','0','0','2025-07-08 06:54:43','2025-10-01 10:02:50');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('251','15','85',NULL,'1','1','1','1','2025-07-08 06:54:43','2025-10-01 09:49:34');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('252','15','86',NULL,'1','0','0','0','2025-07-08 06:54:43','2025-10-30 08:38:22');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('253','16','35',NULL,'1','1','1','1','2025-08-13 21:40:25','2025-08-13 21:40:49');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('254','16','36',NULL,'0','0','0','0','2025-08-13 21:40:25','2025-10-17 13:18:27');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('255','16','37',NULL,'0','0','0','0','2025-08-13 21:40:25','2025-10-17 13:18:24');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('256','16','38',NULL,'0','0','0','0','2025-08-13 21:40:25','2025-10-17 13:18:25');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('257','16','39',NULL,'0','0','0','0','2025-08-13 21:40:25','2025-10-17 13:18:25');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('258','16','40',NULL,'1','1','1','1','2025-08-13 21:40:25','2025-11-18 10:55:29');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('259','16','42',NULL,'1','0','0','0','2025-08-13 21:40:25','2025-11-18 10:56:04');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('260','16','43',NULL,'1','1','1','1','2025-08-13 21:40:25','2025-08-13 22:00:31');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('261','16','44',NULL,'1','0','0','0','2025-08-13 21:40:25','2025-10-17 13:13:57');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('262','16','45',NULL,'1','1','1','1','2025-08-13 21:40:25','2025-08-13 22:00:33');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('263','16','48',NULL,'0','0','0','0','2025-08-13 21:40:25','2025-10-17 13:18:26');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('265','16','50',NULL,'0','0','0','0','2025-08-13 21:40:25','2025-10-17 13:18:23');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('266','16','51',NULL,'0','0','0','0','2025-08-13 21:40:25','2025-10-17 13:18:26');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('267','16','52',NULL,'1','0','0','0','2025-08-13 21:40:25','2025-08-13 22:02:30');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('268','16','53',NULL,'1','1','0','0','2025-08-13 21:40:25','2025-08-22 15:19:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('269','16','54',NULL,'1','1','1','1','2025-08-13 21:40:25','2025-08-13 22:01:40');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('271','16','58',NULL,'1','0','0','0','2025-08-13 21:40:25','2025-11-18 10:56:13');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('272','16','64',NULL,'0','0','0','0','2025-08-13 21:40:25','2025-10-17 13:18:24');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('273','16','79',NULL,'1','0','0','0','2025-08-13 21:40:25','2025-08-13 21:40:34');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('274','16','80',NULL,'1','0','0','0','2025-08-13 21:40:25','2025-08-13 21:40:35');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('275','16','81',NULL,'1','0','0','0','2025-08-13 21:40:25','2025-08-13 21:40:35');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('276','16','84',NULL,'1','1','1','1','2025-08-13 21:40:25','2025-11-18 10:55:31');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('277','16','85',NULL,'1','1','1','1','2025-08-13 21:40:25','2025-08-28 16:48:54');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('278','16','86',NULL,'1','0','0','0','2025-08-13 21:40:25','2025-11-18 10:55:48');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('279','17','35',NULL,'1','1','1','1','2025-08-14 08:45:54','2025-08-14 08:46:03');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('280','17','36',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 12:48:09');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('281','17','37',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 12:48:22');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('282','17','38',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 12:48:19');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('283','17','39',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 12:48:17');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('284','17','40',NULL,'1','1','1','1','2025-08-14 08:45:54','2026-01-22 15:13:53');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('285','17','42',NULL,'1','1','1','1','2025-08-14 08:45:54','2026-01-22 15:13:48');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('286','17','43',NULL,'1','0','0','0','2025-08-14 08:45:54','2025-08-22 15:16:23');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('287','17','44',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 15:16:18');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('288','17','45',NULL,'1','0','0','0','2025-08-14 08:45:54','2025-08-22 15:16:22');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('289','17','48',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 12:48:18');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('291','17','50',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 12:48:21');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('292','17','51',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 12:48:10');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('293','17','52',NULL,'1','1','1','1','2025-08-14 08:45:54','2025-08-14 08:47:36');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('294','17','53',NULL,'1','1','1','1','2025-08-14 08:45:54','2025-08-14 08:47:38');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('295','17','54',NULL,'1','1','1','1','2025-08-14 08:45:54','2025-08-14 08:47:38');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('297','17','58',NULL,'1','1','1','1','2025-08-14 08:45:54','2026-01-22 15:13:51');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('298','17','64',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 12:48:20');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('299','17','79',NULL,'1','0','0','0','2025-08-14 08:45:54','2025-08-22 15:16:50');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('300','17','80',NULL,'0','0','0','0','2025-08-14 08:45:54','2025-08-22 15:16:57');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('301','17','81',NULL,'1','0','0','0','2025-08-14 08:45:54','2025-08-22 15:16:47');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('302','17','84',NULL,'1','1','1','1','2025-08-14 08:45:54','2026-01-22 15:13:57');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('303','17','85',NULL,'1','1','1','1','2025-08-14 08:45:54','2026-01-22 15:13:55');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('304','17','86',NULL,'1','1','1','1','2025-08-14 08:45:54','2026-01-22 15:13:59');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('305','10','92',NULL,'1','1','1','1','2025-08-21 17:58:28','2025-08-21 17:58:28');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('306','10','94',NULL,'1','1','1','1','2025-08-21 22:54:24','2025-08-21 22:54:24');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('307','17','92',NULL,'1','1','1','1','2025-08-21 22:54:50','2025-08-21 22:54:50');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('336','10','99',NULL,'1','1','1','1','2026-08-04 15:02:40','2026-08-04 08:02:40');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('337','12','99',NULL,'0','1','1','1','2026-08-04 15:02:40','2026-08-14 03:27:37');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('338','14','99',NULL,'1','1','1','1','2026-08-04 15:02:41','2026-08-04 08:02:41');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('339','15','99',NULL,'1','1','1','1','2026-08-04 15:02:41','2026-08-04 08:02:41');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('340','16','99',NULL,'1','1','1','1','2026-08-04 15:02:41','2026-08-04 08:02:41');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('341','17','99',NULL,'1','1','1','1','2026-08-04 15:02:41','2026-08-04 08:02:41');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('342','19','35',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('343','19','36',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('344','19','37',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('345','19','38',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('346','19','39',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('347','19','40',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('348','19','42',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('349','19','43',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('350','19','44',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('351','19','45',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('352','19','48',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('354','19','50',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('355','19','51',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('356','19','52',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('357','19','53',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('358','19','54',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('360','19','58',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('361','19','64',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('362','19','79',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('363','19','80',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('364','19','81',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('365','19','84',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('366','19','85',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('367','19','86',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('368','19','92',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('369','19','94',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('370','19','99',NULL,'0','0','0','0','2026-08-04 08:25:56','2026-08-04 08:25:56');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('371','12','64',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('372','12','92',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('373','12','94',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('374','14','92',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('375','14','94',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('376','15','92',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('377','15','94',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('378','16','92',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('379','16','94',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');
INSERT INTO `acl_chi_tiet` (`id_acl_chi_tiet`,`id_acl`,`id_acl_template`,`parms`,`is_read`,`is_write`,`is_delete`,`is_update`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('380','17','94',NULL,'0','0','0','0','2026-08-14 03:45:16','2026-08-14 03:45:16');

DROP TABLE IF EXISTS `acl_tai_khoan`;
CREATE TABLE `acl_tai_khoan` (
  `id_acl_tai_khoan` int(11) NOT NULL AUTO_INCREMENT,
  `id_tai_khoan` int(11) DEFAULT NULL,
  `id_acl` int(11) DEFAULT NULL,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_acl_tai_khoan`)
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('54','668','10','2025-05-13 13:18:31','2025-05-13 13:18:31');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('55','1','11','2025-05-14 12:43:12','2025-05-14 12:43:12');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('58','48','10','2025-05-27 15:27:21','2025-05-27 15:27:21');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('60','50','12','2025-05-29 03:50:50','2025-05-29 03:50:50');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('61','50','13','2025-05-29 03:50:50','2025-05-29 03:50:50');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('62','367','11','2025-06-18 10:37:50','2025-06-18 10:37:50');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('63','303','10','2025-06-18 10:50:40','2025-06-18 10:50:40');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('65','538','11','2025-06-19 15:12:46','2025-06-19 15:12:46');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('67','785','12','2025-06-30 10:07:44','2025-06-30 10:07:44');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('68','626','13','2025-07-03 12:47:50','2025-07-03 12:47:50');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('69','828','12','2025-07-03 12:48:25','2025-07-03 12:48:25');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('78','491','15','2025-07-08 06:58:56','2025-07-08 06:58:56');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('108','842','16','2025-08-13 22:28:24','2025-08-13 22:28:24');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('109','836','16','2025-08-13 22:28:28','2025-08-13 22:28:28');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('111','835','16','2025-08-13 22:28:33','2025-08-13 22:28:33');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('112','837','16','2025-08-13 22:28:35','2025-08-13 22:28:35');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('113','839','16','2025-08-13 22:28:39','2025-08-13 22:28:39');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('114','838','16','2025-08-13 22:28:42','2025-08-13 22:28:42');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('115','840','16','2025-08-13 22:28:45','2025-08-13 22:28:45');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('116','843','16','2025-08-13 22:28:57','2025-08-13 22:28:57');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('118','845','16','2025-08-13 22:29:04','2025-08-13 22:29:04');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('119','846','16','2025-08-13 22:29:07','2025-08-13 22:29:07');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('120','847','16','2025-08-13 22:29:09','2025-08-13 22:29:09');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('122','461','17','2025-08-14 08:50:01','2025-08-14 08:50:01');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('123','850','16','2025-08-20 08:13:09','2025-08-20 08:13:09');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('124','492','10','2025-08-21 08:12:06','2025-08-21 08:12:06');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('127','833','16','2025-08-22 15:18:32','2025-08-22 15:18:32');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('129','849','16','2025-09-19 07:22:43','2025-09-19 07:22:43');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('130','851','16','2025-09-19 07:22:57','2025-09-19 07:22:57');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('131','844','16','2025-09-22 08:47:45','2025-09-22 08:47:45');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('132','514','15','2025-10-01 09:48:39','2025-10-01 09:48:39');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('133','486','10','2025-10-18 14:55:27','2025-10-18 14:55:27');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('134','848','16','2025-10-18 14:57:08','2025-10-18 14:57:08');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('135','841','16','2025-10-18 15:27:09','2025-10-18 15:27:09');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('136','487','10','2025-11-25 07:41:00','2025-11-25 07:41:00');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('137','462','10','2025-11-25 15:39:55','2025-11-25 15:39:55');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('138','493','10','2026-02-23 14:21:11','2026-02-23 14:21:11');
INSERT INTO `acl_tai_khoan` (`id_acl_tai_khoan`,`id_tai_khoan`,`id_acl`,`ngay_tao`,`ngay_cap_nhat`) VALUES ('139','489','10','2026-03-04 15:51:01','2026-03-04 15:51:01');

