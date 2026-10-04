# Flowchart Role Admin

```mermaid
flowchart TD
    A[Start] --> B{User Login}
    B -->|Sukses| C{Cek Role}
    B -->|Gagal| B
    C -->|admin| D[Admin Dashboard]
    C -->|bukan admin| Z[Redirect ke Dashboard Role Masing-masing]

    D --> E{Pilih Menu}

    E -->|Dashboard| F[Admin Dashboard]
    F --> F1[Total Users per Role]
    F --> F2[Active/Inactive Users]
    F --> F3[Users perlu Perhatian]
    F --> F4[New Users Bulan Ini]
    F --> F5[6 User Terbaru]
    F1 --> E
    F2 --> E
    F3 --> E
    F4 --> E
    F5 --> E

    E -->|User Management| G[Daftar Semua Users]
    G --> G1[Pencarian Users]
    G --> G2[Filter by Role]
    G --> G3[Filter by Status]
    G1 --> G4[Paginasi 15 item/halaman]
    G2 --> G4
    G3 --> G4
    G4 --> G5{Pilih User}
    G5 --> H[Detail User]
    H --> H1[Info Profil User]
    H --> H2[Statistik Simpanan]
    H --> H3[Statistik Pembiayaan]
    H --> H4[Statistik Pesanan]
    H --> H5[5 Pesanan Terakhir]
    H --> H6[5 Pembiayaan Terakhir]
    H5 --> E
    H6 --> E
    G5 -->|Hapus User| I{Konfirmasi Hapus}
    I -->|Bukan Admin Sendiri| J[Hapus User + Session + Notifikasi]
    J --> K[Redirect ke Daftar Users]
    I -->|Admin Sendiri| L[Error: Tidak bisa hapus akun sendiri]
    L --> G

    E -->|Export Users| M[Export CSV]
    M --> M1[Filter sesuai pencarian]
    M --> M2[Generate CSV]
    M1 --> M3[Download File CSV]
    M2 --> M3
    M3 --> E

    E -->|Notifikasi| N[Halaman Notifikasi]
    N --> N1[Database Notifications]
    N --> N2[Inbox Entries Custom]
    N --> N3[System Stats]
    N3 --> N3a[Pending DPS Validation]
    N3 --> N3b[Pending Toko Verification]
    N3 --> N3c[Pending Produk Verification]
    N3 --> N3d[New Users 7 Hari]
    N1 --> N4{Pilih Notifikasi}
    N2 --> N5{Pilih Inbox Entry}
    N4 -->|Tandai Dibaca| N6[Mark as Read]
    N6 --> N
    N5 -->|Tandai Dibaca| N7[Mark as Read]
    N7 --> N
    N -->|Baca Semua| N8[Mark All Read]
    N8 --> N
    N --> E

    E -->|Logout| O[Destroy Session]
    O --> P[Redirect ke Login]

    style A fill:#4CAF50,color:#fff
    style Z fill:#f44336,color:#fff
    style P fill:#4CAF50,color:#fff
    style L fill:#f44336,color:#fff
```

## Keterangan Flowchart

### 1. Autentikasi
- User melakukan login dengan email & password
- Middleware `auth` & `role:admin` memverifikasi akses
- Jika role bukan admin, redirect ke dashboard sesuai role

### 2. Admin Dashboard (`/admin/dashboard`)
- Menampilkan statistik lengkap pengguna
- Total users per role (admin, pengurus, DPS, anggota, pelanggan)
- Users aktif & nonaktif
- Users yang perlu perhatian (nonaktif + belum verifikasi)
- Users baru bulan ini
- 6 user terbaru

### 3. User Management (`/admin/users`)
- **Index**: Daftar semua users dengan filter & pencarian
- **Show**: Detail lengkap user + aktivitas (simpanan, pembiayaan, pesanan, toko, produk)
- **Destroy**: Hapus user (kecuali akun admin sendiri)
- **Export**: Download data users dalam format CSV

### 4. Notifikasi (`/admin/notifikasi`)
- Database notifications (Laravel built-in)
- Inbox entries (custom notification system)
- System stats: pending validasi DPS, verifikasi toko, verifikasi produk, new users 7 hari
- Tandai sudah dibaca per notifikasi atau sekaligus

### 5. Profil (`/profile`)
- Edit profil
- Update profil
- Hapus akun (dari `/auth/profile`)

## Routes yang Digunakan Admin

| Route | Method | Controller | Keterangan |
|-------|--------|------------|------------|
| `/admin/dashboard` | GET | AdminDashboardController@index | Dashboard admin |
| `/admin/users` | GET | AdminUserController@index | Daftar users |
| `/admin/users/{user}` | GET | AdminUserController@show | Detail user |
| `/admin/users/{user}` | DELETE | AdminUserController@destroy | Hapus user |
| `/admin/users/export` | GET | AdminUserController@export | Export CSV |
| `/admin/notifikasi` | GET | AdminNotificationController@index | Daftar notifikasi |
| `/admin/notifikasi/{id}/read` | POST | AdminNotificationController@markRead | Tandai dibaca |
| `/admin/notifikasi/read-all` | POST | AdminNotificationController@markAllRead | Tandai semua dibaca |
| `/profile` | GET | ProfileController@edit | Edit profil |
| `/profile` | PATCH | ProfileController@update | Update profil |
| `/profile` | DELETE | ProfileController@destroy | Hapus akun |
