# Siger ZeroNet

Project Laravel untuk dashboard SIG Carbon Trade / Siger ZeroNet Kota Bandar Lampung. Aplikasi ini menampilkan dashboard emisi dan absorpsi carbon, peta choropleth, halaman carbon trade, serta CRUD data carbon untuk admin.

## Fitur

- Dashboard ringkasan emisi, absorpsi, selisih carbon, dan kredit carbon.
- Peta Emisi per kecamatan.
- Peta Absorpsi per kecamatan.
- Tabel Carbon Trade.
- Login dan logout admin.
- CRUD Data Carbon menggunakan tabel `carbon_data`.

## Route

Route public:

- `/`
- `/dashboard`
- `/peta-emisi`
- `/peta-absorpsi`
- `/carbon-trade`
- `/login`

Route wajib login:

- `/data-carbon`
- `/data-carbon/create`
- `/data-carbon/{id}/edit`
- POST/PUT/DELETE `/data-carbon`
- `/logout`

## Akun Admin Default

Jalankan seeder agar akun ini tersedia:

- Email: `admin@sigerzeronet.local`
- Password: `admin12345`

## Kebutuhan

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- PostgreSQL
- PostGIS extension

Project ini memakai query spasial `ST_AsGeoJSON(geom)`, jadi database harus mendukung PostGIS.

## Cara Clone dan Setup

Clone repository:

```bash
git clone https://github.com/hier30/siger-zeronet.git
cd siger-zeronet
```

Install dependency PHP dan JavaScript:

```bash
composer install
npm install
```

Copy file environment:

```bash
cp .env.example .env
```

Untuk Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate app key:

```bash
php artisan key:generate
```

## Konfigurasi Database

Buat database PostgreSQL, misalnya:

```sql
CREATE DATABASE siger_zeronet;
```

Masuk ke database tersebut, lalu aktifkan PostGIS:

```sql
CREATE EXTENSION IF NOT EXISTS postgis;
```

Edit `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=siger_zeronet
DB_USERNAME=postgres
DB_PASSWORD=password_database_kamu
```

## Data Kecamatan

Penting: project ini membutuhkan tabel `kecamatan` dengan minimal kolom:

- `id`
- `kecamatan`
- `geom`

Migration `carbon_data` memiliki foreign key ke tabel `kecamatan`, dan halaman peta membaca kolom `geom`. Jadi sebelum menjalankan migration `carbon_data`, pastikan tabel `kecamatan` sudah ada dan berisi data geometri kecamatan.

Jika tabel `kecamatan` belum ada, minta file dump SQL/database dari anggota tim yang punya data GIS project ini, lalu import ke database `siger_zeronet`.

Contoh import dump PostgreSQL:

```bash
psql -U postgres -d siger_zeronet -f path/ke/dump_kecamatan.sql
```

## Migration dan Seeder

Setelah database dan tabel `kecamatan` siap:

```bash
php artisan migrate
php artisan db:seed
```

Seeder akan membuat akun admin default. Seeder juga akan membuat data `carbon_data` contoh jika tabel `carbon_data` masih kosong dan tabel `kecamatan` sudah berisi data.

## Seeder Data 2026

Seeder 2026 membaca data per kecamatan dari tahun 2025. Jika data 2025 belum tersedia untuk kecamatan tertentu, seeder memakai data tahun terakhir sebelum 2026 yang tersedia.

Jalankan:

```bash
php artisan db:seed --class=CarbonData2026Seeder
```

Seeder ini aman dijalankan ulang. Jika data 2026 untuk suatu kecamatan sudah ada, data tersebut akan dilewati dan tidak dibuat duplikat.

## Menjalankan Project

Jalankan Laravel server:

```bash
php artisan serve
```

Jalankan Vite di terminal lain:

```bash
npm run dev
```

Buka aplikasi:

```text
http://127.0.0.1:8000
```

## Build Asset untuk Produksi

Jika ingin membuat asset production:

```bash
npm run build
```

## Deploy Railway + Supabase

Project ini bisa dideploy dengan Railway sebagai hosting Laravel dan Supabase sebagai database PostgreSQL/PostGIS.

### 1. Siapkan Supabase

1. Buka https://supabase.com dan login.
2. Klik **New project**.
3. Isi:
   - **Name**: `siger-zeronet`
   - **Database Password**: buat password dan simpan baik-baik
   - **Region**: pilih yang paling dekat, misalnya Singapore jika tersedia
4. Tunggu project selesai dibuat.
5. Masuk ke menu **SQL Editor**.
6. Jalankan SQL ini untuk mengaktifkan PostGIS:

```sql
CREATE EXTENSION IF NOT EXISTS postgis;
```

7. Pastikan extension aktif dengan menjalankan:

```sql
SELECT PostGIS_Version();
```

Kalau keluar versi PostGIS, berarti database siap untuk data peta.

### 2. Import Data Kecamatan ke Supabase

Project ini wajib punya tabel `kecamatan` karena peta membaca kolom `geom`.

Minimal kolom yang dibutuhkan:

- `id`
- `kecamatan`
- `geom`

Cara paling aman adalah minta file dump SQL tabel `kecamatan` dari database lokal/teman, misalnya:

```text
dump_kecamatan.sql
```

Lalu import ke Supabase. Ada dua cara:

#### Opsi A: Import lewat SQL Editor

1. Buka file `dump_kecamatan.sql`.
2. Copy isi SQL-nya.
3. Paste ke Supabase **SQL Editor**.
4. Klik **Run**.

#### Opsi B: Import lewat terminal

Ambil connection string Supabase:

1. Buka Supabase project.
2. Masuk ke **Project Settings**.
3. Pilih **Database**.
4. Cari bagian **Connection string**.
5. Pilih mode **Session pooler** atau **Direct connection** untuk import dari lokal.

Contoh command:

```bash
psql "postgresql://USER:PASSWORD@HOST:PORT/postgres" -f path/ke/dump_kecamatan.sql
```

Setelah import, cek tabel:

```sql
SELECT id, kecamatan, ST_AsGeoJSON(geom)
FROM kecamatan
LIMIT 1;
```

Kalau query itu berhasil, data kecamatan dan PostGIS sudah aman.

### 3. Ambil Credential Database Supabase

Untuk Railway, sebaiknya pakai **Transaction pooler**.

Di Supabase:

1. Masuk **Project Settings**.
2. Pilih **Database**.
3. Cari **Connection string**.
4. Pilih **Transaction pooler**.
5. Catat bagian ini:
   - host
   - port
   - database
   - user
   - password

Biasanya untuk Railway:

```env
DB_CONNECTION=pgsql
DB_HOST=host_pooler_supabase
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=user_supabase
DB_PASSWORD=password_supabase
DB_SSLMODE=require
```

### 4. Jalankan Migration ke Supabase

Isi `.env` lokal memakai credential Supabase terlebih dahulu.

Contoh:

```env
DB_CONNECTION=pgsql
DB_HOST=host_supabase
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=password_supabase
DB_SSLMODE=require
```

Setelah `.env` lokal mengarah ke Supabase, jalankan:

```bash
php artisan migrate
php artisan db:seed
php artisan db:seed --class=CarbonData2026Seeder
```

Jika memakai Railway pre-deploy, script opsional tersedia di:

```text
railway/init-app.sh
```

Jalankan script ini hanya setelah database Supabase dan tabel `kecamatan` sudah siap.

### 5. Deploy Laravel ke Railway

1. Push project ke GitHub.
2. Buat project baru di Railway.
3. Pilih Deploy from GitHub Repo.
4. Pilih repo `siger-zeronet`.
5. Tambahkan environment variables dari `.env.railway.example`.

Minimal variable penting:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:isi_app_key
APP_URL=https://domain-railway-kamu.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=host_supabase_pooler
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=user_supabase
DB_PASSWORD=password_supabase
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_CHANNEL=stderr
```

Generate `APP_KEY` lokal jika belum punya:

```bash
php artisan key:generate --show
```

Setelah Railway deploy berhasil, buka domain Railway dan cek halaman dashboard.

## Testing Cepat

Jalankan test:

```bash
php artisan test
```

Checklist manual:

1. Buka `/dashboard` tanpa login, harus bisa diakses.
2. Buka `/peta-emisi`, `/peta-absorpsi`, dan `/carbon-trade` tanpa login.
3. Buka `/data-carbon` tanpa login, harus diarahkan ke `/login`.
4. Login dengan akun admin default.
5. Buka `/data-carbon`, tambah data carbon.
6. Edit data carbon.
7. Hapus data carbon.
8. Logout, lalu pastikan menu admin tidak muncul.

## Troubleshooting

Jika muncul error `Vite manifest not found`, jalankan:

```bash
npm run dev
```

Atau build asset:

```bash
npm run build
```

Jika muncul error tabel `kecamatan` tidak ada, import dulu data/tabel `kecamatan` ke PostgreSQL.

Jika muncul error fungsi `ST_AsGeoJSON` tidak ada, aktifkan PostGIS:

```sql
CREATE EXTENSION IF NOT EXISTS postgis;
```

Jika login admin gagal, jalankan ulang:

```bash
php artisan db:seed
```
