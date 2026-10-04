# TODO - Perbaikan Halaman Jadwal Angsuran Pembiayaan

Target file: `resources/views/dashboard/anggota/partials/angsuran.blade.php`

## Steps

- [x] 1. Periksa struktur HTML (pasangan div/section/table/template/form) & identifikasi tag yang belum ditutup
- [x] 2. Tutup grid statistik setelah card ke-4 (loading & tabel keluar dari grid)
- [x] 3. Pindahkan Loading Indicator ke luar grid statistik
- [x] 4. Pindahkan Tabel ke luar grid statistik agar full-width
- [x] 5. Perbaiki penutup card utama (`bg-white rounded-2xl ... p-6`)
- [x] 6. Lebarkan tabel: `overflow-x-auto` + `w-full` + `min-w-max` + `whitespace-nowrap`
- [x] 7. Tambah sticky header tabel (opsional) & pastikan kolom tidak terpotong
- [x] 8. Pindahkan Pagination ke luar tabel & beri penutup div yang benar
- [x] 9. Responsive stats grid: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`
- [x] 10. Perbaiki Alpine.js: hitung flag `terlambat` client-side (controller tidak mengirimnya), tambah helper `isLunas`, pastikan loadAngsuran/pagination/refresh/Bayar tetap jalan
- [x] 11. Optimasi UI: hover baris, rounded konsisten, shadow halus, spacing konsisten
- [x] 12. Validasi akhir: tidak ada tag belum ditutup, tidak ada error Blade/Alpine, tabel memenuhi lebar card

## Catatan

- Struktur card utama: `Header → Stats Grid → Loading → Tabel → Pagination`
- Grid statistik responsif: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`
- Tabel full-width dengan `overflow-x-auto`, `w-full`, `min-w-max`, `whitespace-nowrap`, sticky header
- Flag `terlambat` dihitung client-side karena controller tidak mengirimkannya
- Controller `AngsuranController.php` tidak diubah (hanya Blade)


