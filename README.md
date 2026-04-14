# Absensi RS

Fondasi aplikasi absensi rumah sakit berbasis `CodeIgniter 3`, `PHP 7.4`, `MySQL/MariaDB`, dan UI `Tailwind CSS` dengan fokus tampilan mobile.

## Fitur Fondasi

- Login dan session management
- Dashboard mobile-first
- Halaman absensi dengan placeholder kamera dan GPS
- Halaman jadwal shift
- Halaman cuti
- Halaman master pegawai
- Schema database + seed data demo

## Kebutuhan

- PHP `7.4+`
- MySQL/MariaDB `5.7+` atau `10.4+`
- Apache/Nginx dengan `mod_rewrite`

## Setup Cepat

1. Import database:

```bash
mysql -u root < database/schema.sql
```

2. Jika perlu, set environment:

```bash
export APP_BASE_URL="http://localhost:8080/"
export DB_HOST="localhost"
export DB_NAME="absen_rs"
export DB_USER="root"
export DB_PASS=""
export APP_KEY="ganti-dengan-kunci-aman"
```

3. Jalankan server lokal:

```bash
php -S 0.0.0.0:8080
```

4. Buka:

```text
http://localhost:8080
```

## Akun Demo

- `superadmin@absen.local` / `Admin@12345`
- `nadia@absen.local` / `Admin@12345`

## Catatan

- UI saat ini sudah mobile-first dan siap jadi dasar pengembangan lebih lanjut.
- Integrasi kamera `getUserMedia`, geolocation browser, check-in/out POST, approval cuti, dan CRUD penuh masih tahap berikutnya.
