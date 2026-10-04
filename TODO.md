# TODO - Integrasi Database Anggota

## Completed ✓

### 1. Migration
- [x] Created: `database/migrations/2026_07_31_074818_add_anggota_fields_to_users_table.php`
  - Adds: `pekerjaan`, `penghasilan`, `status`, `tgl_gabung`
- [x] Ran migration successfully (batch 9)

### 2. Model
- [x] Updated: `app/Models/User.php`
  - Added `$fillable` fields: pekerjaan, penghasilan, status, tgl_gabung
  - Added `simpanan()` hasMany relationship

### 3. FormRequest (Validation)
- [x] Created: `app/Http/Requests/Pengurus/StoreAnggotaRequest.php`
  - Validates: name, email, no_hp, pekerjaan, penghasilan, tgl_gabung, pokok, status, password
  - Custom error messages in Bahasa Indonesia

### 4. Controller
- [x] Created: `app/Http/Controllers/Pengurus/AnggotaController.php`
  - `index()` - JSON paginated list with simpanan_pokok sum, search/filter by status/jenis
  - `store()` - Create user + simpanan pokok in transaction
  - `show()` - Single user detail with simpanan_pokok sum
  - `update()` - Update user + simpanan pokok in transaction
  - `destroy()` - Delete user with cascade

### 5. Routes
- [x] Added to `routes/web.php`:
  - `Route::resource('/anggota', AnggotaController::class)` under pengurus group (middleware: auth, role:manager)

### 6. Blade View
- [x] Fixed: `resources/views/dashboard/pengurus/partials/anggota.blade.php`
  - Kolom "Pokok" now reads from `$anggota->simpanan_pokok` (real data from simpanan table)
  - Kolom "Jenis" removed from anggota membership data
  - Filter dropdown "Jenis" now connected to `filterAnggota()` function
  - Tombol "Edit" has `@click="editAnggota(id)"` with fetch() to show endpoint
  - Tombol "Hapus" has `@click="hapusAnggota(id, name)"` with fetch() DELETE
  - `tambahAnggota()` now uses fetch() to store/update endpoint with CSRF token
  - Loading spinner, success/error messages, form reset after submit

### 7. ManagerDashboardController
- [x] Updated: `app/Http/Controllers/ManagerDashboardController.php`
  - Added `withSum()` for simpanan_pokok on anggotaList query
  - Increased limit from 10 to 50

## Pending
- [ ] Test CRUD endpoints via browser
- [ ] Verify filter/search functionality in Alpine.js
