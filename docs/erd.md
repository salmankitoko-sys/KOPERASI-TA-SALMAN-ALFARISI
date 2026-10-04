# 🗄️ ERD (Entity Relationship Diagram) — Sistem Koperasi Marketplace

---

## 1. OVERVIEW — Semua Entitas & Relasi

```mermaid
erDiagram
    users ||--o{ tokos : "punya toko"
    users ||--o{ simpanan : "miliki simpanan"
    users ||--o{ setoran_simpanan : "setor simpanan"
    users ||--o{ pembiayaan : "ajukan pembiayaan"
    users ||--o{ bagi_hasil : "terima bagi hasil"
    users ||--o{ pesanan : "beli sebagai pembeli"
    users ||--o{ skor_kredit : "miliki skor"
    users ||--o{ pengajuan_modal : "ajukan modal"
    users ||--o{ inbox_entries : "terima notifikasi"
    users ||--o{ payments : "lakukan pembayaran"
    users ||--o{ notifications : "terima notifikasi"
    users ||--o{ opini_syariah : "buat opini syariah"
    users ||--o{ audit_temuan : "buat/verifikasi temuan"
    users ||--o{ laporan_pengawasan : "buat laporan"
    users ||--o{ pencairan_dana : "proses pencairan"
    users ||--o{ pembayaran_angsuran : "verifikasi angsuran"
    users ||--o{ setoran_simpanan : "verifikasi setoran"
    users ||--o{ validasi_akad_dps : "validasi akad"

    tokos ||--o{ produk : "jual produk"
    tokos ||--o{ pesanan : "terima pesanan"
    tokos ||--o{ pembiayaan : "ajukan modal usaha"

    produk ||--o{ pesanan_item : "dipesan"
    produk ||--o{ audit_temuan : "diaudit"

    pesanan ||--o{ pesanan_item : "berisi item"
    pesanan ||--o{ payments : "bayar pesanan"

    pembiayaan ||--|| detail_akad : "punya detail akad"
    pembiayaan ||--o{ angsuran : "jadwal angsuran"
    pembiayaan ||--o{ pencairan_dana : "pencairan dana"
    pembiayaan ||--o{ pembayaran_angsuran : "bayar angsuran"
    pembiayaan ||--o{ pembiayaan_dokumen : "lampiran dokumen"
    pembiayaan ||--o{ opini_syariah : "review syariah"
    pembiayaan ||--o{ audit_temuan : "diaudit"
    pembiayaan ||--o{ validasi_akad_dps : "divalidasi DPS"

    angsuran ||--o{ pembayaran_angsuran : "dibayar"

    rekening_koperasi ||--o{ pencairan_dana : "rekening tujuan"

    payments }o--|| users : "milik user"
    payments }o--o{ pembayaran_angsuran : "terkait angsuran"
    payments }o--o{ setoran_simpanan : "terkait setoran"
    payments }o--o{ pesanan : "terkait pesanan"
```

---

## 2. ERD — Domain User & Autentikasi

```mermaid
erDiagram
    users {
        bigint id PK
        varchar name
        varchar email UK
        timestamp email_verified_at
        varchar password
        varchar role "admin|pengurus|anggota|pelanggan|dps"
        varchar no_hp
        date tgl_lahir
        varchar alamat
        varchar status_keanggotaan "aktif|nonaktif|suspend"
        varchar status_akun "aktif|nonaktif|belum_verifikasi"
        varchar foto_profil
        timestamp created_at
        timestamp updated_at
    }

    sessions {
        varchar id PK
        bigint user_id FK
        varchar ip_address
        text user_agent
        longtext payload
        int last_activity
    }

    password_reset_tokens {
        varchar email PK
        varchar token
        timestamp created_at
    }

    users ||--o{ sessions : "active sessions"
    users ||--o{ password_reset_tokens : "reset token"
```

---

## 3. ERD — Domain Marketplace

```mermaid
erDiagram
    tokos {
        bigint id PK
        bigint user_id FK
        varchar nama_toko
        varchar slug UK
        text deskripsi
        varchar kategori
        enum status "pending|aktif|nonaktif"
        decimal rating_rata
        int jumlah_ulasan
        timestamp created_at
        timestamp updated_at
    }

    produk {
        bigint id PK
        bigint toko_id FK
        varchar nama
        varchar slug UK
        text deskripsi
        varchar kategori
        decimal harga
        int stok
        enum akad "murabahah|salam|istishna"
        enum status "pending|aktif|nonaktif|habis"
        varchar foto_url
        timestamp created_at
        timestamp updated_at
    }

    pesanan {
        bigint id PK
        bigint pembeli_id FK "users.id"
        bigint toko_id FK
        varchar nomor_pesanan UK
        enum status "menunggu|dikemas|dikirim|selesai|batal"
        enum status_pembayaran "menunggu|menunggu_verifikasi|terverifikasi|gagal|lunas"
        enum akad "murabahah|salam|istishna"
        decimal total
        decimal biaya_pengiriman
        varchar metode_pengiriman
        varchar metode_pembayaran
        text alamat_kirim
        text catatan_pembeli
        varchar bukti_transfer
        timestamp created_at
        timestamp updated_at
    }

    pesanan_item {
        bigint id PK
        bigint pesanan_id FK
        bigint produk_id FK
        varchar nama_produk_snapshot
        decimal harga_satuan
        int qty
        decimal subtotal
        timestamp created_at
        timestamp updated_at
    }

    payments {
        bigint id PK
        varchar payment_code UK
        bigint user_id FK
        varchar type "angsuran|simpanan|marketplace"
        bigint payable_id
        decimal amount
        decimal fee
        decimal total_amount
        varchar payment_method "qris|transfer_manual"
        varchar gateway "midtrans|xendit|doku"
        varchar gateway_reference
        varchar external_id UK
        text qr_string
        varchar qr_url
        enum status "pending|paid|failed|expired|cancelled"
        timestamp paid_at
        timestamp expired_at
        json gateway_response
        timestamp webhook_received_at
        text notes
        timestamp created_at
        timestamp updated_at
    }

    users ||--o{ tokos : "pemilik toko"
    tokos ||--o{ produk : "jual produk"
    tokos ||--o{ pesanan : "terima pesanan"
    users ||--o{ pesanan : "beli sebagai pembeli"
    pesanan ||--o{ pesanan_item : "berisi item"
    produk ||--o{ pesanan_item : "dipesan"
    users ||--o{ payments : "lakukan pembayaran"
    pesanan ||--o{ payments : "bayar pesanan"
```

---

## 4. ERD — Domain Koperasi (Simpanan & Pembiayaan)

```mermaid
erDiagram
    simpanan {
        bigint id PK
        bigint user_id FK
        enum jenis "pokok|wajib|sukarela|mudharabah"
        decimal jumlah
        varchar keterangan
        enum status "masuk|pending"
        date tanggal
        timestamp created_at
        timestamp updated_at
    }

    setoran_simpanan {
        bigint id PK
        bigint user_id FK
        enum jenis_simpanan "pokok|wajib|sukarela|mudharabah"
        decimal nominal
        varchar bukti_transfer
        date tanggal_setor
        enum status "menunggu_verifikasi|diverifikasi|ditolak"
        bigint diverifikasi_oleh FK "users.id"
        decimal saldo_setelah
        text catatan_penolakan
        bigint payment_id FK
        timestamp created_at
        timestamp updated_at
    }

    bagi_hasil {
        bigint id PK
        bigint user_id FK
        date periode UK
        decimal saldo_rata_rata
        decimal nisbah_persen
        decimal jumlah_diterima
        enum status "proses|diterima"
        timestamp created_at
        timestamp updated_at
    }

    skor_kredit {
        bigint id PK
        bigint user_id FK
        decimal skor
        varchar factor_json
        date tanggal
        timestamp created_at
    }

    pengajuan_modal {
        bigint id PK
        bigint user_id FK
        bigint toko_id FK
        decimal jumlah
        text alasan
        enum status "diajukan|disetujui|ditolak"
        timestamp created_at
        timestamp updated_at
    }

    pembiayaan {
        bigint id PK
        bigint user_id FK
        bigint toko_id FK "nullable"
        varchar kode UK
        enum akad "murabahah|mudharabah|musyarakah|ijarah|qardh"
        varchar tujuan_pembiayaan
        varchar objek_pembiayaan
        text rencana_penggunaan_dana
        decimal estimasi_omzet_usaha
        decimal jumlah_pembiayaan
        int tenor
        decimal angsuran_bulanan
        date tanggal_pengajuan
        date tanggal_persetujuan
        enum status "diajukan|disetujui|ditolak|berjalan|lunas"
        timestamp created_at
        timestamp updated_at
    }

    detail_akad {
        bigint id PK
        bigint pembiayaan_id FK
        varchar objek
        int tenor
        decimal harga_beli
        decimal harga_jual
        decimal margin_persen
        decimal margin
        decimal dp
        decimal biaya_admin
        decimal angsuran_bulanan
        decimal modal
        decimal modal_anggota
        decimal porsi_modal_koperasi
        decimal porsi_modal_anggota
        decimal nisbah_koperasi
        decimal nisbah_anggota
        decimal estimasi_omzet
        decimal estimasi_biaya
        decimal estimasi_laba
        decimal bagi_hasil_koperasi
        decimal bagi_hasil_anggota
        decimal nilai_aset
        decimal ujrah_bulanan
        decimal biaya_perawatan
        decimal opsi_beli
        decimal total_pembayaran
        timestamp created_at
        timestamp updated_at
    }

    angsuran {
        bigint id PK
        bigint pembiayaan_id FK
        int bulan_ke
        date jatuh_tempo
        decimal jumlah_bayar
        decimal pokok
        decimal margin
        decimal sisa_pokok
        enum status "belum_bayar|dibayar"
        date tanggal_bayar
        timestamp created_at
        timestamp updated_at
    }

    pencairan_dana {
        bigint id PK
        bigint pembiayaan_id FK
        decimal nominal_pencairan
        varchar bank_tujuan
        varchar no_rekening_tujuan
        varchar nama_pemilik_rekening
        varchar bukti_transfer
        date tanggal_pencairan
        bigint dicairkan_oleh FK "users.id"
        enum status "menunggu|diproses|selesai|ditolak"
        text catatan
        varchar status_disbursement "pending|processing|completed|failed"
        timestamp disbursed_at
        varchar gateway_disbursement_id
        timestamp created_at
        timestamp updated_at
    }

    pembayaran_angsuran {
        bigint id PK
        bigint pembiayaan_id FK
        bigint angsuran_id FK
        decimal jumlah_dibayar
        date tanggal_bayar
        varchar bukti_transfer
        enum status "menunggu_verifikasi|diverifikasi|ditolak"
        bigint diverifikasi_oleh FK "users.id"
        date tanggal_verifikasi
        text catatan_penolakan
        text catatan
        bigint payment_id FK
        timestamp created_at
        timestamp updated_at
    }

    pembiayaan_dokumen {
        bigint id PK
        bigint pembiayaan_id FK
        varchar jenis "formulir_pengajuan|ktp|slip_gaji| dll"
        varchar nama_asli
        varchar path
        varchar mime_type
        bigint ukuran
        timestamp created_at
        timestamp updated_at
    }

    rekening_koperasi {
        bigint id PK
        varchar nama_bank
        varchar nomor_rekening
        varchar atas_nama
        varchar kode_rekening
        boolean is_aktif
        timestamp created_at
        timestamp updated_at
    }

    users ||--o{ simpanan : "miliki simpanan"
    users ||--o{ setoran_simpanan : "setor simpanan"
    users ||--o{ bagi_hasil : "terima bagi hasil"
    users ||--o{ skor_kredit : "miliki skor"
    users ||--o{ pengajuan_modal : "ajukan modal"
    users ||--o{ pembiayaan : "ajukan pembiayaan"
    tokos ||--o{ pembiayaan : "modal usaha toko"
    pembiayaan ||--|| detail_akad : "punya detail akad"
    pembiayaan ||--o{ angsuran : "jadwal angsuran"
    pembiayaan ||--o{ pencairan_dana : "pencairan dana"
    pembiayaan ||--o{ pembayaran_angsuran : "bayar angsuran"
    pembiayaan ||--o{ pembiayaan_dokumen : "lampiran dokumen"
    angsuran ||--o{ pembayaran_angsuran : "dibayar"
    rekening_koperasi ||--o{ pencairan_dana : "rekening tujuan"
```

---

## 5. ERD — Domain Pengawasan (DPS)

```mermaid
erDiagram
    opini_syariah {
        bigint id PK
        varchar nomor_opini UK
        enum jenis_objek "pembiayaan|produk"
        bigint objek_id
        enum hasil "Disetujui|Perlu Revisi|Ditolak"
        text catatan
        bigint ditandatangani_oleh FK "users.id"
        date tanggal_opini
        timestamp created_at
        timestamp updated_at
    }

    audit_temuan {
        bigint id PK
        varchar nomor_temuan UK
        bigint pembiayaan_id FK "nullable"
        bigint produk_id FK "nullable"
        varchar jenis_temuan
        varchar kategori
        enum tingkat_resiko "Rendah|Sedang|Tinggi|Kritis"
        text deskripsi
        text rekomendasi
        enum status "Dibuka|Ditindaklanjuti|Diverifikasi|Ditutup"
        bigint dibuat_oleh FK "users.id"
        bigint diverifikasi_oleh FK "users.id"
        date tanggal_temuan
        date tanggal_penutupan
        timestamp created_at
        timestamp updated_at
    }

    laporan_pengawasan {
        bigint id PK
        enum periode "Semester|Tahunan"
        varchar semester
        text ringkasan
        text rekomendasi
        varchar file_laporan
        date tanggal_publikasi
        enum status "Draf|Terbit"
        bigint dibuat_oleh FK "users.id"
        timestamp created_at
        timestamp updated_at
    }

    validasi_akad_dps {
        bigint id PK
        bigint pembiayaan_id FK
        bigint validator_id FK "users.id"
        varchar nomor_validasi UK
        enum hasil "Disetujui|Ditolak|Perlu Revisi"
        text catatan
        date tanggal_validasi
        timestamp created_at
        timestamp updated_at
    }

    pembiayaan ||--o{ opini_syariah : "review syariah"
    produk ||--o{ opini_syariah : "review syariah"
    pembiayaan ||--o{ audit_temuan : "diaudit"
    produk ||--o{ audit_temuan : "diaudit"
    users ||--o{ audit_temuan : "buat temuan"
    users ||--o{ laporan_pengawasan : "buat laporan"
    pembiayaan ||--o{ validasi_akad_dps : "divalidasi"
    users ||--o{ validasi_akad_dps : "sebagai validator"
```

---

## 6. ERD — Domain Notifikasi & Logging

```mermaid
erDiagram
    inbox_entries {
        bigint id PK
        bigint user_id FK
        varchar title
        text message
        json data
        boolean is_read
        timestamp created_at
        timestamp updated_at
    }

    notifications {
        uuid id PK
        varchar type
        varchar notifiable_type
        bigint notifiable_id
        text data
        timestamp read_at
        timestamp created_at
        timestamp updated_at
    }

    webhook_logs {
        bigint id PK
        varchar type "payment|disbursement"
        varchar gateway
        varchar external_id
        varchar order_id
        varchar ip_address
        boolean is_valid
        text error_message
        json payload
        json verified_data
        timestamp created_at
        timestamp updated_at
    }

    users ||--o{ inbox_entries : "terima inbox"
    users ||--o{ notifications : "terima notifikasi"
```

---

## 7. RINGKASAN SEMUA TABEL

| # | Tabel | Domain | Deskripsi |
|---|-------|--------|-----------|
| 1 | **users** | User | Data user (anggota, pelanggan, pengurus, admin, DPS) |
| 2 | **sessions** | Auth | Sesi login aktif |
| 3 | **password_reset_tokens** | Auth | Token reset password |
| 4 | **tokos** | Marketplace | Toko milik anggota |
| 5 | **produk** | Marketplace | Produk yang dijual di toko |
| 6 | **pesanan** | Marketplace | Pesanan dari pembeli ke toko |
| 7 | **pesanan_item** | Marketplace | Item detail dalam pesanan |
| 8 | **payments** | Pembayaran | Record pembayaran (QRIS, transfer, dll) |
| 9 | **simpanan** | Koperasi | Record simpanan anggota (pokok/wajib/sukarela/mudharabah) |
| 10 | **setoran_simpanan** | Koperasi | Setoran simpanan menunggu verifikasi |
| 11 | **bagi_hasil** | Koperasi | Bagi hasil simpanan mudharabah |
| 12 | **skor_kredit** | Koperasi | Skor kredit anggota |
| 13 | **pengajuan_modal** | Koperasi | Pengajuan modal usaha lama |
| 14 | **pembiayaan** | Koperasi | Pengajuan pembiayaan (murabahah/mudharabah/musyarakah/ijarah/qardh) |
| 15 | **detail_akad** | Koperasi | Detail spesifik akad pembiayaan |
| 16 | **angsuran** | Koperasi | Jadwal angsuran pembiayaan |
| 17 | **pencairan_dana** | Koperasi | Pencairan dana pembiayaan |
| 18 | **pembayaran_angsuran** | Koperasi | Pembayaran angsuran oleh anggota |
| 19 | **pembiayaan_dokumen** | Koperasi | Dokumen lampiran pengajuan pembiayaan |
| 20 | **rekening_koperasi** | Koperasi | Rekening bank koperasi |
| 21 | **opini_syariah** | DPS | Opini syariah dari DPS |
| 22 | **audit_temuan** | DPS | Temuan audit DPS |
| 23 | **laporan_pengawasan** | DPS | Laporan pengawasan DPS |
| 24 | **validasi_akad_dps** | DPS | Validasi akad oleh DPS |
| 25 | **inbox_entries** | Notifikasi | Inbox notifikasi user |
| 26 | **notifications** | Notifikasi | Notifikasi Laravel (database driver) |
| 27 | **webhook_logs** | Logging | Log webhook dari payment gateway |

---

## 8. RELASI UTAMA (Simplified)

```
┌─────────────────────────────────────────────────────────────────┐
│                        DOMAIN USER                              │
│  users ──┬── sessions                                           │
│          ├── password_reset_tokens                              │
│          ──┴── (1 user bisa punya banyak data di semua domain)  │
└───────────────────────┬─────────────────────────────────────────┘
                        │
        ┌───────────────┼───────────────────┐
        │               │                   │
        ▼               ▼                   ▼
┌───────────────┐ ┌───────────────┐ ┌────────────────┐
│  MARKETPLACE  │ │   KOPERASI    │ │   DPS/_AUDIT   │
│               │ │               │ │                │
│ users ── tokos│ │ users ── simpanan│ │ users ── opini_syariah│
│   tokos ── produk│ │ users ── setoran_simpanan│ │ users ── audit_temuan│
│   tokos ── pesanan│ │ users ── pembiayaan│ │ users ── laporan_pengawasan│
│   users ── pesanan│ │   pembiayaan ── detail_akad│ │ users ── validasi_akad_dps│
│   pesanan ── pesanan_item│ │   pembiayaan ── angsuran│ │                │
│   produk ── pesanan_item│ │   pembiayaan ── pencairan_dana│ │                │
│   users ── payments│ │   pembiayaan ── pembiayaan_dokumen│ │                │
│   pesanan ── payments│ │   angsuran ── pembayaran_angsuran│ │                │
│               │ │ users ── bagi_hasil│ │                │
│               │ │ users ── skor_kredit│ │                │
│               │ │ rekening_koperasi ── pencairan_dana│ │                │
└───────┬───────┘ └───────┬───────┘ └────────────────┘
        │                 │
        └────────┬────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│              NOTIFIKASI                 │
│  users ── inbox_entries                │
│  users ── notifications (polymorphic)  │
│  payments ── webhook_logs              │
└─────────────────────────────────────────┘
```

---

## 9. INDEKS PERFORMA

| Tabel | Indeks | Tujuan |
|-------|--------|--------|
| **produk** | (toko_id, status) | Query produk per toko |
| **pesanan** | (pembeli_id, status) | Query pesanan pembeli |
| **pesanan** | (toko_id, status) | Query pesanan penjual |
| **simpanan** | (user_id, jenis) | Query simpanan per jenis |
| **bagi_hasil** | (user_id, periode) unique | Cek duplikasi bagi hasil |
| **payments** | (user_id, status) | Query pembayaran user |
| **payments** | (type, payable_id) | Cek duplikasi pembayaran |
| **payments** | (status, expired_at) | Cleanup expired |
| **inbox_entries** | (user_id), (is_read) | Query inbox cepat |
| **audit_temuan** | (status, tingkat_resiko) | Filter temuan |
| **laporan_pengawasan** | (periode, status) | Filter laporan |
| **opini_syariah** | (jenis_objek, objek_id) | Cari opini objek |
| **webhook_logs** | (external_id, type) | Trace webhook |

---

*Diagram ini dibuat menggunakan Mermaid syntax. Untuk melihat visualisasinya, gunakan:*
- *VS Code dengan extension "Mermaid Preview"*
- *GitHub/GitLab markdown viewer*
- *Online: [mermaid.live](https://mermaid.live)*
