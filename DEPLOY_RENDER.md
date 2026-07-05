# Panduan Deploy WebGIS Bajo ke Render.com

## 1. Hasil analisis proyek

- Framework: **Laravel 13**, PHP **8.4**, front-end di-build dengan **Vite + Tailwind**.
- Database: **MySQL** (`DB_CONNECTION=mysql` di `.env.example`).
- Sebelumnya proyek ini disiapkan untuk **Railway**: ada `Procfile` (`php artisan serve --host=0.0.0.0 --port=$PORT`) dan `nixpacks.toml` yang menginstall PHP 8.4 + ekstensi + Node lalu build & migrate.
- Logika spasial (point-in-polygon di `app/Support/GeoHelper.php`) dihitung **di kode PHP**, bukan lewat fungsi spasial MySQL (`ST_*`, kolom `GEOMETRY`, dll). Data GeoJSON disimpan sebagai teks biasa di kolom `text`/`json`. Ini kabar baik: **proyek ini tidak terikat erat ke MySQL**, jadi cukup portable kalau mau pindah database.
- Upload gambar sekolah/wilayah (`SekolahController`, `WilayahDesaController`) disimpan langsung ke folder `public/img/sekolah` dan `public/img/wilayah` di server, **bukan** lewat `Storage`/S3.
- Repo sudah ada di GitHub: `ZaidPutra24/webgis-bajo` — bagus, tinggal disambungkan ke Render.

## 2. Dua hal penting sebelum pindah ke Render

### a. Render **tidak punya MySQL managed** sama sekali
Layanan database managed Render hanya **PostgreSQL** dan **Redis/Key-Value**. Tidak ada opsi "Create MySQL Database" di dashboard Render. Ini beda dengan Railway yang bisa provision MySQL langsung.

### b. Render tidak punya buildpack/Nixpacks native untuk PHP
Render native mendukung Node, Python, Ruby, Go, Rust, Elixir, atau **Docker**. Untuk Laravel, jalur yang didukung adalah **Web Service berbasis Docker** — jadi `Procfile` dan `nixpacks.toml` yang lama tidak akan otomatis dipakai Render dan perlu diganti dengan `Dockerfile`.

### c. Disk pada free Web Service bersifat *ephemeral*
Artinya setiap kali service di-redeploy atau restart, isi filesystem container di-reset ke kondisi image. Gambar yang **sudah ada di repo** (63 file di `public/img/sekolah`, 9 di `public/img/wilayah`) tetap aman karena ikut ter-build ke image. Tapi **gambar baru yang diupload lewat panel admin setelah deploy akan hilang** saat container restart. Untuk pemakaian jangka panjang, sebaiknya nanti dipindah ke storage eksternal (Cloudinary, Supabase Storage, atau S3-compatible) — tapi ini bisa menyusul, tidak menghalangi deploy awal.

> Catatan: kebijakan detail free tier Render (batas jam aktif, masa berlaku database gratis, dll.) bisa berubah — cek halaman pricing/limit resmi Render sebelum memutuskan supaya sesuai kondisi terbaru.

## 3. Rekomendasi jalur

| | **Opsi A — Migrasi ke PostgreSQL (disarankan)** | **Opsi B — Tetap MySQL** |
|---|---|---|
| Database | Postgres gratis dari Render (satu ekosistem) | Harus cari MySQL host eksternal (Render tidak sediakan) |
| Effort kode | Kecil — hanya ganti 3 baris SQL khusus MySQL di seeder | Nol perubahan kode |
| Keandalan | Tinggi, resmi didukung Render | Tergantung pihak ketiga, banyak layanan MySQL gratis (db4free, FreeSQLDatabase, dll.) tidak stabil/kecil kapasitasnya untuk produksi |

Karena aplikasi ini **tidak memakai fitur khusus MySQL** di level database (semua logika GIS ada di PHP), migrasi ke PostgreSQL adalah pilihan paling murah dan paling stabil untuk hosting gratis di Render. Panduan di bawah fokus ke **Opsi A**, dengan catatan untuk Opsi B di bagian akhir.

## 4. Persiapan kode (lakukan sekali, lalu push ke GitHub)

### 4.1 Tambahkan 3 file ini ke root proyek
Sudah saya siapkan dan bisa diunduh di chat ini:
- `Dockerfile`
- `docker-entrypoint.sh`
- `.dockerignore`

Taruh ketiganya di root folder proyek (sejajar dengan `composer.json`).

### 4.2 Ganti kode khusus-MySQL di 3 seeder (untuk Opsi A)
Di `database/seeders/SekolahSeeder.php`, `WilayahSeeder.php`, dan `StatistikSekolahSeeder.php` ada baris:

```php
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Xxx::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
```

`SET FOREIGN_KEY_CHECKS` adalah sintaks khusus MySQL, tidak dikenali PostgreSQL. Ganti dengan helper Laravel yang sudah database-agnostic:

```php
use Illuminate\Support\Facades\Schema;

Schema::disableForeignKeyConstraints();
Xxx::truncate();
Schema::enableForeignKeyConstraints();
```

Lakukan ini di ketiga file (tinggal cari-ganti, isinya identik di ketiganya, hanya beda nama model).

### 4.3 Commit & push
```bash
git add Dockerfile docker-entrypoint.sh .dockerignore database/seeders/SekolahSeeder.php database/seeders/WilayahSeeder.php database/seeders/StatistikSekolahSeeder.php
git commit -m "chore: siapkan Docker untuk deploy di Render + database-agnostic seeder"
git push origin main
```

### 4.4 Siapkan APP_KEY
Generate satu kali di lokal (butuh PHP terpasang), lalu simpan nilainya — nanti dipakai sebagai Environment Variable di Render, **bukan** di-generate ulang tiap boot (supaya sesi & data terenkripsi tetap valid antar-restart):
```bash
php artisan key:generate --show
```
Simpan output-nya, contoh: `base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=`

## 5. Langkah-langkah di Render.com

### 5.1 Buat database PostgreSQL
1. Login ke dashboard Render → **New +** → **PostgreSQL**.
2. Isi nama (mis. `webgis-bajo-db`), pilih plan **Free**, region terdekat (mis. Singapore).
3. Setelah dibuat, buka halaman database tersebut dan catat: **Hostname**, **Port**, **Database**, **Username**, **Password** (atau salin "Internal Database URL"-nya).

### 5.2 Buat Web Service
1. **New +** → **Web Service** → hubungkan akun GitHub → pilih repo `ZaidPutra24/webgis-bajo`.
2. Pada **Environment**, pilih **Docker** (Render otomatis mendeteksi `Dockerfile` di root repo).
3. Plan: **Free**.
4. Biarkan Build Command & Start Command kosong — semuanya sudah diatur lewat `Dockerfile` + `docker-entrypoint.sh`.

### 5.3 Isi Environment Variables di Web Service
Di tab **Environment**, tambahkan:

| Key | Value |
|---|---|
| `APP_NAME` | `WebGIS Bajo` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | (hasil `php artisan key:generate --show` di langkah 4.4) |
| `APP_URL` | `https://<nama-service-anda>.onrender.com` (isi setelah tahu URL-nya dari Render, boleh update belakangan) |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | Hostname database Postgres (langkah 5.1) |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | Nama database (langkah 5.1) |
| `DB_USERNAME` | Username database |
| `DB_PASSWORD` | Password database |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `LOG_CHANNEL` | `stack` |
| `RUN_SEED` | `true` **(hanya untuk deploy pertama, lihat catatan di bawah)** |

> **Catatan `RUN_SEED`:** karena seeder Anda pakai `truncate()` lalu insert ulang tiap kali dijalankan, jangan biarkan `RUN_SEED=true` selamanya — kalau tidak, setiap redeploy/restart akan mengosongkan lalu mengisi ulang data dari seeder (data yang diinput manual lewat panel admin ikut terhapus). Setelah deploy pertama sukses dan data sudah masuk, ubah `RUN_SEED` menjadi `false`.

### 5.4 Deploy
Klik **Create Web Service**. Render akan:
1. Build image dari `Dockerfile` (composer install, npm build).
2. Menjalankan `docker-entrypoint.sh` → cache config, `migrate --force`, (jika `RUN_SEED=true`) `db:seed --force`, lalu start server di `$PORT`.

Pantau log deploy di tab **Logs**. Kalau berhasil, buka URL `https://<nama-service>.onrender.com`.

## 6. Verifikasi setelah deploy
- Cek halaman utama & peta Leaflet tampil dengan data sekolah/wilayah.
- Login ke panel admin (`admin@webgis.com` / password sesuai `DatabaseSeeder.php`) dan **segera ganti password** karena kredensial ini ada di kode publik GitHub.
- Coba tambah/edit data untuk memastikan koneksi database jalan.
- Set `RUN_SEED=false` di Environment Variables setelah data awal berhasil masuk.

## 7. Keterbatasan free tier yang perlu diwaspadai
- **Cold start**: Web Service gratis Render "tidur" setelah idle beberapa menit, request pertama setelah itu akan lambat (container perlu bangun lagi).
- **Ephemeral disk**: seperti disebut di bagian 2c — upload gambar baru lewat admin panel tidak permanen di free tier.
- **Masa berlaku database gratis / batas jam runtime**: bisa berubah sewaktu-waktu sesuai kebijakan Render — cek dashboard/pricing page saat ini untuk detail terbaru.

## 8. Kalau tetap ingin pakai MySQL (Opsi B)

Jika tidak ingin migrasi ke PostgreSQL, alurnya:
1. Buat database MySQL di layanan eksternal (mis. Aiven, Clever Cloud, atau provider MySQL gratis/berbayar murah lainnya) — cek dulu kuota storage & kebijakan masa aktifnya, karena banyak tier gratis MySQL pihak ketiga cukup terbatas/tidak stabil untuk jangka panjang.
2. Tetap gunakan `Dockerfile` yang sama (sudah menyertakan ekstensi `pdo_mysql`).
3. Lewati langkah 4.2 (tidak perlu ubah seeder).
4. Di Environment Variables, set `DB_CONNECTION=mysql`, `DB_PORT=3306`, dan isi host/user/password/nama database sesuai kredensial dari provider eksternal tersebut.

---

**File yang perlu Anda tambahkan ke repo:** `Dockerfile`, `docker-entrypoint.sh`, `.dockerignore` (disediakan terpisah di chat ini).
