CREATE DATABASE IF NOT EXISTS absen_rs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE absen_rs;

CREATE TABLE IF NOT EXISTS tb_roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(100) NOT NULL,
    level TINYINT UNSIGNED NOT NULL,
    permissions TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_units (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_unit VARCHAR(120) NOT NULL,
    parent_id INT UNSIGNED NULL,
    kepala_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_pegawai (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unit_id INT UNSIGNED NULL,
    nip VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    no_hp VARCHAR(30) NULL,
    jabatan VARCHAR(120) NULL,
    tipe_kerja ENUM('SHIFT', 'NON_SHIFT') NOT NULL DEFAULT 'NON_SHIFT',
    status ENUM('AKTIF', 'NONAKTIF', 'CUTI_PANJANG', 'MAGANG') NOT NULL DEFAULT 'AKTIF',
    tanggal_masuk DATE NULL,
    foto_profil VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_pegawai_unit FOREIGN KEY (unit_id) REFERENCES tb_units(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT UNSIGNED NULL,
    role_id INT UNSIGNED NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES tb_roles(id),
    CONSTRAINT fk_users_pegawai FOREIGN KEY (pegawai_id) REFERENCES tb_pegawai(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_shift (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_shift VARCHAR(100) NOT NULL,
    jam_masuk TIME NOT NULL,
    jam_keluar TIME NOT NULL,
    toleransi_menit SMALLINT UNSIGNED NOT NULL DEFAULT 15,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_jadwal (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT UNSIGNED NOT NULL,
    shift_id INT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('DRAFT', 'PUBLISHED', 'REVISI') NOT NULL DEFAULT 'PUBLISHED',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_jadwal_pegawai FOREIGN KEY (pegawai_id) REFERENCES tb_pegawai(id),
    CONSTRAINT fk_jadwal_shift FOREIGN KEY (shift_id) REFERENCES tb_shift(id),
    UNIQUE KEY uniq_jadwal_pegawai_tanggal (pegawai_id, tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_absensi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk DATETIME NULL,
    jam_keluar DATETIME NULL,
    foto_masuk VARCHAR(255) NULL,
    foto_keluar VARCHAR(255) NULL,
    latitude_masuk DECIMAL(10, 7) NULL,
    longitude_masuk DECIMAL(10, 7) NULL,
    latitude_keluar DECIMAL(10, 7) NULL,
    longitude_keluar DECIMAL(10, 7) NULL,
    status ENUM('HADIR', 'TERLAMBAT', 'ABSEN', 'IZIN', 'CUTI', 'LIBUR', 'LEMBUR') NOT NULL DEFAULT 'HADIR',
    catatan VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_absensi_pegawai FOREIGN KEY (pegawai_id) REFERENCES tb_pegawai(id),
    UNIQUE KEY uniq_absensi_pegawai_tanggal (pegawai_id, tanggal),
    KEY idx_absensi_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_lokasi_absensi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unit_id INT UNSIGNED NULL,
    nama_lokasi VARCHAR(120) NOT NULL,
    latitude DECIMAL(10, 7) NOT NULL,
    longitude DECIMAL(10, 7) NOT NULL,
    radius_meter SMALLINT UNSIGNED NOT NULL DEFAULT 100,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lokasi_unit FOREIGN KEY (unit_id) REFERENCES tb_units(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_cuti (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT UNSIGNED NOT NULL,
    approver_id INT UNSIGNED NULL,
    jenis_cuti VARCHAR(100) NOT NULL,
    tgl_mulai DATE NOT NULL,
    tgl_selesai DATE NOT NULL,
    status ENUM('PENDING', 'APPROVED_UNIT', 'APPROVED_HR', 'DITOLAK', 'BATAL') NOT NULL DEFAULT 'PENDING',
    catatan TEXT NULL,
    dokumen_pendukung VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_cuti_pegawai FOREIGN KEY (pegawai_id) REFERENCES tb_pegawai(id),
    CONSTRAINT fk_cuti_approver FOREIGN KEY (approver_id) REFERENCES tb_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_saldo_cuti (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT UNSIGNED NOT NULL,
    tahun YEAR NOT NULL,
    saldo_tahunan TINYINT UNSIGNED NOT NULL DEFAULT 12,
    terpakai_tahunan TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_saldo_pegawai FOREIGN KEY (pegawai_id) REFERENCES tb_pegawai(id),
    UNIQUE KEY uniq_saldo_pegawai_tahun (pegawai_id, tahun)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_konfigurasi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(120) NOT NULL UNIQUE,
    config_value TEXT NOT NULL,
    kategori VARCHAR(60) NOT NULL,
    updated_by INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_konfigurasi_user FOREIGN KEY (updated_by) REFERENCES tb_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_audit_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    aksi VARCHAR(100) NOT NULL,
    nama_tabel VARCHAR(120) NOT NULL,
    data_lama LONGTEXT NULL,
    data_baru LONGTEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES tb_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tb_roles (id, nama_role, level, permissions) VALUES
(1, 'Super Admin', 1, JSON_OBJECT('full_access', true)),
(2, 'Admin HR', 2, JSON_OBJECT('pegawai', true, 'jadwal', true, 'cuti', true)),
(3, 'Kepala Unit', 3, JSON_OBJECT('approval_unit', true, 'unit_only', true)),
(4, 'Pegawai', 4, JSON_OBJECT('self_service', true)),
(5, 'Auditor', 5, JSON_OBJECT('read_only', true))
ON DUPLICATE KEY UPDATE nama_role = VALUES(nama_role), level = VALUES(level), permissions = VALUES(permissions);

INSERT INTO tb_units (id, nama_unit) VALUES
(1, 'Manajemen RS'),
(2, 'IGD'),
(3, 'Rawat Inap'),
(4, 'Poliklinik')
ON DUPLICATE KEY UPDATE nama_unit = VALUES(nama_unit);

INSERT INTO tb_pegawai (id, unit_id, nip, nama, email, no_hp, jabatan, tipe_kerja, status, tanggal_masuk) VALUES
(1, 1, 'ADM0001', 'Super Admin RS', 'superadmin@absen.local', '081234567890', 'IT Manager', 'NON_SHIFT', 'AKTIF', '2026-01-01'),
(2, 2, 'NRS0002', 'Nadia Perawat', 'nadia@absen.local', '081234567891', 'Perawat IGD', 'SHIFT', 'AKTIF', '2026-01-15')
ON DUPLICATE KEY UPDATE nama = VALUES(nama), email = VALUES(email), unit_id = VALUES(unit_id);

INSERT INTO tb_users (id, pegawai_id, role_id, email, password, is_active) VALUES
(1, 1, 1, 'superadmin@absen.local', '$2y$12$nJclYRGUDRN4HdP67M76E.j17r0TYfqYtXZKvPpWfpXavv2r9eYIO', 1),
(2, 2, 4, 'nadia@absen.local', '$2y$12$nJclYRGUDRN4HdP67M76E.j17r0TYfqYtXZKvPpWfpXavv2r9eYIO', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email), password = VALUES(password), role_id = VALUES(role_id);

INSERT INTO tb_shift (id, nama_shift, jam_masuk, jam_keluar, toleransi_menit) VALUES
(1, 'Pagi', '07:00:00', '15:00:00', 15),
(2, 'Siang', '15:00:00', '23:00:00', 15),
(3, 'Malam', '23:00:00', '07:00:00', 15),
(4, 'Reguler', '08:00:00', '16:00:00', 15)
ON DUPLICATE KEY UPDATE nama_shift = VALUES(nama_shift), jam_masuk = VALUES(jam_masuk), jam_keluar = VALUES(jam_keluar);

INSERT INTO tb_jadwal (pegawai_id, shift_id, tanggal, status) VALUES
(1, 4, CURDATE(), 'PUBLISHED'),
(2, 1, CURDATE(), 'PUBLISHED'),
(2, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'PUBLISHED'),
(2, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'PUBLISHED')
ON DUPLICATE KEY UPDATE shift_id = VALUES(shift_id), status = VALUES(status);

INSERT INTO tb_absensi (pegawai_id, tanggal, jam_masuk, status, latitude_masuk, longitude_masuk, catatan) VALUES
(1, CURDATE(), CONCAT(CURDATE(), ' 07:05:00'), 'HADIR', -6.2000000, 106.8166660, 'Check-in demo')
ON DUPLICATE KEY UPDATE jam_masuk = VALUES(jam_masuk), status = VALUES(status);

INSERT INTO tb_saldo_cuti (pegawai_id, tahun, saldo_tahunan, terpakai_tahunan) VALUES
(1, YEAR(CURDATE()), 12, 2),
(2, YEAR(CURDATE()), 12, 1)
ON DUPLICATE KEY UPDATE saldo_tahunan = VALUES(saldo_tahunan), terpakai_tahunan = VALUES(terpakai_tahunan);

INSERT INTO tb_cuti (pegawai_id, approver_id, jenis_cuti, tgl_mulai, tgl_selesai, status, catatan) VALUES
(2, 1, 'TAHUNAN', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 8 DAY), 'PENDING', 'Cuti keluarga'),
(1, 1, 'PENTING', DATE_SUB(CURDATE(), INTERVAL 14 DAY), DATE_SUB(CURDATE(), INTERVAL 14 DAY), 'APPROVED_HR', 'Keperluan administrasi')
ON DUPLICATE KEY UPDATE status = VALUES(status), catatan = VALUES(catatan);

INSERT INTO tb_lokasi_absensi (unit_id, nama_lokasi, latitude, longitude, radius_meter, is_active) VALUES
(1, 'Gedung Utama RS', -6.2000000, 106.8166660, 100, 1)
ON DUPLICATE KEY UPDATE latitude = VALUES(latitude), longitude = VALUES(longitude), radius_meter = VALUES(radius_meter);

INSERT INTO tb_konfigurasi (config_key, config_value, kategori, updated_by) VALUES
('app_name', 'Absensi RS', 'umum', 1),
('default_attendance_radius', '100', 'absensi', 1),
('office_latitude', '-6.200000', 'absensi', 1),
('office_longitude', '106.816666', 'absensi', 1),
('leave_type_tahunan', JSON_OBJECT('kode', 'TAHUNAN', 'nama', 'Cuti Tahunan', 'jatah', 12, 'aktif', 1, 'potong_kuota', 1, 'keterangan', 'Jatah cuti tahunan reguler'), 'cuti_jenis', 1),
('leave_type_sakit', JSON_OBJECT('kode', 'SAKIT', 'nama', 'Cuti Sakit', 'jatah', 12, 'aktif', 1, 'potong_kuota', 0, 'keterangan', 'Tidak memotong kuota tahunan'), 'cuti_jenis', 1),
('leave_type_melahirkan', JSON_OBJECT('kode', 'MELAHIRKAN', 'nama', 'Cuti Melahirkan', 'jatah', 90, 'aktif', 1, 'potong_kuota', 0, 'keterangan', 'Jatah hari kalender default'), 'cuti_jenis', 1),
('leave_type_duka', JSON_OBJECT('kode', 'DUKA', 'nama', 'Cuti Duka', 'jatah', 3, 'aktif', 1, 'potong_kuota', 0, 'keterangan', 'Cuti kedukaan'), 'cuti_jenis', 1),
('leave_type_penting', JSON_OBJECT('kode', 'PENTING', 'nama', 'Cuti Penting', 'jatah', 5, 'aktif', 1, 'potong_kuota', 0, 'keterangan', 'Keperluan penting'), 'cuti_jenis', 1),
('leave_type_tanpa_keterangan', JSON_OBJECT('kode', 'TANPA_KETERANGAN', 'nama', 'Tanpa Keterangan', 'jatah', 0, 'aktif', 1, 'potong_kuota', 0, 'keterangan', 'Digunakan untuk status khusus'), 'cuti_jenis', 1)
ON DUPLICATE KEY UPDATE config_value = VALUES(config_value), updated_by = VALUES(updated_by);
