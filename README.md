# Koperasi Syariah

Aplikasi koperasi syariah berbasis Laravel.

## Persiapan

Pastikan PHP, Composer, Node.js, npm, dan database yang digunakan aplikasi sudah terpasang.

### 1. Clone repository

```bash
git clone https://github.com/salmankitoko-sys/KOPERASI-TA-SALMAN-ALFARISI.git
cd KOPERASI-TA-SALMAN-ALFARISI
```

### 2. Install dependency dan build aset

```bash
composer install && npm install && npm run build
```

### 3. Siapkan konfigurasi aplikasi

```bash
cp .env.example .env && php artisan key:generate
```

Sesuaikan konfigurasi database di file `.env` sebelum menjalankan migration.

### 4. Buat tabel dan isi data awal

```bash
php artisan migrate --seed
```

### 5. Jalankan aplikasi

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

## Akun login default

- Email: [admin@koperasi.test](mailto:admin@koperasi.test)
- Password: `password`
