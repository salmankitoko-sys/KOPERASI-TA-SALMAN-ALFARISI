# Diagram Alur Sistem Koperasi Syariah

Dokumen ini memakai Mermaid. Buka file ini di Markdown preview yang mendukung Mermaid, atau salin blok diagram ke https://mermaid.live.

## Aktor Sistem

- Admin: mengelola pengguna, memantau akses, melihat notifikasi sistem.
- Pengurus: mengelola anggota, menyetujui pembiayaan, pencairan dana, mutasi kas, rekening koperasi, toko, produk, dan notifikasi.
- Anggota: mengajukan pembiayaan, pencairan dana, pembayaran angsuran, setoran simpanan, lapak, produk, pesanan, wishlist, dan pembayaran QRIS.
- DPS: melakukan pengawasan syariah, audit akad, review produk, temuan, opini syariah, laporan pengawasan, dan notifikasi.
- Pelanggan: melihat marketplace, checkout, memantau pesanan, dan menandai pesanan diterima.
- Payment Gateway: mengirim callback pembayaran QRIS dan disbursement.

---

## Flowchart Sistem Umum

```mermaid
flowchart TD
    A([Mulai]) --> B{Pengguna login?}
    B -- Tidak --> C[Marketplace publik]
    C --> C1[Lihat produk]
    C --> C2[Kelola keranjang]
    C2 --> B

    B -- Ya --> D{Role pengguna}
    D -- Admin --> AD[Dashboard Admin]
    D -- Pengurus --> PG[Dashboard Pengurus]
    D -- Anggota --> AG[Dashboard Anggota]
    D -- DPS --> DP[Dashboard DPS]
    D -- Pelanggan --> PL[Dashboard Pelanggan]

    AD --> AD1[Kelola user]
    AD --> AD2[Monitor akses dan notifikasi]

    PG --> PG1[Manajemen anggota]
    PG --> PG2[Review pembiayaan]
    PG --> PG3[Transaksi dan mutasi kas]
    PG --> PG4[Moderasi toko dan produk]

    AG --> AG1[Simpanan]
    AG --> AG2[Pembiayaan]
    AG --> AG3[Pencairan dan angsuran]
    AG --> AG4[Marketplace, lapak, pesanan]

    DP --> DP1[Pengawasan read-only]
    DP --> DP2[Audit akad]
    DP --> DP3[Temuan, opini, laporan]
    DP --> DP4[Review produk]

    PL --> PL1[Marketplace]
    PL --> PL2[Checkout]
    PL --> PL3[Pesanan saya]
```

---

## Flowchart Per Aktor

### 1. Admin

```mermaid
flowchart TD
    A([Admin login]) --> B[Dashboard Admin]
    B --> C[Lihat statistik user]
    B --> D[Kelola data user]
    B --> E[Export data user]
    B --> F[Kelola notifikasi]
    D --> G{Aksi user}
    G -- Detail --> H[Lihat profil dan aktivitas]
    G -- Hapus --> I[Hapus user]
    F --> J[Tandai notifikasi dibaca]
    H --> K([Selesai])
    I --> K
    J --> K
```

### 2. Pengurus

```mermaid
flowchart TD
    A([Pengurus login]) --> B[Dashboard Pengurus]
    B --> C[Manajemen anggota]
    B --> D[Review pembiayaan]
    B --> E[Transaksi dan mutasi]
    B --> F[Marketplace]

    D --> D1[Lihat pengajuan pembiayaan]
    D1 --> D2{Keputusan}
    D2 -- Setujui --> D3[Status pembiayaan disetujui]
    D2 -- Tolak --> D4[Status pembiayaan ditolak]
    D3 --> D5[Anggota dapat ajukan pencairan]

    E --> E1[Kelola rekening koperasi]
    E --> E2[Review pencairan]
    E2 --> E3{Metode pencairan}
    E3 -- Manual --> E4[Catat transfer dan bukti]
    E3 -- API --> E5[Kirim disbursement]
    E4 --> E6[Mutasi kas menunggu verifikasi]
    E5 --> E7[Menunggu callback gateway]
    E6 --> E8[Verifikasi mutasi]
    E8 --> E9[Status pencairan selesai]
    E9 --> E10[Jika plafon cair penuh, pembiayaan berjalan]

    F --> F1[Verifikasi toko]
    F --> F2[Moderasi produk]
    F --> F3[Broadcast notifikasi]
```

### 3. Anggota

```mermaid
flowchart TD
    A([Anggota login]) --> B[Dashboard Anggota]
    B --> C[Simpanan]
    B --> D[Pembiayaan]
    B --> E[Transaksi]
    B --> F[Marketplace dan Lapak]

    C --> C1[Setor simpanan]
    C1 --> C2[Upload bukti transfer]
    C2 --> C3[Menunggu verifikasi mutasi]

    D --> D1[Pilih akad]
    D1 --> D2[Isi kalkulator pembiayaan]
    D2 --> D3[Upload formulir pengajuan]
    D3 --> D4[Pengajuan dikirim]
    D4 --> D5[Menunggu validasi DPS dan review pengurus]

    E --> E1[Ajukan pencairan dana]
    E1 --> E2[Menunggu pengurus]
    E --> E3[Bayar angsuran]
    E3 --> E4[Pilih tagihan]
    E4 --> E5[Upload bukti pembayaran]
    E5 --> E6[Menunggu verifikasi mutasi]

    F --> F1[Buat lapak]
    F1 --> F2[Menunggu verifikasi pengurus]
    F --> F3[Kelola produk]
    F --> F4[Kelola pesanan lapak]
    F --> F5[Checkout marketplace]
```

### 4. DPS

```mermaid
flowchart TD
    A([DPS login]) --> B[Dashboard DPS]
    B --> C[Pengawasan transaksi read-only]
    B --> D[Audit akad pembiayaan]
    B --> E[Review produk marketplace]
    B --> F[Temuan audit]
    B --> G[Opini syariah]
    B --> H[Laporan pengawasan]

    D --> D1[Lihat detail akad dan dokumen]
    D1 --> D2[Isi checklist kepatuhan]
    D2 --> D3{Hasil audit}
    D3 -- Sesuai --> D4[Validasi sesuai syariah]
    D3 -- Perlu perbaikan --> D5[Catat catatan audit]

    E --> E1[Lihat produk]
    E1 --> E2[Review kepatuhan produk]
    F --> F1[Input temuan terkait pembiayaan atau produk]
    G --> G1[Buat opini syariah]
    H --> H1[Buat atau update laporan]
```

### 5. Pelanggan dan Publik

```mermaid
flowchart TD
    A([Pengunjung]) --> B[Marketplace publik]
    B --> C[Lihat daftar produk]
    C --> D[Lihat detail produk]
    D --> E[Tambah ke keranjang]
    E --> F{Login sebagai anggota atau pelanggan?}
    F -- Tidak --> G[Login atau registrasi]
    F -- Ya --> H[Checkout]
    H --> I[Pesanan dibuat]
    I --> J[Pesanan saya]
    J --> K[Terima pesanan]
    K --> L([Selesai])
```

---

## Use Case Diagram

```mermaid
flowchart LR
    Admin([Admin])
    Pengurus([Pengurus])
    Anggota([Anggota])
    DPS([DPS])
    Pelanggan([Pelanggan])
    Gateway([Payment Gateway])

    subgraph Sistem[Koperasi Syariah]
        UC1((Login dan profil))
        UC2((Kelola pengguna))
        UC3((Monitor akses))
        UC4((Ajukan pembiayaan))
        UC5((Validasi akad syariah))
        UC6((Approve atau tolak pembiayaan))
        UC7((Ajukan pencairan))
        UC8((Cairkan dana pembiayaan))
        UC9((Verifikasi mutasi kas))
        UC10((Bayar angsuran))
        UC11((Setor simpanan))
        UC12((Kelola rekening koperasi))
        UC13((Kelola lapak dan produk))
        UC14((Moderasi toko dan produk))
        UC15((Belanja marketplace))
        UC16((Kelola pesanan))
        UC17((Temuan, opini, laporan DPS))
        UC18((Pembayaran QRIS))
        UC19((Callback pembayaran dan disbursement))
        UC20((Notifikasi))
    end

    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC20

    Pengurus --> UC1
    Pengurus --> UC2
    Pengurus --> UC6
    Pengurus --> UC8
    Pengurus --> UC9
    Pengurus --> UC12
    Pengurus --> UC14
    Pengurus --> UC20

    Anggota --> UC1
    Anggota --> UC4
    Anggota --> UC7
    Anggota --> UC10
    Anggota --> UC11
    Anggota --> UC13
    Anggota --> UC15
    Anggota --> UC16
    Anggota --> UC18
    Anggota --> UC20

    DPS --> UC1
    DPS --> UC5
    DPS --> UC14
    DPS --> UC17
    DPS --> UC20

    Pelanggan --> UC1
    Pelanggan --> UC15
    Pelanggan --> UC16

    Gateway --> UC19
    UC18 --> UC19
    UC8 --> UC19
```

---

## Activity Diagram Pembiayaan Sampai Berjalan

```mermaid
flowchart TD
    A([Mulai]) --> B[Anggota pilih menu pembiayaan]
    B --> C[Pilih akad]
    C --> D[Isi data simulasi dan tujuan]
    D --> E[Upload formulir pengajuan]
    E --> F{Validasi form berhasil?}
    F -- Tidak --> G[Tampilkan error dan kembali ke form]
    G --> D
    F -- Ya --> H[Simpan pembiayaan status diajukan]
    H --> I[Kirim notifikasi ke DPS, Pengurus, Admin]
    I --> J[DPS audit akad]
    J --> K{Akad sesuai syariah?}
    K -- Tidak --> L[Catat temuan atau catatan perbaikan]
    L --> M[Pengajuan menunggu perbaikan atau ditolak]
    K -- Ya --> N[Pengurus review pembiayaan]
    N --> O{Keputusan pengurus}
    O -- Tolak --> P[Status pembiayaan ditolak]
    O -- Setujui --> Q[Status pembiayaan disetujui]
    Q --> R[Anggota ajukan pencairan]
    R --> S[Pengurus cairkan dana]
    S --> T[Mutasi kas diverifikasi]
    T --> U[Status pencairan selesai]
    U --> V{Plafon sudah cair penuh?}
    V -- Tidak --> R
    V -- Ya --> W[Status pembiayaan berjalan]
    W --> X[Anggota bisa bayar angsuran]
    X --> Y([Selesai])
```

---

## Activity Diagram Simpanan dan Angsuran

```mermaid
flowchart TD
    A([Mulai]) --> B{Jenis transaksi anggota}
    B -- Setor simpanan --> C[Isi nominal dan jenis simpanan]
    C --> D[Upload bukti transfer]
    D --> E[Simpan setoran status menunggu verifikasi]

    B -- Bayar angsuran --> F[Pilih pembiayaan berjalan]
    F --> G[Pilih tagihan angsuran]
    G --> H[Upload bukti pembayaran]
    H --> I[Simpan pembayaran status menunggu verifikasi]

    E --> J[Masuk antrian Mutasi Kas]
    I --> J
    J --> K[Pengurus cek bukti dan rekening]
    K --> L{Valid?}
    L -- Tidak --> M[Mutasi ditolak]
    M --> N[Notifikasi ke anggota]
    L -- Ya --> O[Mutasi diverifikasi]
    O --> P{Jenis transaksi}
    P -- Simpanan --> Q[Tambah saldo simpanan]
    P -- Angsuran --> R[Tandai angsuran dibayar]
    Q --> S[Notifikasi berhasil]
    R --> S
    S --> T([Selesai])
```

---

## Activity Diagram Marketplace dan Lapak

```mermaid
flowchart TD
    A([Mulai]) --> B{Aktor}
    B -- Anggota penjual --> C[Buat lapak]
    C --> D[Pengurus verifikasi toko]
    D --> E{Toko aktif?}
    E -- Tidak --> F[Toko ditolak atau perlu perbaikan]
    E -- Ya --> G[Anggota tambah produk]
    G --> H[Pengurus atau DPS review produk]
    H --> I{Produk disetujui?}
    I -- Tidak --> J[Produk ditolak]
    I -- Ya --> K[Produk tampil di marketplace]

    B -- Pembeli --> L[Lihat marketplace]
    L --> M[Tambah produk ke keranjang]
    M --> N[Checkout]
    N --> O[Pesanan dibuat]
    O --> P[Penjual proses pesanan]
    P --> Q[Pembeli terima pesanan]
    Q --> R([Selesai])
```

---

## Sequence Diagram Pengajuan Pembiayaan

```mermaid
sequenceDiagram
    actor Anggota
    participant UI as Dashboard Anggota
    participant PC as PembiayaanController
    participant DB as Database
    participant NS as NotificationService
    actor DPS
    actor Pengurus

    Anggota->>UI: Isi form pembiayaan dan upload formulir
    UI->>PC: POST anggota/pembiayaan
    PC->>PC: Normalisasi dan validasi input
    alt Validasi gagal
        PC-->>UI: Redirect dengan error dan input lama
    else Validasi berhasil
        PC->>DB: Simpan pembiayaan status diajukan
        PC->>DB: Simpan detail akad dan dokumen
        PC->>DB: Buat jadwal angsuran jika Murabahah/Qardh
        PC->>NS: Kirim notifikasi pembiayaan diajukan
        NS-->>DPS: Notifikasi audit akad
        NS-->>Pengurus: Notifikasi review pembiayaan
        PC-->>UI: Redirect sukses
    end
```

---

## Sequence Diagram Validasi DPS dan Persetujuan Pengurus

```mermaid
sequenceDiagram
    actor DPS
    participant Audit as AuditController
    participant DB as Database
    participant NS as NotificationService
    actor Pengurus
    participant PGC as PengurusPembiayaanController
    actor Anggota

    DPS->>Audit: Buka detail pembiayaan
    Audit->>DB: Ambil pembiayaan, detail akad, dokumen
    DPS->>Audit: Simpan hasil validasi akad
    Audit->>DB: Simpan validasi DPS dan snapshot
    Audit->>NS: Kirim hasil validasi
    NS-->>Anggota: Notifikasi hasil audit
    NS-->>Pengurus: Notifikasi hasil audit

    Pengurus->>PGC: Approve atau tolak pembiayaan
    PGC->>DB: Update status pembiayaan
    alt Disetujui
        PGC->>NS: Notifikasi pembiayaan disetujui
        NS-->>Anggota: Ajukan pencairan dana
    else Ditolak
        PGC->>NS: Notifikasi pembiayaan ditolak
        NS-->>Anggota: Lihat alasan penolakan
    end
```

---

## Sequence Diagram Pencairan Dana Pembiayaan

```mermaid
sequenceDiagram
    actor Anggota
    participant AT as AnggotaTransaksiController
    participant DB as Database
    participant NS as NotificationService
    actor Pengurus
    participant PT as PengurusTransaksiController
    participant Gateway as Disbursement Gateway

    Anggota->>AT: POST pengajuan pencairan
    AT->>DB: Validasi pembiayaan disetujui dan sisa plafon
    AT->>DB: Simpan pencairan status menunggu
    AT->>NS: Notifikasi pencairan diajukan
    NS-->>Pengurus: Permintaan pencairan baru

    Pengurus->>PT: Cairkan dana
    PT->>DB: Validasi rekening sumber dan status menunggu
    PT->>DB: Update status pencairan menjadi diproses

    alt Transfer manual
        Pengurus->>PT: Upload bukti transfer
        PT->>DB: Simpan bukti transfer dan mutasi kas menunggu_verifikasi
        Pengurus->>PT: Verifikasi mutasi kas
        PT->>DB: Update pencairan selesai
        PT->>DB: Jika plafon cair penuh, pembiayaan berjalan
        PT->>NS: Notifikasi dana ditransfer
        NS-->>Anggota: Dana pencairan dikirim
    else Disbursement API
        PT->>Gateway: Create disbursement
        Gateway-->>PT: Reference processing
        PT->>DB: Simpan reference dan status processing
        Gateway-->>PT: Webhook sukses atau gagal
        PT->>DB: Update pencairan selesai atau catatan gagal
        PT->>NS: Notifikasi hasil pencairan
        NS-->>Anggota: Dana dicairkan atau gagal
    end
```

---

## Sequence Diagram Pembayaran Angsuran Manual

```mermaid
sequenceDiagram
    actor Anggota
    participant AT as AnggotaTransaksiController
    participant DB as Database
    participant NS as NotificationService
    actor Pengurus
    participant PT as PengurusTransaksiController

    Anggota->>AT: Pilih pembiayaan dan angsuran
    AT->>DB: Validasi pembiayaan berjalan dan angsuran belum bayar
    Anggota->>AT: Upload bukti transfer
    AT->>DB: Simpan pembayaran_angsuran menunggu_verifikasi
    AT->>DB: Simpan transaksi_pembayaran referensi pembayaran_angsuran
    AT->>NS: Notifikasi angsuran dibayar
    NS-->>Pengurus: Pembayaran menunggu verifikasi

    Pengurus->>PT: Buka Mutasi Kas
    Pengurus->>PT: Approve mutasi
    PT->>DB: Update transaksi diverifikasi
    PT->>DB: Update pembayaran_angsuran diverifikasi
    PT->>DB: Update angsuran dibayar dan tanggal bayar
    PT->>NS: Notifikasi angsuran diverifikasi
    NS-->>Anggota: Pembayaran diterima
```

---

## Sequence Diagram Pembayaran QRIS

```mermaid
sequenceDiagram
    actor Anggota
    participant Pay as PaymentController
    participant Service as PaymentGatewayService
    participant Gateway as Payment Gateway
    participant Webhook as PaymentWebhookController
    participant DB as Database

    Anggota->>Pay: Pilih bayar QRIS
    Pay->>DB: Validasi tagihan atau simpanan
    Pay->>Service: Buat transaksi QRIS
    Service->>Gateway: Request QRIS
    Gateway-->>Service: QR code dan payment code
    Service-->>Pay: Data pembayaran
    Pay->>DB: Simpan payment menunggu
    Pay-->>Anggota: Tampilkan QRIS

    Gateway-->>Webhook: Callback pembayaran
    Webhook->>DB: Verifikasi payment code dan status
    alt Pembayaran sukses
        Webhook->>DB: Update payment paid
        Webhook->>DB: Update angsuran atau simpanan terkait
    else Pembayaran gagal atau expired
        Webhook->>DB: Update payment gagal/expired
    end
```

---

## Sequence Diagram Marketplace

```mermaid
sequenceDiagram
    actor Pembeli
    participant Market as PublicMarketplaceController
    participant DB as Database
    actor Penjual as Anggota Penjual
    participant Lapak as LapakPesananController

    Pembeli->>Market: Lihat produk marketplace
    Market->>DB: Ambil produk aktif
    DB-->>Market: Daftar produk
    Pembeli->>Market: Tambah ke keranjang
    Market->>DB: Simpan/update cart session
    Pembeli->>Market: Checkout
    Market->>DB: Simpan pesanan dan item pesanan
    Market-->>Pembeli: Pesanan dibuat

    Penjual->>Lapak: Buka pesanan lapak
    Lapak->>DB: Ambil pesanan toko
    Penjual->>Lapak: Update status pesanan
    Lapak->>DB: Simpan status pesanan
    Pembeli->>Market: Tandai pesanan diterima
    Market->>DB: Update pesanan selesai
```

---

## Catatan Status Penting

```mermaid
stateDiagram-v2
    [*] --> diajukan
    diajukan --> disetujui: Pengurus approve
    diajukan --> ditolak: Pengurus tolak
    disetujui --> berjalan: Pencairan selesai / plafon cair
    berjalan --> lunas: Semua angsuran dibayar
    ditolak --> [*]
    lunas --> [*]
```

```mermaid
stateDiagram-v2
    [*] --> menunggu
    menunggu --> diproses: Pengurus setujui/cairkan
    menunggu --> ditolak: Pengurus tolak
    diproses --> selesai: Mutasi diverifikasi / callback sukses
    diproses --> ditolak: Ditolak atau gagal
    selesai --> [*]
    ditolak --> [*]
```