# Operations Guide — BIMA Screening

Panduan ini ditulis untuk deployment ke shared hosting dengan ~12.000 pengguna dalam satu minggu.

---

## Pre-Launch Checklist

Lakukan ini **sebelum** event dimulai.

### 1. Perbaiki `.env` di server

Buka file `.env` di server (bukan di lokal), ubah nilai-nilai berikut:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-kamu.com

SESSION_DRIVER=file
```

> **Kenapa `SESSION_DRIVER=file`?**
> Default-nya `database` artinya setiap request pengguna membuka koneksi ke MySQL hanya untuk menyimpan sesi. Shared hosting biasanya hanya punya 20–50 koneksi MySQL serentak. Dengan `file`, sesi disimpan di disk — tidak pakai koneksi DB sama sekali.

> **Kenapa `APP_DEBUG=false`?**
> Kalau `true`, error PHP ditampilkan langsung ke pengguna (termasuk isi `.env`, nama tabel, dll). Di production ini harus `false`.

---

### 2. Clear & rebuild cache di server

Jalankan via SSH atau terminal di hosting:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> **Penting:** Setiap kali kamu ubah `.env`, wajib jalankan `php artisan config:clear` lagi. Kalau tidak, Laravel akan tetap pakai nilai lama dari cache.

---

### 3. Pastikan folder storage writable

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

Kalau tidak bisa via SSH, atur permission dari File Manager di cPanel.

---

### 4. Test end-to-end sebelum dibuka

Coba sendiri alur lengkap:
- [ ] Isi form identitas
- [ ] Jawab PHQ-9 sampai selesai
- [ ] Cek apakah hasil muncul dengan benar
- [ ] Login admin, cek dashboard tampil normal
- [ ] Cek `storage/logs/laravel.log` — tidak boleh ada error baru

---

## Estimasi Beban

| Skenario | Req/menit |
|----------|-----------|
| Normal (tersebar merata) | ~15–30 |
| Peak harian | ~50–80 |
| Semua orang serentak (worst case) | ~200–500 |

Shared hosting umumnya sanggup sampai ~100 req/menit untuk app Laravel sederhana. Selama pengguna tersebar sepanjang minggu, aman. Risiko ada jika ada pengumuman massal yang membuat ribuan orang buka sekaligus.

---

## Monitoring Selama Event

### Yang perlu dipantau

**1. Laravel log (paling penting)**
```
storage/logs/laravel.log
```
Buka file ini dan perhatikan baris terakhir. Error akan muncul di sini sebelum pengguna lapor.

**2. Control panel hosting (cPanel/Plesk)**
- CPU usage — kalau konsisten di atas 80%, server kewalahan
- MySQL processes — kalau ada puluhan query menumpuk, ada bottleneck

**3. Coba akses manual**
Sesekali buka situsnya sendiri dan rasakan kecepatannya. Kalau sudah terasa lambat (>4 detik load), itu tanda awal masalah.

---

## Troubleshooting — Ketika Sesuatu Tidak Beres

### HTTP 500 — Internal Server Error

Pengguna melihat halaman error atau blank.

**Langkah:**
1. Buka `storage/logs/laravel.log`, cari baris `[ERROR]` atau `[CRITICAL]` paling bawah
2. Lihat pesan errornya:

| Pesan Error | Artinya | Solusi |
|-------------|---------|--------|
| `SQLSTATE[HY000] [1040] Too many connections` | MySQL kehabisan koneksi | Hubungi hosting, minta naikkan `max_connections`. Sementara: kurangi traffic atau aktifkan maintenance mode |
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL mati | Hubungi hosting support segera |
| `Allowed memory size exhausted` | PHP kehabisan memori | Hosting terlalu kecil, perlu upgrade atau minta naikkan `memory_limit` |
| `No such file or directory (storage/...)` | Folder storage tidak ada / tidak writable | Jalankan `chmod -R 775 storage` |
| `View not found` | Cache view rusak | Jalankan `php artisan view:clear` |

---

### Halaman Lambat (>5 detik)

**Langkah:**
1. Cek CPU usage di cPanel — kalau tinggi, server memang sedang overloaded
2. Cek apakah hanya halaman tertentu yang lambat:
   - Lambat di semua halaman = masalah server
   - Lambat hanya di dashboard admin = query berat, bukan masalah pengguna umum
3. Kalau parah, aktifkan maintenance mode sementara:
   ```bash
   php artisan down --message="Sedang dalam perbaikan, akan kembali dalam 30 menit"
   ```
   Matikan maintenance:
   ```bash
   php artisan up
   ```

---

### Pengguna Tidak Bisa Submit / Data Tidak Tersimpan

**Langkah:**
1. Cek `storage/logs/laravel.log` untuk error database
2. Cek apakah disk hosting penuh (cPanel → Disk Usage)
3. Coba submit sendiri, lihat apakah ada error
4. Kalau ada pesan `CSRF token mismatch`:
   - Ini biasanya karena sesi kadaluarsa (pengguna terlalu lama diam di halaman form)
   - Normal — pengguna cukup refresh dan mulai ulang

---

### Setelah Perbaikan

Setelah fix apapun, selalu jalankan:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Dan pastikan `APP_DEBUG=false` tetap aktif sebelum dibuka kembali ke publik.

---

## Cara Ambil File `laravel.log`

Kalau ada error dan perlu dikirim ke developer atau diperiksa di laptop.

### 1. File Manager cPanel (paling mudah)
1. Buka cPanel → **File Manager**
2. Navigasi ke folder project → `storage/logs/`
3. Klik kanan `laravel.log` → **Download**

### 2. SFTP via FileZilla (untuk file besar)
1. Download [FileZilla](https://filezilla-project.org/) (gratis)
2. Isi koneksi:
   - **Host**: `skriningkesehatanmentalum.com`
   - **Username**: username cPanel
   - **Password**: password cPanel
   - **Port**: `22`
3. Klik **Quickconnect**
4. Navigasi ke `storage/logs/` di panel kanan
5. Drag `laravel.log` ke panel kiri (laptop)

### 3. Live monitor via SSH
Kalau punya akses SSH, jalankan ini untuk melihat log secara real-time:
```bash
tail -f storage/logs/laravel.log
```
Log baru akan langsung muncul saat terjadi error.

---

## Monitoring di cPanel

### Resource Usage
Buka **cPanel → Metrics → Resource Usage**. Yang perlu diperhatikan:
- **CPU %** — kalau terus di atas 80%, server kewalahan
- **Physical Memory** — kalau mendekati limit, PHP bisa crash
- **Entry Processes** — kalau sering mentok di angka maksimum (biasanya 20–25), antrian request penuh

### MySQL
Buka **phpMyAdmin**, jalankan query ini untuk cek ada query yang macet:
```sql
SHOW PROCESSLIST;
```
Kalau banyak baris dengan status `Waiting` atau `Locked`, ada query yang bermasalah.

### Disk Usage
Buka **cPanel → Files → Disk Usage**. Kalau disk penuh, app langsung crash — tidak bisa tulis session, log, atau upload.

### Error Log Server
Buka **cPanel → Metrics → Errors** untuk melihat PHP fatal error di level server. Berbeda dari `laravel.log` — ini error yang bahkan tidak sempat ditangani Laravel.

---

## Kontak & Eskalasi

Kalau masalah tidak bisa diselesaikan sendiri:
1. **Shared hosting support** — untuk masalah MySQL down, disk penuh, PHP crash
2. Sertakan: nama domain, waktu kejadian, dan isi error dari `laravel.log`

php artisan config:clear && php artisan config:cache
php artisan route:cache
php artisan view:cache

