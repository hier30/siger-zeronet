# Deploy Siger ZeroNet ke Railway + Supabase

Panduan ini khusus untuk project Laravel ini. Railway dipakai untuk hosting aplikasi, Supabase dipakai sebagai database PostgreSQL/PostGIS.

Catatan penting: pgAdmin4 tidak dipindahkan ke Supabase. pgAdmin4 hanya aplikasi GUI untuk melihat PostgreSQL lokal. Yang dipindahkan adalah isi database/tabel PostgreSQL lokal, terutama tabel `kecamatan` yang punya kolom geometry `geom`.

## Ringkasan Urutan Aman

1. Buat project Supabase.
2. Aktifkan PostGIS di Supabase.
3. Export tabel `kecamatan` dari PostgreSQL lokal/pgAdmin.
4. Import tabel `kecamatan` ke Supabase.
5. Jalankan migration dan seeder Laravel ke Supabase.
6. Deploy aplikasi Laravel ke Railway.
7. Isi environment variables Railway.
8. Generate domain Railway dan test halaman.

Urutan ini penting karena migration `carbon_data` punya foreign key ke tabel `kecamatan`. Kalau tabel `kecamatan` belum ada, `php artisan migrate` akan gagal.

## 1. Buat Project Supabase

1. Buka https://supabase.com.
2. Login.
3. Klik **New project**.
4. Isi:
   - **Name**: `siger-zeronet`
   - **Database Password**: buat password kuat dan simpan.
   - **Region**: pilih yang dekat, misalnya Singapore jika tersedia.
5. Tunggu project selesai dibuat.

## 2. Aktifkan PostGIS

Opsi paling mudah untuk project ini:

1. Buka Supabase project.
2. Masuk **SQL Editor**.
3. Jalankan:

```sql
CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA extensions;
```

4. Cek:

```sql
SELECT extensions.PostGIS_Version();
```

Kalau query menampilkan versi PostGIS, extension sudah aktif.

Karena PostGIS dibuat di schema `extensions`, set env Laravel:

```env
DB_SEARCH_PATH=public,extensions
```

## 3. Ambil Connection String Supabase

Di Supabase:

1. Klik tombol **Connect** atau buka **Project Settings > Database**.
2. Catat connection string.

Untuk import/migration dari komputer lokal, pakai salah satu:

- **Direct connection**: bagus untuk migration, `pg_dump`, restore, dan database tools. Pakai ini kalau koneksi kamu mendukung IPv6 atau project Supabase punya IPv4 add-on.
- **Session pooler**: pilihan aman kalau internet/ISP kamu IPv4-only.

Untuk runtime aplikasi di Railway, gunakan:

- **Session pooler** untuk Laravel biasa/persistent app.
- **Transaction pooler** juga bisa untuk traffic pendek, tapi untuk Laravel tradisional session pooler biasanya lebih nyaman.

Port yang umum:

- Direct connection: `5432`
- Session pooler: `5432`
- Transaction pooler: `6543`

## 4. Export Tabel `kecamatan` dari pgAdmin/PostgreSQL Lokal

Project ini minimal butuh tabel:

- `kecamatan.id`
- `kecamatan.kecamatan`
- `kecamatan.geom`

### Opsi A: Export dari pgAdmin4

1. Buka pgAdmin4.
2. Connect ke server PostgreSQL lokal.
3. Klik kanan database lokal project ini, misalnya `siger_zeronet`.
4. Pilih **Backup...**.
5. Untuk hanya tabel kecamatan:
   - Format: **Plain**
   - Filename: misalnya `kecamatan.sql`
   - Di tab object/options, pilih table `public.kecamatan` saja jika pgAdmin menyediakan pilihan object.
6. Pastikan dump tidak membawa owner/privileges kalau ada opsi:
   - **No owner**: aktif
   - **No privileges**: aktif
7. Klik **Backup**.

Kalau pgAdmin kamu membuat dump custom/binary, ubah ke **Plain** agar mudah di-import lewat `psql` atau SQL Editor.

### Opsi B: Export lewat terminal

Jalankan dari komputer yang punya akses ke PostgreSQL lokal:

```bash
pg_dump -h 127.0.0.1 -p 5432 -U postgres -d siger_zeronet --table=public.kecamatan --format=plain --no-owner --no-privileges --file=kecamatan.sql
```

Ganti:

- `postgres` dengan user lokal kamu.
- `siger_zeronet` dengan nama database lokal kamu.
- `kecamatan.sql` dengan lokasi file yang kamu mau.

## 5. Import `kecamatan.sql` ke Supabase

### Opsi A: Import lewat psql

Gunakan connection string Supabase direct/session. Contoh:

```bash
psql "postgresql://USER:PASSWORD@HOST:PORT/postgres?sslmode=require" -v ON_ERROR_STOP=1 -f kecamatan.sql
```

Ganti `USER`, `PASSWORD`, `HOST`, dan `PORT` dari Supabase.

### Opsi B: Import lewat pgAdmin4

1. Di pgAdmin4, klik kanan **Servers > Register > Server...**.
2. Tab **General**:
   - Name: `Supabase Siger ZeroNet`
3. Tab **Connection**:
   - Host: host dari Supabase direct/session pooler.
   - Port: port dari Supabase.
   - Maintenance database: `postgres`
   - Username: user dari Supabase.
   - Password: password database Supabase.
4. Tab **SSL**:
   - SSL mode: `require`
5. Save.
6. Setelah connect, buka database `postgres`.
7. Jalankan file `kecamatan.sql` lewat Query Tool, atau pakai menu Restore kalau file dibuat dalam format custom.

### Cek Hasil Import

Di Supabase SQL Editor, jalankan:

```sql
SELECT id, kecamatan, extensions.ST_AsGeoJSON(geom) AS geojson
FROM public.kecamatan
LIMIT 1;
```

Kalau keluar satu baris dan geojson tidak error, data GIS sudah aman.

## 6. Jalankan Migration dan Seeder ke Supabase

Di lokal, sementara arahkan `.env` ke database Supabase:

```env
DB_CONNECTION=pgsql
DB_HOST=HOST_SUPABASE
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=USER_SUPABASE
DB_PASSWORD=PASSWORD_SUPABASE
DB_SSLMODE=require
DB_SEARCH_PATH=public,extensions
```

Lalu jalankan:

```bash
php artisan config:clear
php artisan migrate
php artisan db:seed
php artisan db:seed --class=CarbonData2026Seeder
```

Seeder akan membuat:

- akun admin default
- data `carbon_data` jika masih kosong dan tabel `kecamatan` sudah terisi
- data sertifikat karbon
- data tahun 2026 jika ada data dasar

Admin default:

```text
Email: admin@sigerzeronet.local
Password: admin12345
```

## 7. Push Project ke GitHub

Pastikan file rahasia tidak ikut:

- Jangan commit `.env`.
- Commit `.env.railway.example`, `railway.json`, dan `railway/init-app.sh`.

Push ke GitHub:

```bash
git add .
git commit -m "Prepare Railway and Supabase deployment"
git push
```

## 8. Deploy ke Railway

1. Buka https://railway.com.
2. Login.
3. Klik **New Project**.
4. Pilih **Deploy from GitHub repo**.
5. Pilih repo `siger-zeronet`.
6. Railway akan mendeteksi Laravel lewat Railpack.
7. Buka service aplikasi.
8. Masuk **Variables**.
9. Isi environment variables.

Minimal variables:

```env
APP_NAME="SigerZeroNet"
APP_ENV=production
APP_KEY=base64:ISI_DARI_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://DOMAIN-RAILWAY-KAMU.up.railway.app

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=HOST_SUPABASE
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=USER_SUPABASE
DB_PASSWORD=PASSWORD_SUPABASE
DB_SSLMODE=require
DB_SEARCH_PATH=public,extensions

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

MAIL_MAILER=log
MAIL_FROM_ADDRESS=admin@sigerzeronet.local
MAIL_FROM_NAME="${APP_NAME}"
VITE_APP_NAME="${APP_NAME}"
```

Generate `APP_KEY`:

```bash
php artisan key:generate --show
```

## 9. Railway Build dan Pre-deploy

Project ini sudah punya:

- `railway.json`
- `railway/init-app.sh`
- `.env.railway.example`

Di Railway service settings:

1. Build command: `npm run build`
2. Pre-deploy command:

```bash
chmod +x ./railway/init-app.sh && sh ./railway/init-app.sh
```

Pre-deploy command ini menjalankan:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan db:seed --class=CarbonData2026Seeder --force
```

Aktifkan pre-deploy setelah tabel `kecamatan` sudah masuk ke Supabase. Kalau belum, deploy akan gagal karena migration `carbon_data` butuh tabel `kecamatan`.

## 10. Generate Domain Railway

1. Buka Railway service.
2. Masuk **Settings > Networking**.
3. Klik **Generate Domain**.
4. Copy domain yang muncul.
5. Update variable Railway:

```env
APP_URL=https://domain-kamu.up.railway.app
```

Redeploy setelah `APP_URL` diubah.

## 11. Checklist Testing

Buka domain Railway:

1. `/dashboard` bisa dibuka.
2. `/peta-emisi` menampilkan peta.
3. `/peta-absorpsi` menampilkan peta.
4. `/carbon-trade` menampilkan tabel.
5. `/login` bisa login dengan admin default.
6. `/data-carbon` bisa create/edit/delete data setelah login.

Cek API:

```text
/api/peta-emisi?tahun=2025
/api/peta-absorpsi?tahun=2025
/api/dashboard?tahun=2025
```

Kalau API peta mengembalikan `features: []`, biasanya data `kecamatan.geom` kosong/tidak berhasil di-import.

## Troubleshooting Cepat

### `relation "kecamatan" does not exist`

Tabel `kecamatan` belum di-import ke Supabase. Import dulu `kecamatan.sql`, lalu ulangi:

```bash
php artisan migrate
```

### `function st_asgeojson(...) does not exist`

PostGIS belum aktif atau schema PostGIS belum masuk search path.

Cek:

```sql
SELECT extensions.PostGIS_Version();
```

Pastikan env:

```env
DB_SEARCH_PATH=public,extensions
```

Lalu clear config:

```bash
php artisan config:clear
```

### `permission denied` atau error owner/role saat import

Dump dari lokal membawa owner/privileges yang tidak ada di Supabase. Export ulang dengan:

```bash
pg_dump -h 127.0.0.1 -p 5432 -U postgres -d siger_zeronet --table=public.kecamatan --format=plain --no-owner --no-privileges --file=kecamatan.sql
```

### Railway deploy berhasil tapi halaman error 500

Cek Railway logs. Hal yang paling sering:

- `APP_KEY` kosong.
- Database credentials salah.
- `DB_SSLMODE=require` belum diset.
- `DB_SEARCH_PATH` belum memuat schema PostGIS.
- Tabel `kecamatan` belum ada.

### `Vite manifest not found`

Pastikan Railway build command:

```bash
npm run build
```

Lalu redeploy.
