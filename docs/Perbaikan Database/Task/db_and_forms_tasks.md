# Task List: Update Database & Forms — Integrasi Data dan Perbaikan

Lokasi: docs/Perbaikan Database/Task/db_and_forms_tasks.md
Dibuat: 29 Oktober 2025

Ringkasan
- Berdasarkan `report_comparison.md`, file ini merangkum tugas-tugas yang dapat dikerjakan untuk menyelaraskan implementasi kode (migrasi + model + UI) dengan rekomendasi desain dan kualitas data di dokumen Perbaikan Database.

Cara pakai
- Pilih item prioritas (HIGH dulu). Kerjakan setiap tugas sebagai branch fitur terpisah. Jalankan safety checks sebelum menerapkan migration di staging/prod.

Daftar tugas (actionable)

1) Add UNIQUE constraint to `assets.serial_number` (HIGH)
 - Tujuan: tegakkan keunikan serial_number untuk perangkat keras.
 - Langkah:
   1. Buat artisan command / SQL report untuk menemukan semua duplikat serial_number dan ekspor ke `storage/serial_duplicates.csv`.
   2. Komunikasikan hasil ke tim untuk pembersihan data (merge/hapus/mapping). Jika duplikat valid (mis. virtual assets), tentukan kriteria pengecualian.
   3. Buat migration yang menambahkan unique index `assets_serial_number_unique` (reversible drop index pada down()).
 - Files to create: `database/migrations/xxxx_xx_xx_add_unique_serial_to_assets.php`, optional artisan command `app/Console/Commands/DetectDuplicateSerials.php`.
 - Tests: PHPUnit test yang memastikan store/update asset menolak duplikat.

2) Add `purchase_orders` table + `assets.purchase_order_id` (MEDIUM)
 - Tujuan: enable TCO tracing dari asset ke PO.
 - Langkah:
   1. Create migration `purchase_orders` (id, po_number unique, supplier_id FK, order_date, total_cost decimal, timestamps).
   2. Add nullable `purchase_order_id` integer FK to `assets` and index it.
   3. Update Asset model relationship and import UI (asset create/edit add PO selector).
 - Files: migration for purchase_orders, migration to alter assets, model `PurchaseOrder.php`, update views.

3) Decide & implement `assets.location_id` policy (MEDIUM)
 - Options:
   A) Add `location_id` to `assets` (denormalize for convenience) and backfill from latest `movements`.
   B) Keep canonical `location` via `movements` and provide a helper view/accessor for common queries.
 - Action: decide with stakeholders. If A chosen, create migration to add `location_id`, backfill script and update `Asset` model and forms.

4) ticket_assets pivot + migration plan (HIGH if needed)
 - Tujuan: support many-to-many relation tickets <-> assets.
 - Langkah:
   1. Create migration for `ticket_assets` with composite PK or unique constraint and FKs.
   2. Create data migration script to insert existing `tickets.asset_id` rows to pivot.
   3. Update controllers and views to write/read pivot; keep `tickets.asset_id` for a deprecation window.
 - Files: `database/migrations/*create_ticket_assets_table.php`, artisan data migration `php artisan migrate:ticket_assets_migrate`.

5) ticket_history (immutable audit log) (HIGH)
 - Tujuan: record change events (status, assignment, priority) for SLA/compliance.
 - Langkah:
   1. Add migration for `ticket_history` (id, ticket_id, field_changed, old_value, new_value, changed_by, changed_at).
   2. Implement model events (or controller hooks) to write to `ticket_history` on changes.
 - Files: migration + update Ticket model or listeners (app/Listeners/TicketChangeLogger.php).

6) Indexes & performance tuning (MEDIUM)
 - Tujuan: add recommended indexes and composite indexes.
 - Steps: review `2025_10_15_112745_add_optimized_database_indexes.php`, add any missing composite indexes (e.g., tickets (assigned_to, ticket_status_id), tickets (location_id, ticket_status_id), daily_activities (user_id), assets (model_id, status_id)).

7) Consolidate `.php.skip` migrations (LOW-MEDIUM)
 - Reviews any `.php.skip` files, extract useful changes, create proper migration files (with timestamps), and archive the `.skip` ones.

8) Asset create view cleanup (UI bugfix) (HIGH)
 - Files: `resources/views/assets/create.blade.php` and `resources/views/assets/edit.blade.php`.
 - Fixes:
   - Remove duplicate `purchase_date` input and duplicate `warranty_type_id` block.
   - Standardize `asset_tag` maxlength to 50 (both input maxlength and hint text), or decide target length.
   - Ensure assigned_to optional/required rules consistent with business.

9) Server & client validation for `serial_number` (HIGH)
 - Implement server-side validation rules in AssetController (store/update) to enforce `unique:assets,serial_number` (conditional for non-empty). Add optional AJAX endpoint `GET /api/assets/check-serial?serial=...` returning JSON (exists:true/false) and client JS to call it on blur.
 - Acceptance: duplicate serials rejected by server; client-side AJAX informs user before submit.

10) Ticket UI: multi-asset support (OPTIONAL, HIGH if required)
 - If chosen: replace single `asset_id` select with multi-select UI (Select2 or dynamic rows). Update controllers to save to pivot and update views.

11) Asset Requests: add `request_number` (MEDIUM)
 - Migration: add `request_number` string unique to `asset_requests` and backfill for existing rows (e.g., AR-YYYY-xxxx).
 - Update create flow to generate request_number and show confirmation.

12) Tests and migration safety scripts (HIGH)
 - Create detection scripts and PHPUnit feature tests for critical flows (asset create with serial, ticket create with assets, asset_request numbering). Add migration dry-run checks.

13) Kamus data & documentation (LOW)
 - Generate `kamus_data.json` and `kamus_data.md` listing current tables, columns, types, FK and onDelete rules. Save under `docs/Perbaikan Database/Task`.

14) Deployment & rollback plan (HIGH)
 - Write `deployment_plan.md` describing staging steps, backups, migration order, and rollback steps.

Prioritas rekomendasi (singkat)
- HIGH: 1, 4 (if business requires), 5, 8, 9, 12, 14
- MEDIUM: 2, 3, 6, 11
- LOW: 7, 13

Jika ingin saya mulai mengerjakan salah satu item (contoh: A = UNIQUE serial migration; B = ticket_assets pivot; C = view cleanup), pilih huruf/nomor, saya akan buat branch, file migration, perubahan view, dan menjalankan verifikasi ringan.

---

Recent actions taken (update 2025-10-29):

- Added and ran the duplicate-detection command `app/Console/Commands/DetectDuplicateSerials.php` — it scanned the `assets` table and found no duplicate serials in the dev database.
- Created and applied migration `database/migrations/2025_10_29_160000_add_unique_serial_to_assets.php`. The migration includes an idempotent check for an existing index and will export duplicates to `storage/app/serial_duplicates_before_unique.csv` if any are found.
- Created and applied migrations for Purchase Orders and linking to assets:
  - `database/migrations/2025_10_29_150000_create_purchase_orders_table.php`
  - `database/migrations/2025_10_29_150500_add_purchase_order_id_to_assets.php`
  These add a `purchase_orders` table and a nullable `assets.purchase_order_id` FK (with onDelete set null). Views and controller were wired to show/select Purchase Orders in asset create/edit flows.
- Created and applied migration to add `request_number` to `asset_requests` and backfilled existing rows: `database/migrations/2025_10_29_151000_add_request_number_to_asset_requests.php`. Views were updated to display the generated `request_number` (format `AR-<YEAR>-<NNNN>`).

Next recommended steps:

- Add PHPUnit feature tests for:
  - Asset creation/update enforcing serial uniqueness.
  - AssetRequest creation and `request_number` generation/backfill.
- Create `deployment_plan.md` describing staging backup, migration order (apply PR-related migrations to staging first), and rollback steps.
- Optionally run a staging scan of `storage/app/serial_duplicates.csv` before applying the unique-index migration to production (already scanned in dev with no duplicates).

Saved-by: automated review on top of `report_comparison.md` (see that file for context)
