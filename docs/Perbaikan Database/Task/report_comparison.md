# Laporan Perbandingan: Dokumen "Perbaikan Database" vs Implementasi Kode

Tanggal: 29 Oktober 2025

Ringkasan singkat:
- Saya membaca keenam dokumen di `docs/Perbaikan Database` dan memeriksa model utama serta migrasi terkait di `app/` dan `database/migrations/`.
- Laporan ini merangkum kesesuaian antara desain yang direkomendasikan di dokumen dengan implementasi aktual, mencatat mismatch penting, dan merekomendasikan langkah perbaikan prioritas.

Catatan metode: Saya memeriksa secara langsung file markdown di folder dokumentasi dan file model/migrasi yang relevan (mis. `app/Asset.php`, `app/User.php`, `database/migrations/*create_assets*.php`, `create_tickets_table.php`, `create_asset_models_table.php`, `create_asset_types_table.php`, `create_locations_table.php`, `2025_10_02_000005_create_asset_requests_table.php`, `2025_10_02_000001_enhance_tickets_table.php`, dsb.).

---

## 1) Inti Fondasi - Manajemen Aset dan Pengguna

Dokumen merekomendasikan: tabel `assets` dengan kolom penting seperti `asset_tag` (UNIK), `serial_number` (UNIK), `model_id` (FK ke `asset_models`), `status_id`, `location_id`, `assigned_to_user_id` (nullable, FK ke `users`), `purchase_order_id`, `purchase_date`, `warranty_expiry_date`, `retirement_date`, dan tabel lookup pendukung (`asset_models`, `manufacturers`, `asset_types`, `statuses`, `suppliers`, `divisions`, dsb.).

Implementasi (kode & migrasi ditemukan):
- Ada migrasi awal `2016_03_14_123236_create_assets_table.php` yang membuat `assets` dengan: `id`, `asset_tag` (string(10) unique index), `serial_number` (nullable, NOT UNIQUE), `model_id`, `division_id`, `supplier_id`, `movement_id`, `purchase_date`, `warranty_months`, `warranty_type_id`, `invoice_id`, timestamps. Foreign keys untuk model/division/supplier ada.
- Nambah migrasi `2025_10_02_000004_enhance_assets_table.php` yang menambah: `qr_code` (unique), `status_id` (FK ke `statuses`), `assigned_to` (FK ke `users`, onDelete set null), `notes`, serta `ip_address`/`mac_address` columns. Jadi `status_id` dan `assigned_to` memang diimplementasikan lewat migrasi enhancement.
- Tabel lookup seperti `asset_models`, `asset_types`, `manufacturers`, `suppliers`, `locations`, `statuses`, `divisions` ada (migrasi & model ditemukan).

Mismatch / Catatan penting:
- `serial_number` tidak diberi constraint UNIQUE dalam migrasi awal. Dokumen merekomendasikan unikitas serial_number untuk perangkat keras. Saat ini serial_number nullable dan tidak unik.
- `asset_tag` diimplementasikan sebagai string(10) — dokumen tidak menetapkan panjang pasti tetapi sering memakai VARCHAR(50); pertimbangkan apakah 10 karakter cukup untuk semua skenario barcode/tag.
- `purchase_order_id` tidak ada di migrasi Assets; ada `invoice_id` tetapi dokumen merekomendasikan relasi ke `purchase_orders` untuk TCO. Jika ingin trace ke PO, butuh kolom/relasi tambahan.
- `location_id` tidak ada langsung di tabel `assets` (migrasi awal tidak membuatnya). Implementasi menggunakan `movement_id` (ke tabel `movements`) yang berisi `location_id`; kemungkinan intent aplikasi menyimpan lokasi via history/movements. Namun dokumen merekomendasikan kolom langsung `location_id` di `assets` untuk kemudahan query dan konsistensi—keduanya bisa koeksis, tapi perbedaan ini harus didokumentasikan.

Rekomendasi tindakan:
1. Tambah UNIQUE constraint pada `assets.serial_number` (jika kebijakan bisnis memperbolehkan) atau tentukan aturan lain (mis. serials non-unique for virtual assets). Buat migration yang menambahkan index unique dengan pengecekan data duplikat sebelumnya.
2. Pertimbangkan memperbesar `asset_tag` jika sistem saat ini menghasilkan tag lebih panjang (audit contoh nilai saat ini).
3. Tambah `purchase_order_id` ke `assets` (nullable) dan migrasi/relasi ke tabel `purchase_orders` bila PO tracking diperlukan.
4. Jika `location_id` sering dibutuhkan untuk reporting, pertimbangkan menambahkan `location_id` ke `assets` (dengan konsistensi terhadap `movement_id`), atau dokumentasikan pola akses via `movements`.

---

## 2) Kerangka Kerja Operasional - Service Desk dan Ticketing

Dokumen merekomendasikan: tabel `tickets` dengan `ticket_number` (unik, user-facing), `ticket_type_id`, `category_id`, `priority_id`, `status_id`, `impact`, `urgency`, `reported_by_user_id`, `assigned_to_user_id` (nullable), `title`, `description`, timestamps; tabel pivot `ticket_assets` (many-to-many) antara tickets dan assets; `ticket_comments`, `ticket_history` untuk audit.

Implementasi (kode & migrasi ditemukan):
- Migrasi awal `2016_04_13_124618_create_tickets_table.php` membuat `tickets` dengan `id`, `user_id`, `location_id`, `ticket_status_id`, `ticket_type_id`, `ticket_priority_id`, `subject`, `description`, `closed`, timestamps. Note: kolom bernama `user_id` (pencatat) — dokumen menyebut `reported_by_user_id` (naming saja, semantik sama).
- Migrasi enhancement `2025_10_02_000001_enhance_tickets_table.php` menambahkan `ticket_code` (unik), `assigned_to`, `assigned_at`, SLA fields (`sla_due`, `first_response_at`, `resolved_at`) dan `asset_id` (nullable). Jadi ada kode tiket unik (`ticket_code`) dan assignment fields.
- Tabel `tickets_types`, `tickets_priorities`, `tickets_statuses`, `tickets_entries` dsb. ada.
- Tidak ditemukan migrasi untuk pivot `ticket_assets` (many-to-many). Sebagai gantinya, sistem menambahkan `asset_id` di tabel `tickets` (one-to-many — satu tiket menunjuk satu asset).
- `ticket_comments` dan `ticket_history` tabel — saya tidak menemukan migrasi terpisah bernama `ticket_comments` atau `ticket_history` dengan pola yang sama persis seperti dokumen; ada `tickets_entries` dan `tickets_canned_fields` migrasi. Perlu pengecekan lebih lanjut: apakah `tickets_entries` diimplementasikan sebagai komentar/history gabungan.

Mismatch / Catatan penting:
- Dokumen merekomendasikan hubungan many-to-many (`ticket_assets`) untuk memodelkan kenyataan operasional; implementasi saat ini menggunakan `tickets.asset_id` (one-to-many). Ini menyederhanakan schema tetapi membatasi kasus di mana satu tiket terkait banyak aset.
- `ticket_number` nama di dokumen vs `ticket_code` di migrasi: fungsional sama (unik user-facing) — ini OK.
- Audit/history: migrasi yang jelas untuk `ticket_history` (immutable change log) tidak ditemukan; periksa implementasi `tickets_entries` apakah memenuhi kebutuhan audit (kemungkinan sebagian ter-cover).

Rekomendasi tindakan:
1. Jika kasus bisnis sering membutuhkan banyak asset per tiket, buat migrasi pivot `ticket_assets` (ticket_id, asset_id) dan migrasi yang memigrasikan data dari `tickets.asset_id` ke pivot (preserve non-null asset_id entries).
2. Pastikan `ticket_history` (change log) tersedia atau buat tabel terpisah untuk menjamin audit trail immutable.
3. Verifikasi bahwa `tickets_entries` memenuhi peran `ticket_comments`/`ticket_history`; jika tidak, tambahkan tabel yang diperlukan.

---

## 3) Manajemen Siklus Hidup - Permintaan dan Penyediaan Aset

Dokumen merekomendasikan: tabel `asset_requests` (request_number unik, requested_by_user_id, request_type, asset_category, justification, status_id, approved_by, approval_date, fulfilled_asset_id) dan integrasi ke `purchase_orders` serta closed-loop traceability.

Implementasi (kode & migrasi ditemukan):
- Ada migrasi `2025_10_02_000005_create_asset_requests_table.php` yang membuat `asset_requests` dengan: `id`, `requested_by` (FK users), `asset_type_id`, `justification`, `status` enum, `approved_by` (nullable), `approved_at`, `approval_notes`, `fulfilled_asset_id` (nullable), `fulfilled_at` dan foreign keys. Ini konsisten dengan dokumen pada tingkat fungsional.

Mismatch / Catatan penting:
- Dokumen menyebut `request_number (UNIQUE)` sementara migrasi saat ini menggunakan `id` dan tidak menambahkan explicit `request_number`/`request_code`. Ini bukanlah isu fungsional besar tetapi berpengaruh pada UX/nomor referensi audit.
- Integrasi ke `purchase_orders` belum terlihat (tidak ada migrasi `purchase_orders` diobservasi dalam pemeriksaan singkat). Jika organisasi butuh TCO trace, perlu menambahkan PO entity dan relasi.

Rekomendasi tindakan:
1. Tambah kolom `request_number`/`request_code` (unik, user-facing) bila diperlukan untuk pelacakan bisnis.
2. Tambah tabel `purchase_orders` dan relasi ke `asset_requests` dan/atau `assets` bila belum ada dan diperlukan oleh proses pengadaan.

---

## 4) Kinerja dan Akuntabilitas - Aktivitas Harian dan Pelacakan KPI

Dokumen merekomendasikan: tabel `daily_activities` yang mendetilkan aktivitas (activity_type_id, user_id, ticket_id nullable, asset_id nullable, start_time, end_time, duration_minutes, description) serta petunjuk bagaimana menurunkan KPI.

Implementasi (kode & migrasi ditemukan):
- Ada migrasi `2016_..._create_daily_activities_table.php` (dan/atau update terbaru) yang membuat `daily_activities`. Struktur migrasi berisi `user_id`, `ticket_id` (nullable), `asset_id` (nullable), `duration_minutes`, `description` dan timestamp fields sesuai rekomendasi.
- Tabel `activity_types` tidak saya temukan eksplisit di pencarian singkat, namun konsep `type`/`activity_type_id` perlu dikonfirmasi.

Mismatch / Catatan penting:
- Secara umum `daily_activities` sudah ada; perlu verifikasi kolom `activity_type_id`/lookup `activity_types` untuk konsistensi.

Rekomendasi tindakan:
1. Pastikan indeks pada `daily_activities.user_id`, `daily_activities.ticket_id` dan `daily_activities.asset_id` untuk kueri pelaporan.
2. Verifikasi/dokumentasikan `activity_types` (lookup) ada dan diisi.

---

## 5) Skema Terpadu - Diagram ERD & Kamus Data

Dokumen menyajikan ERD konseptual dan kamus data (kolom, tipe, constraints, contoh). Ia menekankan penggunaan FK, UNIQUE dan strategi ON DELETE yang hati-hati (RESTRICT, SET NULL, dsb.).

Temuan implementasi:
- Banyak FK dan lookup table yang dideskripsikan memang diimplementasikan (manufacturers, asset_models, asset_types, statuses, divisions, suppliers dan lain-lain).
- Banyak foreign key di-migrate dengan `->foreign(...)->references(...)->on(...)` dan beberapa `onDelete('set null')` (mis. `assigned_to` pada assets dan tickets), konsisten dengan rekomendasi dokumen.

Mismatch / Catatan penting:
- Beberapa naming berbeda (dok: `assigned_to_user_id`, kode: `assigned_to`). Ini kecil tetapi harus distandarkan di kamus data untuk menghindari kebingungan.
- Perlu review kebijakan onDelete untuk beberapa FK — beberapa migrasi sudah memakai `onDelete('set null')` di tempat yang aman.

Rekomendasi tindakan:
1. Buat/upgrade kamus data (file JSON/MD) yang merekam nama kolom aktual, tipe, FK dan aturan onDelete yang saat ini ada — ini akan mempermudah sinkronisasi dengan dokumen target.
2. Standarkan penamaan FK (mis. gunakan `_id` suffix konsisten atau document alasan mengapa beberapa kolom tidak memakai suffix).

---

## 6) Pertimbangan Implementasi dan Rekomendasi Strategis

Dokumen menekankan indeks pada FK, indeks komposit untuk pola kueri umum, strategi arsip/retensi, prinsip least privilege, enkripsi dan audit.

Temuan implementasi & rekomendasi:
- Banyak kolom FK diindeks pada saat migrasi awal (`->index()` terlihat pada beberapa FK). Ada juga migrasi yang menambahkan atau mengoptimalkan indeks (`2025_10_15_112745_add_optimized_database_indexes.php`).
- Beberapa file migrasi berlabel `.php.skip` yang menunjukkan ada eksperimen/patch yang mungkin belum diaktifkan. Perlu konfirmasi apakah mereka sengaja di-skip.

Rekomendasi tindakan strategis (prioritas):
1. Pastikan semua FK yang sering di-join memiliki index (audit `database/migrations/*` dan `SHOW INDEX` pada DB jika tersedia).
2. Tambahkan indeks komposit yang disarankan (mis. tickets (assigned_to, ticket_status_id)) jika query sering memerlukannya.
3. Rencanakan strategi arsip untuk `tickets` dan `ticket_history` (mis. migrasi/command yang memindahkan baris > N tahun ke tabel _archive).
4. Verifikasi migrasi `.skip` — konsolidasikan perubahan yang diperlukan ke migrasi resmi dan jalankan review migrasi.

---

## Ringkasan Gap Prioritas (Top 6)
1. serial_number pada `assets` tidak UNIQUE — dokumen menekankan keunikan. (HIGH)
2. Tidak ada pivot `ticket_assets` untuk many-to-many; saat ini `tickets.asset_id` hanya satu asset per ticket. (HIGH, jika use-case memerlukan banyak asset)
3. `purchase_order_id` tidak ada pada `assets` — dibutuhkan untuk TCO / audit pengadaan. (MEDIUM)
4. Review dan standarisasi penamaan FK (`assigned_to` vs `assigned_to_user_id`) dan dokumentasikan di kamus data. (LOW)
5. Pastikan `ticket_history`/immutable audit tersedia atau buat tabel khusus. (HIGH for compliance)
6. Periksa migrasi `.skip` dan konsolidasikan perubahan indeks/constraint. (MEDIUM)

---

## Rekomendasi langkah selanjutnya (implementable)
1. Buat migration kecil untuk menambahkan UNIQUE index ke `assets.serial_number` setelah membersihkan/meresolve duplikat (script deteksi duplikat terlebih dahulu). Tambahkan test untuk memastikan constraint.
2. Jika business requires many-to-many tickets<->assets: buat migration pivot `ticket_assets` & data migration dari `tickets.asset_id` ke pivot; kemudian drop `tickets.asset_id` (setelah memvalidasi semua dependensi). Alternatif: biarkan `tickets.asset_id` untuk simple-case dan tambahkan pivot untuk advanced-case — jelas kan pilihan ke tim.
3. Tambah `purchase_orders` table dan `purchase_order_id` di `assets` (nullable) jika perlu TCO tracing.
4. Tambah tabel `ticket_history` jika `tickets_entries` tidak memadai; buat mekanisme application-level untuk menulis immutable change log.
5. Audit semua FK untuk index presence; tambahkan indeks komposit yang sering dipakai.
6. Konsolidasi migrasi `.php.skip` ke proses resmi atau hapus/arsipkan jika usang.

---

## Lampiran: File yang saya baca (pilihan)
- docs/Perbaikan Database/*.md (semua 6 file)
- app/Asset.php, app/User.php, app/AssetType.php, app/AssetModel.php, app/Location.php
- database/migrations/2016_03_14_123236_create_assets_table.php
- database/migrations/2016_03_10_083700_create_asset_models_table.php
- database/migrations/2016_03_09_081346_create_asset_types_table.php
- database/migrations/2016_03_08_125036_create_locations_table.php
- database/migrations/2016_04_13_124618_create_tickets_table.php
- database/migrations/2025_10_02_000001_enhance_tickets_table.php
- database/migrations/2025_10_02_000005_create_asset_requests_table.php
- database/migrations/2025_10_02_000004_enhance_assets_table.php

---

Jika Anda mau, saya bisa:
- (A) Membuat migration draft untuk memperbaiki `serial_number` (cek duplikat & tambah UNIQUE) dan/atau
- (B) Membuat migration pivot `ticket_assets` + skrip migrasi data dari `tickets.asset_id` ke pivot, atau
- (C) Menyusun kamus data (JSON/MD) otomatis dari migrasi yang ada.

Catatan: Saya menandai prioritas untuk tiap rekomendasi. Jika Anda pilih salah satu tindakan (A/B/C atau kombinasi), saya akan buat patch/migration dan menjalankan pengecekan cepat (lint/tests) sebelum menyelesaikan.

---

Penutup: laporan ini dibuat berdasarkan pembacaan file dokumentasi dan pemeriksaan model/migrasi di workspace saat ini. Jika ada bagian skema yang saya belum periksa (mis. migrasi lama atau modul eksternal), beri tahu saya dan saya akan melengkapinya.

