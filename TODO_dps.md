# TODO - Implementasi Role Dewan Pengawas Syariah (DPS)

## Fase 1 — Database, Seeder, Factory
- [x] 1.1 Buat migration `create_audit_temuan_table`
- [x] 1.2 Buat migration `create_opini_syariah_table`
- [x] 1.3 Buat migration `create_laporan_pengawasan_table`
- [x] 1.4 Tambah akun DPS di `UserSeeder` (dps@koperasi.test)
- [x] 1.5 Tambah state `dps()` di `UserFactory`
- [x] 1.6 Buat migration `2026_08_01_000004_create_notifications_table`

## Fase 2 — Model
- [x] 2.1 Buat model `AuditTemuan` + relasi
- [x] 2.2 Buat model `OpiniSyariah` + relasi
- [x] 2.3 Buat model `LaporanPengawasan` + relasi

## Fase 3 — Authorization & Routes
- [x] 3.1 Daftarkan Gates DPS di `AppServiceProvider`
- [x] 3.2 Perluas routes `/dps` (dashboard, audit, produk, temuan, opini, laporan)
- [x] 3.3 Tambah routes notifikasi `/dps/notifikasi` (index, read, read-all)

## Fase 4 — Controller
- [x] 4.1 Upgrade `DPSDashboardController`
- [x] 4.2 Buat `AuditController` (read-only akad)
- [x] 4.3 Buat `ProdukModerasiController`
- [x] 4.4 Buat `TemuanController` (tanpa delete)
- [x] 4.5 Buat `OpiniSyariahController`
- [x] 4.6 Buat `LaporanPengawasanController`
- [x] 4.7 Buat `NotifikasiController` (index, markRead, markAllRead)

## Fase 5 — Frontend (tab-based ala Pengurus)
- [x] 5.1 Rebuild `dashboard/dps/index.blade.php`
- [x] 5.2 Buat partial `sidebar.blade.php`
- [x] 5.3 Buat partial `dashboard.blade.php` (statistik + aktivitas + grafik)
- [x] 5.4 Buat partial `audit.blade.php`
- [x] 5.5 Buat partial `produk.blade.php`
- [x] 5.6 Buat partial `temuan.blade.php`
- [x] 5.7 Buat partial `opini.blade.php`
- [x] 5.8 Buat partial `laporan.blade.php`
- [x] 5.9 Tambah notification bell (mobile + desktop) + dropdown notifikasi Alpine

## Fase 6 — Notifikasi & Validasi
- [x] 6.1 Tambah notifikasi DPS/Admin (database notifications)
- [x] 6.2 Jalankan migrate + seed
- [x] 6.3 Validasi view (view:clear + view:cache)
- [x] 6.4 Verifikasi hak akses (read-only transaksi, write hanya audit/opini/laporan)
- [x] 6.5 Verifikasi routes via `route:list --path=dps` (17 routes OK)

