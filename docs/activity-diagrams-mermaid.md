# Activity Diagram - Sistem Koperasi Syariah

Dokumen ini memakai Mermaid. Buka file ini di Markdown preview yang mendukung Mermaid, atau salin blok diagram ke https://mermaid.live.

---

## 1. Lifecycle Pembiayaan (End-to-End)

```mermaid
flowchart TD
    A([Mulai]) --> B[Anggota: Pilih Jenis Akad]
    B --> B1{Jenis Akad}
    B1 -->|Murabahah| B2[Harga beli, margin%, DP, objek]
    B1 -->|Mudharabah| B3[Modal, nisbah%, estimasi omzet]
    B1 -->|Musyarakah| B4[Modal koperasi+anggota, nisbah%]
    B1 -->|Ijarah| B5[Nilai aset, ujrah/bulan, rawat]
    B1 -->|Qardh| B6[Pokok pembiayaan, tenor]

    B2 --> C[Upload Formulir Pengajuan]
    B3 --> C
    B4 --> C
    B5 --> C
    B6 --> C

    C --> D{Validasi Form?}
    D -->|Gagal| E[Tampilkan Error]
    E --> B
    D -->|Berhasil| F[Simpan Pembiayaan\nstatus: diajukan]

    F --> G[Buat Detail Akad]
    G --> H[Buat Dokumen Pengajuan]
    H --> I{Murabahah/Qardh?}
    I -->|Ya| J[Buat Jadwal Angsuran]
    I -->|Tidak| K[Kirim Notifikasi]
    J --> K

    K --> L[DPS: Audit Akad]
    L --> M[Isi Checklist Validasi]
    M --> N{Hasil Validasi}
    N -->|Sesuai Syariah| O[Pengurus: Review Pembiayaan]
    N -->|Perlu Perbaikan| P[Anggota: Perbaiki Data]
    P --> B
    N -->|Tidak Sesuai| Q[Status: Ditolak]
    Q --> Z1([Selesai - Ditolak])

    O --> R{Hash Masih Cocok?}
    R -->|Ya + Setuju| S[Status: Disetujui]
    R -->|Tidak / Tolak| T[Ditolak atau Validasi Ulang]
    T --> Z1

    S --> U[Anggota: Ajukan Pencairan]
    U --> V[Pengurus: Proses Pencairan]
    V --> V1{Metode Cair}
    V1 -->|Manual| W[Transfer + Bukti]
    V1 -->|API| X[Disbursement Gateway]
    W --> Y[Verifikasi Mutasi Kas]
    X --> Y

    Y --> AA{Valid?}
    AA -->|Ya + Plafon Penuh| BB[Status: Berjalan]
    AA -->|Tidak| CC[Tolak Mutasi]
    CC --> Z1

    BB --> DD[Anggota: Bayar Angsuran]
    DD --> EE[Pengurus: Verifikasi Pembayaran]
    EE --> FF{Bukti Valid?}
    FF -->|Ya| GG[Update Angsuran: Dibayar]
    FF -->|Tidak| HH[Tolak Pembayaran]
    HH --> DD

    GG --> II{Semua Lunas?}
    II -->|Ya| JJ[Status: Lunas]
    II -->|Tidak| DD

    JJ --> Z1([Selesai])
```

---

## 2. Setoran Simpanan

```mermaid
flowchart TD
    A([Mulai]) --> B[Anggota: Pilih Jenis Simpanan]
    B --> B1{Jenis}
    B1 -->|Pokok| C[Input Nominal]
    B1 -->|Wajib| C
    B1 -->|Sukarela| C
    B1 -->|Mudharabah| C

    C --> D[Input Tanggal Setor]
    D --> E{Metode Pembayaran}
    E -->|Transfer Manual| F[Pilih Rekening Koperasi]
    E -->|QRIS| G[Request QRIS Payment]

    F --> H[Upload Bukti Transfer]
    H --> I[Submit Setoran]
    I --> I1[Status: Menunggu Verifikasi]

    G --> J[Payment Gateway: Generate QR]
    J --> K[Anggota: Scan & Bayar]
    K --> L{Pembayaran?}
    L -->|Berhasil| M[Webhook: Status PAID]
    L -->|Gagal/Expired| N[Webhook: Status Gagal]
    N --> Z([Selesai - Gagal])

    I1 --> O[Pengurus: Cek Mutasi Kas]
    M --> O

    O --> P{Bukti Valid?}
    P -->|Ya| Q[Approve Mutasi]
    Q --> R[Update Saldo Simpanan]
    R --> S[Notify: Setoran Diterima]
    P -->|Tidak| T[Tolak Mutasi]
    T --> U[Notify: Setoran Ditolak]

    S --> Z1([Selesai])
    U --> Z
```

---

## 3. Bayar Angsuran

```mermaid
flowchart TD
    A([Mulai]) --> B[Anggota: Lihat Jadwal Angsuran]
    B --> C[Pilih Tagihan Belum Bayar]
    C --> D{Metode Pembayaran}
    D -->|Transfer Manual| E[Pilih Pembiayaan + Angsuran]
    D -->|QRIS| F[Request QRIS]

    E --> G[Pilih Rekening Koperasi]
    G --> H[Upload Bukti Transfer]
    H --> I[Submit Pembayaran]
    I --> I1[Buat PembayaranAngsuran\nstatus: menunggu_verifikasi]
    I1 --> I2[Buat TransaksiPembayaran]

    F --> J[QR Code ditampilkan]
    J --> K[Anggota: Scan & Bayar]
    K --> L{Pembayaran?}
    L -->|Berhasil| M[Webhook: PAID]
    L -->|Gagal| N[Webhook: Gagal]
    N --> Z([Selesai - Gagal])

    I2 --> O[Pengurus: Verifikasi Mutasi]
    M --> O

    O --> P{Bukti Valid?}
    P -->|Ya| Q[Approve Mutasi]
    Q --> R[Update PembayaranAngsuran]
    R --> S[Update Angsuran: Dibayar]
    S --> T[Set Tanggal Bayar]
    P -->|Tidak| U[Tolak Mutasi]
    U --> V[Notify: Pembayaran Ditolak]

    T --> W{Semua Angsuran Lunas?}
    W -->|Ya| X[Pembiayaan: Lunas]
    W -->|Tidak| Y[Anggota: Cek Angsuran Berikutnya]

    X --> Z1([Selesai])
    Y --> Z1
```

---

## 4. Pencairan Dana Pembiayaan

```mermaid
flowchart TD
    A([Mulai]) --> B[Anggota: Pilih Pembiayaan Disetujui]
    B --> C[Cek Sisa Plafon]
    C --> D{Sisa Plafon > 0?}
    D -->|Tidak| E[Error: Plafon Habis]
    E --> Z([Selesai])

    D -->|Ya| F[Input Data Pencairan]
    F --> G[Nominal + Bank + Rekening Tujuan]
    G --> H[Submit Pencairan]
    H --> I[Status: Menunggu]
    I --> J[Notify Pengurus]

    J --> K[Pengurus: Review Permintaan]
    K --> L{Disetujui?}
    L -->|Tolak| M[Status: Ditolak]
    M --> N[Notify Anggota: Ditolak]
    N --> Z

    L -->|Setuju| O[Status: Diproses]
    O --> P{Metode Cair}

    P -->|Manual| Q[Pilih Rekening Koperasi Sumber]
    Q --> R[Input Referensi Transfer]
    R --> S[Upload Bukti Transfer]
    S --> T[Buat MutasiKas]
    T --> U[Verifikasi Mutasi]

    P -->|API Disbursement| V[Kirim ke Payment Gateway]
    V --> W[Menunggu Callback]
    W --> X{Callback?}
    X -->|Sukses| Y[Update: Selesai]
    X -->|Gagal| Y1[Update: Gagal + Catatan]
    Y1 --> Z

    U --> AA{Valid?}
    AA -->|Ya| AB[Update Pencairan: Selesai]
    AA -->|Tidak| AC[Tolak Mutasi]
    AC --> Z

    AB --> AD[Cek Plafon vs Total Pencairan]
    AD --> AE{Plafon Penuh?}
    AE -->|Ya| AF[Update Pembiayaan: Berjalan]
    AE -->|Tidak| AG[Bisa Ajukan Lagi]

    AF --> AH[Notify Anggota: Dana Ditransfer]
    AG --> AH
    AH --> Z1([Selesai])
```

---

## 5. Marketplace Checkout

```mermaid
flowchart TD
    A([Mulai]) --> B[Pembeli: Browse Marketplace]
    B --> C[Lihat Daftar Produk]
    C --> D{Login?}
    D -->|Belum| E[Login / Register]
    E --> D
    D -->|Ya| F[Lihat Detail Produk]
    F --> G{Stok > 0?}
    G -->|Tidak| H[Produk Habis]
    H --> Z([Selesai])
    G -->|Ya| I[Tambah ke Keranjang]
    I --> J[Update Session Cart]

    J --> K[Buka Keranjang]
    K --> L[Review Item + Jumlah]
    L --> M[Checkout]
    M --> N[Input Alamat Kirim]
    N --> O[Pilih Metode Pengiriman]
    O --> P[Pilih Metode Pembayaran]

    P --> Q{Metode Pembayaran}
    Q -->|Transfer Manual| R[Buat Pesanan\nstatus_pembayaran: menunggu_verifikasi]
    Q -->|Bayar di Tempat| S[Buat Pesanan\nstatus_pembayaran: menunggu]
    Q -->|QRIS| T[Buat Pesanan + QRIS Payment]

    R --> U[Group Item per Toko]
    S --> U
    T --> U

    U --> V[Kurangi Stok Produk]
    V --> W{Stok Habis?}
    W -->|Ya| X[Update Produk: Habis]
    W -->|Tidak| Y[Stok Berkurang]
    X --> Z1[Notify Penjual & Pengurus]
    Y --> Z1

    T --> Z2[Payment Gateway: Generate QR]
    Z2 --> Z3[Anggota: Scan & Bayar]
    Z3 --> Z4{Berhasil?}
    Z4 -->|Ya| Z5[Webhook: PAID]
    Z4 -->|Gagal| Z6[Notify: Pembayaran Gagal]
    Z6 --> Z

    Z1 --> Z7[Penjual: Proses Pesanan]
    Z5 --> Z7

    Z7 --> Z8[Update Status: Dikirim]
    Z8 --> Z9[Pembeli: Terima Pesanan]
    Z9 --> Z10[Update Pesanan: Selesai]
    Z10 --> Z11([Selesai])
```

---

## 6. DPS Validasi Akad Syariah

```mermaid
flowchart TD
    A([Mulai]) --> B[DPS: Login]
    B --> C[Masuk Menu Audit Akad]
    C --> D[Lihat Daftar Pembiayaan]
    D --> E[Filter by Status/Akad/Search]
    E --> F[Klik Detail Pembiayaan]
    F --> G[Review Struktur Akad]
    G --> H[Lihat Dokumen Pengajuan]
    H --> I[Lihat Data Anggota & Toko]

    I --> J{Data Lengkap?}
    J -->|Tidak| K[Catat Temuan: Data Tidak Lengkap]
    K --> L[Mulai Checklist Validasi]
    J -->|Ya| L

    L --> M[Kriteria 1: Kepatuhan Syariah]
    M --> N[Kriteria 2: Kelengkapan Dokumen]
    N --> O[Kriteria 3: Kewajaran Margin/Nisbah]
    O --> P[Kriteria Lainnya...]

    P --> Q[Input Kesimpulan]
    Q --> R{Perlu Catatan Perbaikan?}
    R -->|Ya| S[Input Catatan Perbaikan]
    R -->|Tidak| T[Submit Validasi]
    S --> T

    T --> U[Simpan Snapshot Data]
    U --> V[Hitung Hash Snapshot]
    V --> W[Simpan ValidasiAkadDps\nversi baru]
    W --> X[Update Pembiayaan\nstatus_validasi_dps]

    X --> Y{Hasil?}
    Y -->|Sesuai Syariah| Z1[Notify Pengurus:\nAkad Sesuai]
    Y -->|Perlu Perbaikan| Z2[Notify Anggota:\nPerlu Perbaikan]
    Y -->|Tidak Sesuai| Z3[Notify Anggota:\nAkad Ditolak]

    Z1 --> Z4([Selesai])
    Z2 --> Z4
    Z3 --> Z4
```

---

## 7. Toko & Produk Moderasi

```mermaid
flowchart TD
    A([Mulai]) --> B[Anggota: Buka Lapak]
    B --> C[Input Data Toko]
    C --> D[Submit Pendaftaran]
    D --> E[Status Toko: Menunggu Verifikasi]

    E --> F[Pengurus: Review Toko]
    F --> G{Toko Layak?}
    G -->|Ya| H[Approve Toko]
    H --> I[Status: Aktif]
    I --> J[Notify Anggota: Toko Aktif]
    G -->|Tidak| K[Reject Toko]
    K --> L[Status: Nonaktif]
    L --> M[Notify Anggota: Toko Ditolak]

    J --> N[Anggota: Tambah Produk]
    N --> O[Input Data Produk]
    O --> P[Submit Produk]
    P --> Q[Status: Menunggu Verifikasi]

    Q --> R[Pengurus: Review Produk]
    R --> S{Disetujui Pengurus?}
    S -->|Ya| T[DPS: Review Syariah]
    S -->|Tidak| U[Reject + Notify]
    U --> Z([Selesai])

    T --> V{Sesuai Syariah?}
    V -->|Ya| W[Status: Aktif]
    W --> X[Produk Tampil di Marketplace]
    V -->|Tidak| Y[Reject + Notify]
    Y --> Z

    X --> Z1([Selesai])
```

---

## 8. Mutasi Kas (Verifikasi Terpusat)

```mermaid
flowchart TD
    A([Mulai]) --> B[Anggota: Submit Transaksi]
    B --> B1{Jenis Transaksi}
    B1 -->|Simpanan| C[Buat SetoranSimpanan]
    B1 -->|Angsuran| D[Buat PembayaranAngsuran]
    B1 -->|Pencairan| E[Buat PencairanDana]

    C --> F[Buat TransaksiPembayaran\nstatus: menunggu_verifikasi]
    D --> F
    E --> F

    F --> G[Pengurus: Buka Mutasi Kas]
    G --> H[Lihat Daftar Menunggu]
    H --> I[Cek Bukti Transfer]
    I --> J[Cek Rekening Sumber/Tujuan]
    J --> K[Cek Nominal & Tanggal]

    K --> L{Semua Valid?}
    L -->|Ya| M[Approve Mutasi]
    M --> N{Jenis Transaksi}
    N -->|Simpanan| O[Tambah Saldo Simpanan]
    N -->|Angsuran| P[Update Angsuran: Dibayar]
    N -->|Pencairan| Q[Update Pencairan: Selesai]

    O --> R[Notify Anggota: Diterima]
    P --> R
    Q --> R

    L -->|Tidak| S[Tolak Mutasi]
    S --> T[Input Alasan Penolakan]
    T --> U[Notify Anggota: Ditolak]

    R --> V([Selesai])
    U --> V
```

---

## 9. Pembayaran QRIS

```mermaid
flowchart TD
    A([Mulai]) --> B[Anggota: Pilih Item Dibayar]
    B --> B1{Jenis Pembayaran}
    B1 -->|Angsuran| C[Request QRIS Angsuran]
    B1 -->|Simpanan| D[Request QRIS Simpanan]
    B1 -->|Marketplace| E[Request QRIS Pesanan]

    C --> F[PaymentGatewayService: Create QRIS]
    D --> F
    E --> F

    F --> G[Payment Gateway: Generate QR]
    G --> H[Simpan Payment: pending]
    H --> I[Tampilkan QR Code]
    I --> J[Anggota: Scan QR]

    J --> K[User Konfirmasi Pembayaran]
    K --> L{Pembayaran?}
    L -->|Berhasil| M[Webhook: capture/settlement]
    L -->|Expired| N[Webhook: expire]
    L -->|Dibatalkan| O[Webhook: cancel/deny]

    M --> P[PaymentWebhookController]
    P --> Q[Update Payment: PAID]
    Q --> R{Tipe Pembayaran}
    R -->|Angsuran| S[Update PembayaranAngsuran]
    R -->|Simpanan| T[Update SetoranSimpanan]
    R -->|Marketplace| U[Update Pesanan]

    S --> V[Log Webhook Success]
    T --> V
    U --> V

    N --> W[Update Payment: expired]
    O --> W
    W --> X[Log Webhook Status]

    V --> Y([Selesai - PAID])
    X --> Z([Selesai - Gagal])
```

---

## 10. Login & Autentikasi

```mermaid
flowchart TD
    A([Mulai]) --> B{Sudah Login?}
    B -->|Ya| C[Redirect ke Dashboard]
    C --> Z([Selesai])
    B -->|Tidak| D[Input Email & Password]

    D --> E{Akun Ada?}
    E -->|Ya| F{Password Benar?}
    E -->|Tidak| G{Ingin Register?}
    G -->|Ya| H[Form Register]
    H --> I[Pilih Role: Anggota/Pelanggan]
    I --> J[Isi Data Registrasi]
    J --> K[Submit + Email Verifikasi]
    K --> Z
    G -->|Tidak| D

    F -->|Ya| L{Email Terverifikasi?}
    F -->|Tidak| M[Error: Password Salah]
    M --> D

    L -->|Ya| N[Cek Role Pengguna]
    L -->|Tidak| O[Pesan Verifikasi Email]
    O --> Z

    N --> P{Role?}
    P -->|admin| Q[/admin/dashboard]
    P -->|pengurus| R[/pengurus/dashboard]
    P -->|anggota| S[/anggota/dashboard]
    P -->|dps| T[/dps/dashboard]
    P -->|pelanggan| U[/pelanggan/dashboard]

    Q --> Z
    R --> Z
    S --> Z
    T --> Z
    U --> Z
```

---

## 11. Alur Notifikasi Sistem

```mermaid
flowchart TD
    A([Event Trigger]) --> B{Tipe Event}
    B -->|Pembiayaan Diajukan| C[Notify DPS: Audit Akad]
    B -->|Pembiayaan Disetujui| D[Notify Anggota: Disetujui]
    B -->|Pencairan Diajukan| E[Notify Pengurus: Pencairan Baru]
    B -->|Toko Diverifikasi| F[Notify Anggota: Toko Aktif/Ditolak]
    B -->|Akad Divalidasi DPS| G[Notify Pengurus + Anggota]
    B -->|Angsuran Dibayar| H[Notify Pengurus: Verifikasi]
    B -->|Simpanan Disetor| I[Notify Pengurus: Verifikasi]
    B -->|Pesanan Baru| J[Notify Penjual + Pengurus]

    C --> K[Simpan Database Notification]
    D --> K
    E --> K
    F --> K
    G --> K
    H --> K
    I --> K
    J --> K

    K --> L[Distribusi ke Role]
    L --> M[Admin: Lihat Notifikasi]
    L --> N[Pengurus: Lihat Notifikasi]
    L --> O[DPS: Lihat Notifikasi]
    L --> P[Anggota: Lihat Notifikasi]

    M --> Q[User Klik Notifikasi]
    N --> Q
    O --> Q
    P --> Q

    Q --> R[Update Status: Dibaca]
    R --> S([Selesai])
```

---

## State Diagrams

### Status Pembiayaan

```mermaid
stateDiagram-v2
    [*] --> diajukan: Anggota submit pengajuan

    diajukan --> disetujui: Pengurus approve\n(DPS validasi sesuai)
    diajukan --> ditolak: Pengurus tolak
    diajukan --> menunggu_perbaikan: DPS: perlu perbaikan

    menunggu_perbaikan --> diajukan: Anggota submit ulang

    disetujui --> berjalan: Pencairan selesai\n(plafon cair penuh)

    berjalan --> lunas: Semua angsuran dibayar

    ditolak --> [*]
    lunas --> [*]
```

### Status Pencairan Dana

```mermaid
stateDiagram-v2
    [*] --> menunggu: Anggota submit pencairan

    menunggu --> diproses: Pengurus approve
    menunggu --> ditolak: Pengurus tolak

    diproses --> selesai: Mutasi diverifikasi / callback sukses
    diproses --> ditolak: Mutasi ditolak / callback gagal

    selesai --> [*]
    ditolak --> [*]
```

### Status Transaksi Kas

```mermaid
stateDiagram-v2
    [*] --> menunggu_verifikasi: Anggota submit transaksi

    menunggu_verifikasi --> diverifikasi: Pengurus approve
    menunggu_verifikasi --> ditolak: Pengurus tolak

    diverifikasi --> [*]: Transaksi selesai
    ditolak --> [*]: Transaksi ditolak
```

### Status Toko

```mermaid
stateDiagram-v2
    [*] --> menunggu_verifikasi: Anggota buka lapak

    menunggu_verifikasi --> aktif: Pengurus approve
    menunggu_verifikasi --> nonaktif: Pengurus reject

    aktif --> nonaktif: Pengurus nonaktifkan
    nonaktif --> aktif: Pengurus aktifkan kembali
```
