# Enspire Learning

Website Learning Management System (LMS) sederhana untuk bimbingan belajar Enspire Learning. Project ini dibangun sebagai aplikasi Laravel full-stack: Laravel menangani backend, autentikasi, validasi, hak akses, database, dan halaman Blade; Tailwind CSS + CSS custom mengatur tampilan; Alpine.js menangani interaksi ringan; Chart.js menampilkan grafik.

> **Status project:** starter aplikasi fungsional untuk dikembangkan dan diuji secara lokal. Source code dan migration disediakan, tetapi dependency `vendor/` dan `node_modules/` tidak disertakan di ZIP. Jalankan proses instalasi pada bagian berikut sebelum membuka aplikasi.

## 1. Teknologi

| Bagian | Teknologi |
|---|---|
| Backend | Laravel 13 |
| Bahasa backend | PHP 8.3 atau lebih baru |
| Frontend | Blade, Tailwind CSS 4, CSS custom, Alpine.js |
| Grafik | Chart.js |
| Database | MySQL |
| Asset bundler | Vite + npm |
| Autentikasi | Laravel session authentication |
| Penyimpanan lampiran | Laravel Storage, disk lokal privat |

Laravel 13 membutuhkan PHP minimal 8.3. Persyaratan resmi dapat dicek di [Laravel Deployment Documentation](https://laravel.com/docs/13.x/deployment). Panduan instalasi resmi: [Laravel Installation](https://laravel.com/docs/13.x/installation).

## 2. Gambaran Arsitektur

Aplikasi menggunakan pola **monolitik full-stack Laravel**. Browser tidak pernah berhubungan langsung dengan MySQL.

```text
Pengguna (browser desktop / tablet / mobile)
                |
                | HTTPS / HTTP request
                v
        Laravel Web Routes
                |
        Authentication Middleware
                |
          Role Middleware
          /           \
      teacher         student
         |                |
  Controller + Policy / validasi / query yang dibatasi
         |                |
      Blade Views + Tailwind + Alpine.js + Chart.js
                |
             Eloquent ORM
                |
             MySQL
                |
  Laravel Storage (file lampiran privat, foto profil publik)
```

### Role aplikasi

1. **Pengajar (`teacher`)**
   - Dashboard pengajar.
   - CRUD seluruh materi lintas mata pelajaran.
   - CRUD jadwal/sesi mengajar yang ditugaskan ke akun pengajar tersebut.
   - Laporan aktivitas mengajar berdasarkan akun yang sedang login.
   - Melihat akun siswa yang sudah tersedia dan menghubungkan akun tersebut dengan mata pelajaran/kelas.
   - Mengubah profil akun sendiri.

2. **Siswa (`student`)**
   - Dashboard siswa.
   - Melihat materi terpublikasi dari mata pelajaran yang dihubungkan ke akunnya.
   - Melihat jadwal yang relevan.
   - Mengelola rencana belajar miliknya sendiri.
   - Melihat dan memperbarui progress materi miliknya sendiri.
   - Mengubah profil pribadi yang diizinkan.

**Tidak ada role Admin dan tidak ada registrasi publik.** Role dibaca dari database, bukan dipilih melalui form login.

### Aturan data yang wajib dipertahankan

- Siswa Matematika tidak boleh membuka materi Fisika meskipun mengetahui URL atau ID materi.
- Pengajar dapat mengelola materi semua mata pelajaran.
- `teaching_sessions.teacher_id` adalah pemilik/penanggung jawab sesi. Halaman laporan dan operasi edit/hapus selalu dibatasi menggunakan ID pengajar dari session login.
- Sesi mengajar dan laporan memakai tabel `teaching_sessions` yang sama agar data jadwal dan laporan tidak tercatat dua kali.
- Tombol **Hubungkan Akun Siswa** pada halaman Siswa tidak membuat akun login baru. Fungsinya memasangkan akun siswa yang sudah ada di database dengan mata pelajaran/kelas.
- Lampiran materi disimpan di storage privat dan diunduh melalui route Laravel yang memeriksa role dan hak akses.

## 3. Struktur Folder Penting

```text
Enspire-Learning/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/                    # Login dan logout
│   │   ├── Teacher/                 # Dashboard, materi, jadwal, siswa, laporan, profil
│   │   └── Student/                 # Dashboard, materi, jadwal, rencana, progress, profil
│   ├── Http/Middleware/RoleMiddleware.php
│   └── Models/                      # Eloquent models dan relasi
├── bootstrap/                       # Bootstrap Laravel 13
├── config/                          # Konfigurasi aplikasi, DB, auth, session, storage
├── database/
│   ├── migrations/                   # Skema database
│   └── seeders/DatabaseSeeder.php    # Akun dan data demo lokal
├── public/                           # Entry point web
├── resources/
│   ├── css/app.css                   # Design system Enspire
│   ├── js/app.js                     # Alpine.js dan Chart.js
│   └── views/                        # Blade views siswa/pengajar
├── routes/web.php
├── storage/                          # Cache, log, dan file aplikasi
├── tests/Feature/                    # Pengujian hak akses dasar
├── .env.example
├── composer.json
├── package.json
├── vite.config.js
└── README.md
```

## 4. Persiapan di Windows dan VS Code

Cara termudah untuk pemula adalah memakai **Laragon** atau lingkungan PHP lokal lain yang menyediakan PHP dan MySQL.

### Software yang dibutuhkan

1. PHP **8.3+** dan ekstensi PHP yang dibutuhkan Laravel, termasuk `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `curl`, `dom`, `filter`, `hash`, dan `session`.
2. Composer 2.
3. MySQL 8.x atau MariaDB yang kompatibel.
4. Node.js 22 LTS (atau versi yang memenuhi persyaratan Vite 7) dan npm.
5. Visual Studio Code.

Cek versi melalui terminal VS Code:

```powershell
php -v
composer --version
node -v
npm -v
```

Pastikan PHP yang aktif di terminal adalah PHP 8.3 atau lebih baru. Jika `php` atau `composer` tidak dikenali, PATH instalasi PHP/Composer belum ditambahkan atau terminal belum dibuka ulang.

### Ekstrak dan buka project

1. Ekstrak file ZIP project ke folder kerja, misalnya `D:\Project\Enspire-Learning`.
2. Buka VS Code.
3. Pilih **File → Open Folder**, lalu pilih folder `Enspire-Learning`.
4. Buka terminal VS Code melalui **Terminal → New Terminal**.
5. Pastikan terminal berada di folder yang berisi `artisan` dan `composer.json`.

### Instal dependency PHP

Jalankan:

```powershell
composer install
```

Composer akan mengunduh Laravel dan dependency PHP ke folder `vendor/`. Folder tersebut memang tidak disertakan di ZIP agar ukuran project tetap ringan.

### Siapkan file environment

Di PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Buka file `.env` di VS Code. Sesuaikan koneksi database, contohnya:

```dotenv
APP_NAME="Enspire Learning"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=enspire_learning
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=file
```

Jika MySQL di komputer kamu menggunakan port berbeda (misalnya `3307`), ubah `DB_PORT` sesuai port MySQL yang aktif. Jangan menebak port: periksa konfigurasi Laragon/XAMPP atau phpMyAdmin.

### Buat database MySQL

1. Jalankan MySQL dari Laragon/XAMPP atau service database yang kamu pakai.
2. Buka phpMyAdmin atau HeidiSQL.
3. Buat database baru dengan nama `enspire_learning`.
4. Gunakan charset `utf8mb4` dan collation `utf8mb4_unicode_ci` jika diminta.
5. Pastikan nama database, username, password, dan port di `.env` sesuai.

Jangan membuat tabel secara manual. Migration Laravel akan membuat tabelnya.

### Migrasi dan isi data demo

Jalankan:

```powershell
php artisan migrate --seed
```

Perintah ini membuat tabel dan memasukkan akun demo, mata pelajaran, kelas, materi, jadwal, rencana belajar, serta progress contoh.

Untuk menghapus semua tabel dan mengisi ulang data demo dari awal (PERHATIAN: seluruh data pada database yang dipakai akan dihapus):

```powershell
php artisan migrate:fresh --seed
```

### Instal dan build frontend

```powershell
npm install
npm run build
```

`npm run build` membuat asset produksi di `public/build`. Jalankan ulang perintah tersebut setelah mengubah CSS/JavaScript jika ingin menguji hasil build terbaru.

Untuk mode development dengan hot reload, buka terminal kedua dan jalankan:

```powershell
npm run dev
```

Biarkan terminal Vite tersebut tetap berjalan selama pengembangan. Di terminal pertama, jalankan Laravel:

```powershell
php artisan serve
```

Buka alamat berikut di browser:

```text
http://127.0.0.1:8000
```

Jika ingin memakai mode development Vite, jangan jalankan `npm run build` bersamaan dengan `npm run dev` untuk sesi yang sama. Pilih salah satu cara.

### Link storage

Untuk menampilkan foto profil yang disimpan di disk publik, jalankan satu kali:

```powershell
php artisan storage:link
```

Lampiran materi tetap disimpan secara privat dan tidak dibuka melalui URL publik.

## 5. Akun Demo untuk Pengujian Lokal

Semua akun demo dibuat oleh `DatabaseSeeder`. Password contoh untuk semua akun adalah `Enspire123!`.

| Role | Email | Password | Mata pelajaran |
|---|---|---|---|
| Pengajar | `teacher@enspire.test` | `Enspire123!` | Mengelola semua materi; laporan berisi sesi Ayu Lestari |
| Pengajar kedua | `teacher2@enspire.test` | `Enspire123!` | Data laporan terpisah untuk pengujian isolasi |
| Siswa | `siswa@enspire.test` | `Enspire123!` | Matematika saja |
| Siswa | `siswa.fisika@enspire.test` | `Enspire123!` | Fisika saja |

**Penting:** akun dan password di atas hanya untuk pengembangan lokal. Jangan gunakan password demo di production. Buat/import akun nyata melalui proses internal yang aman dan paksa penggantian password awal jika aplikasi mulai dipakai pengguna sungguhan.

## 6. Fitur yang Sudah Disiapkan

### Autentikasi dan navigasi

- Login email/password.
- Password di-hash oleh Laravel.
- Regenerasi session setelah login.
- Logout mengakhiri session.
- Redirect otomatis sesuai role.
- Middleware role `teacher` dan `student`.
- Tidak ada halaman registrasi publik.

### Area Pengajar

- Dashboard dengan ringkasan siswa, sesi hari ini, jumlah materi, dan sesi yang perlu perhatian.
- Grafik aktivitas sesi tujuh hari terakhir.
- Daftar jadwal hari ini dan kalender sederhana.
- CRUD materi lintas mata pelajaran.
- Upload beberapa lampiran materi (PDF, PPT/PPTX, DOC/DOCX, JPG/PNG; maksimal 10 MB per file).
- Filter dan pencarian materi.
- Daftar akun siswa yang telah disediakan sebelumnya.
- Menghubungkan akun siswa ke mapel/kelas memakai email akun yang sudah ada.
- CRUD jadwal/sesi mengajar milik pengajar yang login.
- Laporan aktivitas mengajar dengan filter bulan/tahun, ringkasan, grafik, dan ekspor CSV.
- Edit profil akun sendiri.

### Area Siswa

- Dashboard dengan jumlah materi, jadwal, progress, dan materi terbaru.
- Materi hanya dari mata pelajaran yang terhubung ke akun siswa.
- Detail materi dan download lampiran dengan pemeriksaan hak akses.
- Jadwal belajar yang relevan dengan mapel dan kelas siswa.
- CRUD rencana belajar milik siswa yang login.
- Progress belajar per materi untuk siswa yang login.
- Edit profil akun sendiri.

### Tentang progress materi

Saat siswa pertama kali membuka detail materi, sistem membuat catatan progress awal `Sedang Dipelajari` (10%). Pada halaman Progress, siswa dapat memilih status `Belum Dimulai` (0%), `Sedang Dipelajari` (50%), atau `Selesai` (100%). Persentase keseluruhan dihitung dari jumlah materi terpublikasi yang selesai dibandingkan total materi yang bisa diakses siswa.

## 7. Pengujian yang Disarankan

Jalankan test suite:

```powershell
php artisan test
```

Ada pengujian feature untuk beberapa pembatasan inti:

- Siswa tidak dapat membuka laporan pengajar.
- Pengajar tidak dapat membuka area progress siswa.
- Siswa tidak dapat membuka materi dari mata pelajaran yang tidak diikutinya.
- Pengajar tidak dapat mengedit sesi milik pengajar lain.

Uji manual juga dengan dua jenis akun:

1. Login sebagai `siswa@enspire.test`. Pastikan daftar materi hanya Matematika.
2. Coba buka URL materi Fisika milik siswa lain. Akses harus ditolak.
3. Logout lalu login sebagai `teacher@enspire.test`. Pastikan materi semua mata pelajaran terlihat.
4. Login sebagai `teacher2@enspire.test`. Pastikan halaman laporan berisi sesi pengajar kedua, bukan sesi Ayu Lestari.
5. Ubah angka ID sesi di URL edit menjadi ID sesi pengajar lain. Server harus menolak akses.
6. Coba akses URL `/teacher/reports` dengan akun siswa. Server harus menolak akses.
7. Coba akses URL `/student/progress` dengan akun pengajar. Server harus menolak akses.

Kamu juga dapat melihat daftar route:

```powershell
php artisan route:list
```

## 8. Troubleshooting

### `php` is not recognized / `php` tidak dikenali

- Pastikan PHP sudah terpasang.
- Tambahkan folder PHP ke Environment Variables → Path.
- Tutup dan buka ulang VS Code/terminal.
- Jalankan `php -v` lagi.

### `composer` is not recognized

- Install Composer untuk Windows.
- Saat installer meminta lokasi PHP, arahkan ke `php.exe` versi 8.3+.
- Buka terminal baru, lalu jalankan `composer --version`.

### `could not find driver` atau `could not find pdo driver`

- Buka `php.ini` aktif yang ditunjukkan oleh `php --ini`.
- Aktifkan extension `pdo_mysql` (hapus tanda `;` di depan `extension=pdo_mysql`).
- Restart terminal dan web server, lalu cek `php -m`.

### `SQLSTATE[HY000] [2002] Connection refused`

- Pastikan MySQL sedang berjalan.
- Periksa `DB_HOST` dan `DB_PORT` di `.env`.
- Pastikan database `enspire_learning` sudah dibuat.
- Setelah mengubah `.env`, jalankan `php artisan config:clear`.

### `No application encryption key has been specified`

Jalankan:

```powershell
php artisan key:generate
```

### `Vite manifest not found`

Pastikan dependency npm sudah terpasang dan build berhasil:

```powershell
npm install
npm run build
```

### Foto profil tidak muncul

Jalankan:

```powershell
php artisan storage:link
```

Pastikan file foto tersimpan di `storage/app/public/profile-photos`.

### Perubahan `.env` tidak terbaca

Jalankan:

```powershell
php artisan optimize:clear
```

## 9. Deployment (Jangan Langsung Upload Folder Mentah)

Aplikasi Laravel dapat dipindahkan ke shared hosting PHP/MySQL atau platform cloud yang mendukung Laravel. Sebelum memilih provider, pastikan tersedia:

- PHP minimal 8.3 dan extension yang dibutuhkan.
- MySQL/MariaDB.
- Composer atau cara mengunggah `vendor/` hasil Composer install.
- HTTPS/SSL.
- Penyimpanan file dan backup berkala.
- Document root website diarahkan ke folder `public/` Laravel, bukan root project.

Garis besar deployment:

1. Push source code ke repository Git privat (jangan commit `.env`, `vendor/`, `node_modules/`, file database, atau data pribadi siswa).
2. Siapkan database production.
3. Upload/deploy source code dan jalankan `composer install --no-dev --optimize-autoloader`.
4. Buat `.env` production dengan `APP_ENV=production`, `APP_DEBUG=false`, URL HTTPS, dan kredensial database yang aman.
5. Jalankan `php artisan key:generate` hanya saat menyiapkan aplikasi pertama kali. Jangan mengganti `APP_KEY` pada deployment berikutnya jika aplikasi sudah memiliki data terenkripsi/session aktif.
6. Jalankan `php artisan migrate --force`.
7. Jalankan `npm ci && npm run build` saat build tersedia di server/build pipeline.
8. Jalankan `php artisan storage:link` bila diperlukan untuk foto profil.
9. Atur document root ke `public/`, aktifkan HTTPS, permission storage/bootstrap cache, dan backup database/file.
10. Buat akun production dengan password baru; jangan jalankan seeder demo di database production.

Perintah optimasi setelah konfigurasi production:

```bash
php artisan optimize
```

Untuk membersihkan cache konfigurasi/rute jika ada perubahan konfigurasi:

```bash
php artisan optimize:clear
```

Baca panduan resmi sebelum deploy: [Laravel Deployment](https://laravel.com/docs/13.x/deployment). Jangan menaruh `.env`, source code, atau file database ke direktori publik. Web server harus melayani folder `public/` saja.

## 10. Catatan Pengembangan dan Batasan Versi Awal

- Tampilan dibangun ulang dengan acuan visual PDF Figma yang dikirim. Komposisi warna, sidebar, kartu, tabel, kalender, dan pola halaman diarahkan ke mockup; detail spacing/komponen masih dapat disesuaikan setelah dibandingkan berdampingan dengan Figma di browser.
- Kalender pada versi awal adalah kalender bulan berjalan. Navigasi bulan dan sinkronisasi kalender penuh dapat ditambahkan berikutnya.
- Filter jadwal dan materi sudah disediakan dalam bentuk dasar; kebutuhan filter lebih rinci dapat disesuaikan dengan data riil Enspire.
- Halaman pengelolaan siswa hanya menghubungkan akun siswa existing ke mapel/kelas. Pembuatan kredensial baru tetap dilakukan melalui database/seeder/proses internal, bukan registrasi publik.
- Pengingat bentrok jadwal, notifikasi otomatis, reset password email, dan audit log belum termasuk versi awal.
- Sebelum dipakai oleh siswa sungguhan, lakukan review keamanan, pengujian alur lengkap, backup, penggantian password demo, dan pemeriksaan konfigurasi hosting.

## 11. Referensi Resmi

- Laravel 13 Installation: https://laravel.com/docs/13.x/installation
- Laravel 13 Deployment: https://laravel.com/docs/13.x/deployment
- Laravel 13 Routing: https://laravel.com/docs/13.x/routing
- Laravel 13 Authentication: https://laravel.com/docs/13.x/authentication
- Laravel 13 File Storage: https://laravel.com/docs/13.x/filesystem
