# 📊 Diagram Use Case, Sequence & Activity — Sistem Koperasi Marketplace

---

## 1. USE CASE DIAGRAM — SYSTEM OVERVIEW

```mermaid
graph TB
    subgraph Actors
        A["👤 Anggota<br/>(Penjual + Pembeli)"]
        P["🛒 Pelanggan<br/>(Pembeli)"]
        PG["👨‍💼 Pengurus<br/>(Admin Koperasi)"]
        DPS["⚖️ DPS<br/>(Dewan Pengawas Syariah)"]
        SYS["🔧 System<br/>(Payment Gateway)"]
    end

    subgraph "Marketplace"
        UC1["Browse Produk"]
        UC2["Lihat Detail Produk"]
        UC3["Kelola Keranjang"]
        UC4["Checkout"]
        UC5["Pembayaran"]
        UC6["Upload Bukti Transfer"]
        UC7["Konfirmasi Penerimaan"]
        UC8["Kelola Toko Saya"]
        UC9["Kelola Produk"]
        UC10["Proses Pesanan Masuk"]
        UC11["Konfirmasi Pembayaran"]
        UC12["Kelola Wishlist"]
    end

    subgraph "Koperasi"
        UC13["Lihat Simpanan"]
        UC14["Setor Simpanan"]
        UC15["Ajukan Pembiayaan"]
        UC16["Bayar Angsuran"]
        UC17["Lihat Bagi Hasil"]
        UC18["Monitoring Keuangan"]
    end

    subgraph "Moderasi & Pengawasan"
        UC19["Verifikasi Toko"]
        UC20["Moderasi Produk"]
        UC21["Review Opini Syariah"]
        UC22["Catat Temuan Audit"]
        UC23["Buat Laporan Pengawasan"]
    end

    subgraph "Transaksi Koperasi"
        UC24["Verifikasi Transaksi"]
        UC25["Proses Pencairan"]
        UC26["Mutasi Kas"]
        UC27["Kelola Rekening"]
    end

    subgraph "Sistem"
        UC28["Notifikasi Real-time"]
        UC29["Webhook Pembayaran"]
        UC30["Autentikasi & Otorisasi"]
    end

    A --> UC1
    A --> UC2
    A --> UC3
    A --> UC4
    A --> UC5
    A --> UC6
    A --> UC7
    A --> UC8
    A --> UC9
    A --> UC10
    A --> UC11
    A --> UC12
    A --> UC13
    A --> UC14
    A --> UC15
    A --> UC16
    A --> UC17
    A --> UC18

    P --> UC1
    P --> UC2
    P --> UC3
    P --> UC4
    P --> UC5
    P --> UC6
    P --> UC7
    P --> UC12

    PG --> UC19
    PG --> UC24
    PG --> UC25
    PG --> UC26
    PG --> UC27
    PG --> UC18

    DPS --> UC20
    DPS --> UC21
    DPS --> UC22
    DPS --> UC23
    DPS --> UC18

    SYS --> UC29
    SYS --> UC28

    UC4 -.->|<<include>>| UC3
    UC5 -.->|<<include>>| UC4
    UC6 -.->|<<extend>>| UC5
    UC11 -.->|<<extend>>| UC10
    UC7 -.->|<<extend>>| UC10
    UC19 -.->|<<include>>| UC8
    UC20 -.->|<<include>>| UC9
```

---

## 2. USE CASE DETAIL — MARKETPLACE

```mermaid
graph LR
    subgraph "UC: Browse & Beli Produk"
        direction TB
        B1["Pembeli mengakses /marketplace"]
        B2["Filter produk per kategori"]
        B3["Lihat detail produk"]
        B4["Tambah ke keranjang"]
        B5["Checkout → pilih pengiriman & pembayaran"]
        B6["Bayar → transfer/QRIS/cod"]
        B7["Upload bukti transfer (jika transfer manual)"]
        B8["Konfirmasi penerimaan barang"]
        B1 --> B2 --> B3 --> B4 --> B5 --> B6 --> B7 --> B8
    end

    subgraph "UC: Jual Produk"
        direction TB
        S1["Buka toko baru"]
        S2["Pengurus verifikasi toko"]
        S3["Tambah produk baru"]
        S4["DPS review produk (moderasi syariah)"]
        S5["Produk aktif di marketplace"]
        S6["Terima pesanan masuk"]
        S7["Konfirmasi pembayaran pembeli"]
        S8["Proses & kirim pesanan"]
        S1 --> S2 --> S3 --> S4 --> S5 --> S6 --> S7 --> S8
    end
```

---

## 3. SEQUENCE DIAGRAM — Checkout & Pembayaran

### 3a. Checkout Lengkap

```mermaid
sequenceDiagram
    autonumber
    actor Buyer as 👤 Pembeli
    participant Cart as 🛒 Keranjang(Session)
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Seller as 🏪 Penjual
    actor Admin as 👨‍💼 Pengurus

    Buyer->>Cart: Tambah produk ke keranjang
    Cart-->>Buyer: Keranjang updated

    Buyer->>System: GET /marketplace/checkout
    System-->>Buyer: Form checkout (alamat, pengiriman, pembayaran)

    Buyer->>System: POST /marketplace/checkout
    Note over System: Group items per toko

    loop Untuk setiap toko
        System->>DB: BEGIN TRANSACTION + lockForUpdate()
        System->>DB: Cek stok produk
        alt Stok mencukupi
            System->>DB: Decrement stok produk
            System->>DB: Create Pesanan (status: menunggu)
            System->>DB: Create PesananItem (snapshot nama & harga)
        else Stok habis
            System-->>Buyer: Error: stok tidak mencukupi
        end
        System->>DB: COMMIT
    end

    System->>Notify: pesananBaru(pesanan)
    Notify->>Seller: Notifikasi: "Pesanan Baru Masuk!"
    Notify->>Admin: Notifikasi: "Pesanan Marketplace Baru"
    Notify->>Buyer: Notifikasi: "Pesanan Berhasil Dibuat!"

    System-->>Buyer: Redirect ke /marketplace/pembayaran
```

### 3b. Pembayaran Transfer Manual

```mermaid
sequenceDiagram
    autonumber
    actor Buyer as 👤 Pembeli
    participant Payment as 💳 Halaman Pembayaran
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Seller as 🏪 Penjual

    Buyer->>Payment: GET /marketplace/pembayaran
    Payment-->>Buyer: Instruksi bayar + No. rekening BSI

    Note over Buyer: Buyer transfer uang ke rekening BSI

    Buyer->>Payment: Upload bukti transfer (form)
    Payment->>System: POST /pesanan-saya/{id}/upload-bukti
    System->>DB: Update pesanan.bukti_transfer
    System->>DB: Update status_pembayaran = menunggu_konfirmasi
    System->>Notify: buktiTransferDiterima(pesanan)
    Notify->>Seller: Notifikasi: "Bukti Transfer Diterima"

    Seller->>System: POST /anggota/lapak/pesanan/{id}/konfirmasi-pembayaran
    System->>DB: Update status_pembayaran = terverifikasi
    System->>Notify: pembayaranDikonfirmasi(pesanan)
    Notify->>Buyer: Notifikasi: "Pembayaran Terverifikasi!"

    Note over Seller: Penjual bisa proses pesanan
```

### 3c. Pembayaran QRIS

```mermaid
sequenceDiagram
    autonumber
    actor Buyer as 👤 Pembeli
    participant Payment as 💳 Halaman Pembayaran
    participant System as 🖥️ System
    participant Gateway as 🌐 Payment Gateway
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Seller as 🏪 Penjual

    Buyer->>Payment: GET /marketplace/pembayaran
    Payment->>System: Redirect ke QRIS page
    System-->>Buyer: QR Code ditampilkan

    Note over Buyer: Buyer scan QR dan bayar

    Gateway->>System: POST /payments/webhook (callback)
    System->>DB: Update status_pembayaran = lunas
    System->>Notify: pembayaranDikonfirmasi(pesanan)
    Notify->>Seller: Notifikasi: "Pembayaran Terverifikasi!"

    Note over Seller: Penjual bisa proses pesanan
```

### 3d. Pembayaran Bayar di Tempat

```mermaid
sequenceDiagram
    autonumber
    actor Buyer as 👤 Pembeli
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    actor Seller as 🏪 Penjual

    Buyer->>System: GET /marketplace/pembayaran
    System-->>Buyer: Instruksi: "Bayar saat barang diterima"

    Note over Seller: Penjual langsung bisa proses pesanan
    Note over Buyer: Pembeli bayar saat terima barang
```

---

## 4. SEQUENCE DIAGRAM — Proses Penjual

```mermaid
sequenceDiagram
    autonumber
    actor Seller as 🏪 Penjual
    participant Lapak as 📦 Dashboard Lapak
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Buyer as 👤 Pembeli

    Seller->>Lapak: GET /anggota/lapak/pesanan
    Lapak-->>Seller: Daftar pesanan masuk

    Seller->>Lapak: Pilih pesanan, ubah status
    Lapak->>System: PATCH /anggota/lapak/pesanan/{id}/status
    System->>DB: Update pesanan.status

    alt Status: menunggu ke dikemas
        System->>Notify: Status Pesanan Diperbarui
        Notify->>Buyer: "Pesanan sedang dikemas"
    else Status: dikemas ke dikirim
        System->>Notify: Status Pesanan Diperbarui
        Notify->>Buyer: "Pesanan sedang dikirim"
    else Status: dibatalkan
        System->>DB: Restore stok produk
        System->>Notify: Status Pesanan Dibatalkan
        Notify->>Buyer: "Pesanan dibatalkan"
    end

    System-->>Lapak: Status updated
    Lapak-->>Seller: Dashboard refreshed
```

---

## 5. SEQUENCE DIAGRAM — Konfirmasi Penerimaan Pembeli

```mermaid
sequenceDiagram
    autonumber
    actor Buyer as 👤 Pembeli
    participant Orders as 📦 Pesanan Saya
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Seller as 🏪 Penjual
    actor Admin as 👨‍💼 Pengurus

    Buyer->>Orders: GET /pesanan-saya
    Orders-->>Buyer: Daftar pesanan

    Buyer->>Orders: Klik "Terima Pesanan"
    Orders->>System: PATCH /pesanan-saya/{id}/terima
    System->>DB: Update pesanan.status = selesai
    System->>DB: Update status_pembayaran = terverifikasi (jika belum)

    System->>Notify: pesananDiterima(pesanan)
    Notify->>Seller: Notifikasi: "Pesanan Selesai"
    Notify->>Admin: Notifikasi: "Pesanan Selesai"

    System-->>Orders: Status updated
    Orders-->>Buyer: Pesanan selesai
```

---

## 6. SEQUENCE DIAGRAM — Koperasi: Simpanan

```mermaid
sequenceDiagram
    autonumber
    actor Member as 👤 Anggota
    participant Simpanan as 🏦 Halaman Simpanan
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Admin as 👨‍💼 Pengurus

    Member->>Simpanan: GET /anggota/simpanan
    Simpanan-->>Member: Ringkasan simpanan

    Member->>Simpanan: Setor simpanan (form)
    Simpanan->>System: POST /anggota/transaksi/simpanan
    System->>DB: Create SetoranSimpanan (status: menunggu_verifikasi)
    System->>Notify: Setoran simpanan baru
    Notify->>Admin: Notifikasi: "Setoran Simpanan Baru"

    Admin->>System: POST /pengurus/transaksi/simpanan/{id}/approve
    System->>DB: Update status = terverifikasi
    System->>DB: Insert record ke tabel Simpanan (status: masuk)
    System->>Notify: Setoran terverifikasi
    Notify->>Member: Notifikasi: "Simpanan Terverifikasi"
```

---

## 7. SEQUENCE DIAGRAM — Koperasi: Pembiayaan

```mermaid
sequenceDiagram
    autonumber
    actor Member as 👤 Anggota
    participant Form as 📝 Form Pembiayaan
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Admin as 👨‍💼 Pengurus
    actor DPS_Auditor as ⚖️ DPS

    Member->>Form: GET /anggota/pembiayaan/ajukan
    Form-->>Member: Form pengajuan

    Member->>Form: Isi form + upload dokumen
    Form->>System: POST /anggota/pembiayaan
    System->>DB: BEGIN TRANSACTION
    System->>DB: Create Pembiayaan (status: diajukan)
    System->>DB: Create DetailAkad
    System->>DB: Create Jadwal Angsuran
    System->>DB: Store dokumen pengajuan
    System->>DB: COMMIT
    System->>Notify: pembiayaanDiajukan(pembiayaan)
    Notify->>Admin: Notifikasi: "Pengajuan Pembiayaan Baru"
    Notify->>DPS_Auditor: Notifikasi: "Pengajuan Perlu Review"

    DPS_Auditor->>System: Review akad & validasi syariah
    System->>DB: Create OpiniSyariah

    Admin->>System: Approve / Tolak pembiayaan
    System->>DB: Update Pembiayaan.status
    alt Disetujui
        System->>DB: Create jadwal angsuran (status: belum_bayar)
        System->>Notify: Pembiayaan disetujui
        Notify->>Member: Notifikasi: "Pembiayaan Disetujui!"
    else Ditolak
        System->>Notify: Pembiayaan ditolak
        Notify->>Member: Notifikasi: "Pembiayaan Ditolak"
    end
```

---

## 8. SEQUENCE DIAGRAM — Koperasi: Angsuran

```mermaid
sequenceDiagram
    autonumber
    actor Member as 👤 Anggota
    participant Angsuran as 📋 Halaman Angsuran
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Admin as 👨‍💼 Pengurus

    Member->>Angsuran: GET /anggota/angsuran
    Angsuran-->>Member: Daftar angsuran

    Member->>Angsuran: Bayar angsuran
    Angsuran->>System: POST /anggota/transaksi/angsuran
    System->>DB: Create transaksi angsuran (status: menunggu_verifikasi)
    System->>Notify: Pembayaran angsuran baru
    Notify->>Admin: Notifikasi: "Bukti Angsuran Baru"

    Admin->>System: Approve pembayaran angsuran
    System->>DB: Update Angsuran.status = dibayar
    System->>DB: Update sisa_pokok
    alt Semua angsuran lunas
        System->>DB: Update Pembiayaan.status = lunas
    end
    System->>Notify: Angsuran terverifikasi
    Notify->>Member: Notifikasi: "Angsuran Terverifikasi"
```

---

## 9. SEQUENCE DIAGRAM — Moderasi Produk (DPS)

```mermaid
sequenceDiagram
    autonumber
    actor Seller as 🏪 Penjual
    participant Lapak as 📦 Dashboard Lapak
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor DPS_Auditor as ⚖️ DPS

    Seller->>Lapak: Tambah produk baru
    Lapak->>System: POST /anggota/lapak/produk/tambah
    System->>DB: Create Produk (status: pending)
    System->>Notify: Produk baru perlu review
    Notify->>DPS_Auditor: Notifikasi: "Produk Perlu Review Syariah"

    DPS_Auditor->>System: GET /dps/produk/{id}
    System-->>DPS_Auditor: Detail produk + opini syariah

    DPS_Auditor->>System: POST /dps/produk/{id}/review
    System->>DB: Create OpiniSyariah
    alt Disetujui
        System->>DB: Update Produk.status = aktif
        System->>Notify: Produk Disetujui
        Notify->>Seller: Notifikasi: "Produk Disetujui!"
    else Perlu Revisi
        System->>DB: Update Produk.status = pending
        System->>Notify: Produk Perlu Revisi
        Notify->>Seller: Notifikasi: "Produk Perlu Revisi"
    else Ditolak
        System->>DB: Update Produk.status = nonaktif
        System->>Notify: Produk Ditolak
        Notify->>Seller: Notifikasi: "Produk Ditolak"
    end
```

---

## 10. SEQUENCE DIAGRAM — Verifikasi Toko (Pengurus)

```mermaid
sequenceDiagram
    autonumber
    actor Seller as 🏪 Anggota
    participant System as 🖥️ System
    participant DB as 🗄️ Database
    participant Notify as 🔔 Notification
    actor Admin as 👨‍💼 Pengurus

    Seller->>System: Buka toko baru (POST /anggota/lapak)
    System->>DB: Create Toko (status: menunggu_verifikasi)
    System->>Notify: Toko baru perlu verifikasi
    Notify->>Admin: Notifikasi: "Toko Baru Perlu Diverifikasi"

    Admin->>System: GET /pengurus/dashboard (tab marketplace)
    System-->>Admin: Daftar toko menunggu verifikasi

    alt Approve
        Admin->>System: POST /pengurus/marketplace/toko/{id}/approve
        System->>DB: Update Toko.status = aktif
        System->>Notify: Toko Diverifikasi
        Notify->>Seller: Notifikasi: "Toko Aktif!"
    else Reject
        Admin->>System: POST /pengurus/marketplace/toko/{id}/reject
        System->>DB: Update Toko.status = nonaktif
        System->>Notify: Toko Ditolak
        Notify->>Seller: Notifikasi: "Toko Ditolak"
    end
```

---

## 11. SEQUENCE DIAGRAM — Notifikasi Real-time

```mermaid
sequenceDiagram
    autonumber
    participant User as 👤 User Browser
    participant Inbox as 📬 Inbox Controller
    participant DB as 🗄️ Database
    participant Notify as 🔔 NotificationService

    Note over User: Polling setiap 15 detik

    loop Setiap 15 detik
        User->>Inbox: GET /inbox?ajax=1
        Inbox->>DB: Query InboxEntry (unread)
        DB-->>Inbox: Daftar notifikasi
        Inbox-->>User: JSON response (notifikasi baru)
    end

    User->>Inbox: POST /inbox/{id}/read
    Inbox->>DB: Update read_at = now()
    Inbox-->>User: 200 OK (AJAX)

    User->>Inbox: POST /inbox/read-all
    Inbox->>DB: Update semua read_at = now()
    Inbox-->>User: 200 OK (AJAX)
```

---

## 12. RINGKASAN ACTOR & USE CASE

| Actor | Use Case Utama |
|-------|---------------|
| **👤 Anggota** | Browse & beli produk, jual produk, kelola toko, kelola produk, proses pesanan, kelola simpanan, ajukan pembiayaan, bayar angsuran, lihat bagi hasil, monitoring keuangan |
| **🛒 Pelanggan** | Browse & beli produk, kelola keranjang, checkout, bayar, upload bukti transfer, konfirmasi terima |
| **👨‍💼 Pengurus** | Verifikasi toko, verifikasi transaksi, proses pencairan, mutasi kas, kelola rekening, monitoring marketplace |
| **⚖️ DPS** | Moderasi produk (review syariah), catat temuan audit, buat opini syariah, laporan pengawasan |
| **🔧 System** | Webhook pembayaran gateway, notifikasi real-time, autentikasi & otorisasi |

---

## 13. ALUR STATUS PESANAN

```mermaid
stateDiagram-v2
    [*] --> menunggu: Checkout berhasil
    menunggu --> dikemas: Penjual proses
    dikemas --> dikirim: Penjual kirim
    dikirim --> selesai: Pembeli terima
    menunggu --> batal: Dibatalkan
    dikemas --> batal: Dibatalkan

    state "Status Pembayaran" as SP {
        [*] --> menunggu_bayar: Checkout
        menunggu_bayar --> menunggu_konfirmasi: Upload bukti
        menunggu_konfirmasi --> terverifikasi: Penjual konfirmasi
        menunggu_bayar --> lunas: QRIS bayar
        menunggu_bayar --> terverifikasi: Bayar di tempat diterima
    }
```

---

## 14. ALUR STATUS PEMBIAYAAN

```mermaid
stateDiagram-v2
    [*] --> diajukan: Anggota ajukan
    diajukan --> disetujui: Pengurus approve
    diajukan --> ditolak: Pengurus tolak
    disetujui --> berjalan: Dana dicairkan
    berjalan --> lunas: Semua angsuran bayar

    note right of diajukan: DPS validasi akad syariah
    note right of berjalan: Anggota bayar angsuran bulanan
```

---

## 15. ACTIVITY DIAGRAM — Alur Pemesanan Marketplace (Pembeli)

```mermaid
flowchart TD
    Start([🛒 Mulai Belanja]) --> Browse["Browse Produk di Marketplace"]
    Browse --> Filter["Filter per Kategori"]
    Filter --> LihatDetail["Lihat Detail Produk"]

    LihatDetail --> PilihAksi{Pilih Aksi}

    PilihAksi -->|Tambah ke Keranjang| CekLogin{Sudah Login?}
    PilihAksi -->|Lihat Toko| LihatToko["Lihat Profil Toko"]
    PilihAksi -->|Wishlist| CekLogin2{Sudah Login?}

    CekLogin -->|Ya| TambahKeranjang["Produk ditambahkan ke keranjang"]
    CekLogin -->|Tabel redirect login| Login["Halaman Login"]
    Login --> Register{Sudah punya akun?}
    Register -->|Ya| Masuk["Masuk dengan email & password"]
    Register -->|Tabel| Daftar["Form Registrasi"]
    Daftar --> Verifikasi["Verifikasi Email"]
    Masuk --> TambahKeranjang
    Verifikasi --> TambahKeranjang

    CekLogin2 -->|Ya| SimpanWishlist["Disimpan ke Wishlist"]

    TambahKeranjang --> LanjutBelanja{Lanjut Belanja?}
    LanjutBelanja -->|Ya| Browse
    LanjutBelanja -->|Tabel ke Checkout| LihatKeranjang["Lihat Keranjang"]

    LihatKeranjang --> EditQty{Edit Jumlah?}
    EditQty -->|Ya| UpdateQty["Update Jumlah Item"]
    EditQty -->|Tabel| Checkout

    UpdateQty --> Checkout

    Checkout --> IsiForm["Isi Form Checkout:\n- Alamat Pengiriman\n- Metode Pengiriman\n- Metode Pembayaran"]
    IsiForm --> PilihPengiriman{Pilih Pengiriman}

    PilihPengiriman -->|Ambil di Toko| Ongkir0["Ongkir: Gratis"]
    PilihPengiriman -->|Diantar Penjual| Ongkir5["Ongkir: Rp 5.000"]
    PilihPengiriman -->|Kurir Lokal| Ongkir10["Ongkir: Rp 10.000"]

    Ongkir0 --> HitungTotal
    Ongkir5 --> HitungTotal
    Ongkir10 --> HitungTotal

    HitungTotal["Hitung Total Pembayaran"]
    HitungTotal --> Konfirmasi["Konfirmasi Checkout"]

    Konfirmasi --> CekStok{"Cek Stok Produk"}

    CekStok -->|Stok Cukup| BuatPesanan["Buat Pesanan per Toko\n(decrypt stok, snapshot harga)"]
    CekStok -->|Stok Habis| ErrorStok["Error: Stok Tidak Mencukupi"]
    ErrorStok --> LihatKeranjang

    BuatPesanan --> KirimNotif["Kirim Notifikasi:\n- Pembeli: Pesanan Berhasil\n- Penjual: Pesanan Baru\n- Pengurus: Monitoring"]

    KirimNotif --> HalamanBayar["Redirect ke Halaman Pembayaran"]

    HalamanBayar --> PilihBayar{Pilih Metode Bayar}

    PilihBayar -->|Transfer Manual| LihatRekening["Lihat No. Rekening BSI"]
    PilihBayar -->|QRIS| ScanQR["Scan QR Code"]
    PilihBayar -->|Bayar di Tempat| InstruksiCOD["Bayar saat terima barang"]

    LihatRekening --> Transfer["Transfer uang ke rekening"]
    Transfer --> UploadBukti["Upload Bukti Transfer"]
    UploadBukti --> MenungguKonfirmasi["Menunggu Konfirmasi Penjual"]
    MenungguKonfirmasi --> PenjualKonfirm{Penjual Konfirmasi?}
    PenjualKonfirm -->|Ya| PembayaranTerverifikasi["Pembayaran Terverifikasi"]
    PenjualKonfirm -->|Menunggu| MenungguKonfirmasi

    ScanQR --> Webhook["Payment Gateway Webhook"]
    Webhook --> PembayaranTerverifikasi

    InstruksiCOD --> ProsesPesanan

    PembayaranTerverifikasi --> ProsesPesanan["Penjual Proses Pesanan"]
    ProsesPesanan --> StatusDikemas["Status: Dikemas"]
    StatusDikemas --> StatusDikirim["Status: Dikirim"]
    StatusDikirim --> KonfirmasiTerima{Konfirmasi Terima?}
    KonfirmasiTerima -->|Ya| Selesai["Pesanan Selesai"]
    KonfirmasiTerima -->|Menunggu| StatusDikirim

    Selesai --> End([✅ Selesai])
```

---

## 16. ACTIVITY DIAGRAM — Alur Proses Pesanan (Penjual)

```mermaid
flowchart TD
    Start([📦 Pesanan Masuk]) --> Login["Login ke Dashboard Lapak"]
    Login --> LihatPesanan["Lihat Daftar Pesanan Masuk"]

    LihatPesanan --> PilihPesanan["Pilih Pesanan"]
    PilihPesanan --> CekBuktiTransfer{Ada Bukti Transfer?}

    CekBuktiTransfer -->|Ya| LihatBukti["Lihat Bukti Transfer"]
    CekBuktiTransfer -->|Tabel - Bayar di Tempat/QRIS| CekStatusBayar

    LihatBukti --> KonfirmasiBayar{Konfirmasi Pembayaran?}
    KonfirmasiBayar -->|Bayar Sesuai| SetTerverifikasi["Set status_pembayaran = terverifikasi"]
    KonfirmasiBayar -->|Tabel| TegurPembeli["Tegur Pembemi (Chat/Notifikasi)"]

    SetTerverifikasi --> KirimNotifBayar["Notifikasi ke Pembeli: Pembayaran Terverifikasi"]
    KirimNotifBayar --> CekStatusBayar
    TegurPembeli --> CekStatusBayar

    CekStatusBayar{Status Pembayaran Ready?}

    CekStatusBayar -->|Ya - terverifikasi/lunas/bayar di tempat| Proses
    CekStatusBayar -->|Tabel - menunggu| Tunggu["Menunggu Pembayaran"]
    Tunggu --> CekStatusBayar

    Proses["Mulai Proses Pesanan"]
    Proses --> UpdateStatus1["Update Status: Dikemas"]
    UpdateStatus1 --> NotifDikemas["Notifikasi ke Pembeli: Sedang Dikemas"]
    NotifDikemas --> KemasBarang["Kemas Barang"]

    KemasBarang --> UpdateStatus2["Update Status: Dikirim"]
    UpdateStatus2 --> NotifDikirim["Notifikasi ke Pembeli: Sedang Dikirim"]
    NotifDikirim --> TungguDiterima["Menunggu Konfirmasi Pembeli"]

    TungguDiterima --> CekStatus{Status Pesanan?}
    CekStatus -->|Diterima Pembeli| Selesai["Pesanan Selesai"]
    CekStatus -->|Dibatalkan| Batal["Pesanan Dibatalkan"]

    Selesai --> NotifSelesai["Notifikasi: Pesanan Selesai"]
    Batal --> RestoreStok["Kembalikan Stok Produk"]
    RestoreStok --> NotifBatal["Notifikasi: Pesanan Dibatalkan"]

    NotifSelesai --> End([✅ Selesai])
    NotifBatal --> End2([❌ Selesai])
```

---

## 17. ACTIVITY DIAGRAM — Alur Pembayaran (3 Metode)

```mermaid
flowchart TD
    Start([💳 Halaman Pembayaran]) --> PilihMetode{Pilih Metode Pembayaran}

    %% Transfer Manual
    PilihMetode -->|Transfer Manual| LihatRekening["Tampilkan No. Rekening BSI\n- Bank: BSI\n- No: 7123456789\n- Atas Nama: Koperasi"]
    LihatRekening --> CopyRekening["Tombol Copy No. Rekening"]
    CopyRekening --> Transfer["Pembeli Transfer Uang"]
    Transfer --> UploadBukti["Upload Bukkti Transfer\n(format: jpg/png, max 5MB)"]
    UploadBukti --> ValidasiFile{File Valid?}
    ValidasiFile -->|Ya| SimpanBukti["Simpan bukti_transfer\nstatus = menunggu_konfirmasi"]
    ValidasiFile -->|Tabel| UploadBukti
    SimpanBukti --> NotifPenjual["Notifikasi ke Penjual:\nBukti Transfer Diterima"]
    NotifPenjual --> TungguKonfirmasi["Menunggu Penjual Konfirmasi"]
    TungguKonfirmasi --> PenjualOk{Penjual Konfirmasi?}
    PenjualOk -->|Bayar Sesuai| Lunas["status_pembayaran = terverifikasi"]
    PenjualOk -->|Tabel - belum sesuai| UploadBukti

    %% QRIS
    PilihMetode -->|QRIS| TampilQR["Tampilkan QR Code\n(nominal sesuai total)"]
    TampilQR --> ScanQR["Pembeli Scan QR"]
    ScanQR --> Bayar["Pembeli Bayar via Mobile Banking"]
    Bayar --> Webhook["Payment Gateway Callback"]
    Webhook --> VerifikasiOtomatis["Verifikasi Otomatis"]
    VerifikasiOtomatis --> Lunas

    %% Bayar di Tempat
    PilihMetode -->|Bayar di Tempat| InstruksiCOD["Instruksi:\nBayar saat barang diterima\nTunai/Transfer"]
    InstruיצהCOD --> LangsungProses["Penjual Langsung Proses Pesanan"]
    LangsungProses --> TerimaBarang["Pembeli Terima Barang"]
    TerimaBarang --> BayarCOD["Bayar ke Penjual/Kurir"]
    BayarCOD --> Lunas

    %% Semua metode converge
    Lunas --> TampilkanStatus["Tampilkan Status: Pembayaran Terverifikasi"]
    TampilkanStatus --> End([✅ Pembayaran Selesai])
```

---

## 18. ACTIVITY DIAGRAM — Alur Pengajuan Pembiayaan

```mermaid
flowchart TD
    Start([📝 Ajukan Pembiayaan]) --> Login["Login sebagai Anggota"]
    Login --> CekToko{Punya Toko?}

    CekToko -->|Ya| PilihTujuan["Pilih Tujuan Pembiayaan"]
    CekToko -->|Tabel| BukaToko["Buka Toko Baru dulu"]

    BukaToko --> TungguVerifikasiToko["Menunggu Verifikasi Pengurus"]
    TungguVerifikasiToko --> TokoAktif["Toko Aktif"]
    TokoAktif --> PilihTujuan

    PilihTujuan --> PilihAkad["Pilih Jenis Akad:\n- Murabahah\n- Mudharabah\n- Musyarakah\n- Ijarah\n- Qardh"]

    PilihAkad --> IsiForm["Isi Form Pengajuan"]
    IsiForm --> UploadDokumen["Upload Formulir Pengajuan\n(PDF/DOC, max 8MB)"]
    UploadDokumen --> Validasi["Validasi Form"]

    Validasi --> CekValid{Valid?}
    CekValid -->|Tabel| IsiForm
    CekValid -->|Ya| SimpanData["Simpan ke Database:\n- Pembiayaan (status: diajukan)\n- DetailAkad\n- Jadwal Angsuran\n- Dokumen"]

    SimpanData --> NotifAll["Notifikasi ke:\n- Pengurus: Pengajuan Baru\n- DPS: Perlu Review Syariah"]

    NotifAll --> DPSReview["DPS Review Akad & Syariah"]
    DPSReview --> BuatOpini["DPS Buat Opini Syariah"]
    BuatOpini --> PengurusReview["Pengurus Review & Validasi"]

    PengurusReview --> Keputusan{Keputusan?}
    Keputusan -->|Disetujui| Setuju["Status: Disetujui"]
    Keputusan -->|Ditolak| Tolak["Status: Ditolak"]

    Setuju --> NotifSetuju["Notifikasi: Pembiayaan Disetujui"]
    Tolak --> NotifTolak["Notifikasi: Pembiayaan Ditolak"]

    NotifSetuju --> CairkanDana["Dana Dicairkan"]
    CairkanDana --> StatusBerjalan["Status: Berjalan"]
    StatusBerjalan --> BayarAngsuran["Bayar Angsuran Bulanan"]

    BayarAngsuran --> CekLunas{Semua Lunas?}
    CekLunas -->|Tabel| BayarAngsuran
    CekLunas -->|Ya| Lunas["Status: Lunas"]

    Lunas --> End([✅ Pembiayaan Selesai])
    NotifTolak --> End2([❌ Ditolak])
```

---

## 19. ACTIVITY DIAGRAM — Alur Setor Simpanan

```mermaid
flowchart TD
    Start([🏦 Setor Simpanan]) --> Login["Login sebagai Anggota"]
    Login --> LihatSimpanan["Lihat Ringkasan Simpanan"]
    LihatSimpanan --> PilihJenis["Pilih Jenis Simpanan:\n- Pokok\n- Wajib\n- Sukarela\n- Mudharabah"]

    PilihJenis --> IsiNominal["Isi Nominal Setoran"]
    IsiNominal --> UploadBukti["Upload Bukkti Transfer"]
    UploadBukti --> Konfirmasi["Konfirmasi Setoran"]

    Konfirmasi --> Simpan["Simpan ke Database:\nSetoranSimpanan\n(status: menunggu_verifikasi)"]
    Simpan --> Notif["Notifikasi ke Pengurus:\nSetoran Simpanan Baru"]

    Notif --> Tunggu["Menunggu Verifikasi Pengurus"]
    Tunggu --> Pengurus{Pengurus Verifikasi?}

    Pengurus -->|Approve| Terverifikasi["Status: Terverifikasi"]
    Pengurus -->|Tolak| Ditolak["Status: Ditolak"]

    Terverifikasi --> UpdateSimpanan["Insert ke Tabel Simpanan\n(status: masuk)"]
    UpdateSimpanan --> NotifAnggota["Notifikasi ke Anggota:\nSimpanan Terverifikasi"]
    NotifAnggota --> End([✅ Selesai])

    Ditolak --> NotifTolak["Notifikasi ke Anggota:\nSetoran Ditolak"]
    NotifTolak --> End2([❌ Ditolak])
```

---

## 20. ACTIVITY DIAGRAM — Alur Bayar Angsuran

```mermaid
flowchart TD
    Start([💰 Bayar Angsuran]) --> Login["Login sebagai Anggota"]
    Login --> LihatAngsuran["Lihat Daftar Angsuran"]
    LihatAngsuran --> PilihAngsuran["Pilih Angsuran yang Belum Dibayar"]

    PilihAngsuran --> CekJatuhTempo{Jatuh Tempo?}
    CekJatuhTempo -->|Sudah Jatuh Tempo| Warning["Peringatan: Angsuran Terlambat"]
    CekJatuhTempo -->|Masih Waktu| Lanjut

    Warning --> Lanjut["Lanjut ke Pembayaran"]
    Lanjut --> PilihMetode["Pilih Metode Bayar:\n- Transfer Manual\n- QRIS"]

    PilihMetode -->|Transfer Manual| FormTransfer["Form Upload Bukti Transfer"]
    PilihMetode -->|QRIS| BayarQR["Bayar via QR Code"]

    FormTransfer --> SubmitBukti["Submit Bukkti Transfer"]
    SubmitBukti --> NotifAdmin["Notifikasi ke Pengurus:\nBukti Angsuran Baru"]
    BayarQR --> VerifikasiOtomatis["Verifikasi Otomatis"]

    NotifAdmin --> TungguAdmin["Menunggu Verifikasi Pengurus"]
    TungguAdmin --> Admin{Pengurus Approve?}

    Admin -->|Approve| UpdateAngsuran["Update Angsuran.status = dibayar\nUpdate sisa_pokok"]
    Admin -->|Tolak| Kembali["Kembali ke Form"]
    Kembali --> FormTransfer

    UpdateAngsuran --> CekLunas{Semua Angsuran Lunas?}
    CekLunas -->|Tabel| End([✅ Angsuran Dibayar])
    CekLunas -->|Ya| PembiayaanLunas["Update Pembiayaan.status = lunas"]
    VerifikasiOtomatis --> UpdateAngsuran
    PembiayaanLunas --> End2([🎉 Pembiayaan Lunas])
```

---

## 21. ACTIVITY DIAGRAM — Alur Moderasi Produk (DPS)

```mermaid
flowchart TD
    Start([⚖️ Moderasi Produk]) --> Login["Login sebagai DPS"]
    Login --> LihatDaftar["Lihat Daftar Produk Pending"]
    LihatDaftar --> Filter["Filter: Status, Search"]
    Filter --> PilihProduk["Pilih Produk untuk Direview"]

    PilihProduk --> LihatDetail["Lihat Detail Produk:\n- Nama, Harga, Stok\n- Deskripsi, Foto\n- Toko & Kategori"]
    LihatDetail --> CekOpini{Ada Opini Sebelumnya?}

    CekOpini -->|Ya| LihatOpini["Lihat Opini Syariah Sebelumnya"]
    CekOpini -->|Tabel| Review

    LihatOpini --> Review["Review Produk"]

    Review --> Keputusan{Keputusan Syariah?}
    Keputusan -->|Disetujui| Setuju["Buat Opini: Disetujui\n(status produk = aktif)"]
    Keputusan -->|Perlu Revisi| Revisi["Buat Opini: Perlu Revisi\n(status produk = pending)"]
    Keputusan -->|Ditolak| Tolak["Buat Opini: Ditolak\n(status produk = nonaktif)"]

    Setuju --> SimpanOpini["Simpan OpiniSyariah"]
    Revisi --> SimpanOpini
    Tolak --> SimpanOpini

    SimpanOpini --> Notif["Notifikasi ke Penjual:\nHasil Review Produk"]
    Notif --> End([✅ Review Selesai])
```

---

## 22. ACTIVITY DIAGRAM — Alur Verifikasi Toko (Pengurus)

```mermaid
flowchart TD
    Start([👨‍💼 Verifikasi Toko]) --> Login["Login sebagai Pengurus"]
    Login --> Dashboard["Buka Dashboard - Tab Marketplace"]
    Dashboard --> LihatToko["Lihat Daftar Toko Menunggu Verifikasi"]

    LihatToko --> PilihToko["Pilih Toko"]
    PilihToko --> LihatDetail["Lihat Detail Toko:\n- Nama Toko\n- Pemilik\n- Kategori\n- Status Saat Ini"]

    LihatDetail --> Keputusan{Keputusan?}
    Keputusan -->|Approve| Aktifkan["Update Toko.status = aktif"]
    Keputusan -->|Reject| Nonaktifkan["Update Toko.status = nonaktif"]

    Aktifkan --> NotifAktif["Notifikasi ke Pemilik:\nToko Aktif! Bisa Jualan"]
    Nonaktifkan --> NotifNonaktif["Notifikasi ke Pemilik:\nToko Ditolak"]

    NotifAktif --> End([✅ Toko Diverifikasi])
    NotifNonaktif --> End2([❌ Toko Ditolak])
```

---

## 23. ACTIVITY DIAGRAM — Alur Autentikasi & Otorisasi

```mermaid
flowchart TD
    Start([🔐 Akses Aplikasi]) --> CekLogin{Sudah Login?}

    CekLogin -->|Tabel| HalamanLogin["Halaman Login"]
    HalamanLogin --> InputCredentials["Input Email & Password"]
    InputCredentials --> Validasi{Valid?}

    Validasi -->|Tabel| LoginError["Error: Email/Password Salah"]
    LoginError --> HalamanLogin

    Validasi -->|Ya| CekVerifikasi{Email Terverifikasi?}
    CekVerifikasi -->|Tabel| BelumVerifikasi["Halaman Verifikasi Email"]
    BelumVerifikasi --> KirimEmail["Kirim Email Verifikasi"]
    KirimEmail --> KlikLink["Klik Link Verifikasi"]
    KlikLink --> CekRole

    CekVerifikasi -->|Ya| CekRole{Role User?}

    CekLogin -->|Ya| CekRole

    CekRole -->|admin| AdminDash["Dashboard Admin"]
    CekRole -->|pengurus| PengurusDash["Dashboard Pengurus"]
    CekRole -->|anggota| AnggotaDash["Dashboard Anggota"]
    CekRole -->|pelanggan| PelangganDash["Dashboard Pelanggan"]
    CekRole -->|dps| DPSDash["Dashboard DPS"]

    AnggotaDash --> CekToko{Punya Toko?}
    CekToko -->|Ya| TabPenjual["Tab: Pesanan Masuk, Toko Saya, Produk"]
    CekToko -->|Tabel| TabAnggota["Tab: Simpanan, Pembiayaan, Angsuran"]

    TabPenjual --> Marketplace["Marketplace Features"]
    TabAnggota --> Koperasi["Koperasi Features"]

    Marketplace --> End([✅ Dashboard Loaded])
    Koperasi --> End
    AdminDash --> End
    PengurusDash --> End
    PelangganDash --> End
    DPSDash --> End
```

---

*Diagram ini dibuat menggunakan Mermaid syntax. Untuk melihat visualisasinya, gunakan:*
- *VS Code dengan extension "Mermaid Preview"*
- *GitHub/GitLab markdown viewer*
- *Online: [mermaid.live](https://mermaid.live)*
