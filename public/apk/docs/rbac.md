# Master Data Log Aktivitas & Manajemen User Role - Operator Section

Dokumentasi lengkap untuk fitur baru di bagian Operator MyCampus.

## Fitur yang Ditambahkan

### 1. Log Aktivitas (Activity Logs)
Master data untuk mencatat dan memantau semua aktivitas pengguna dalam sistem.

**Lokasi Route:**
- View: `/operator/activity-logs`
- Show Detail: `/operator/activity-logs/{id}`
- Export CSV: `/operator/activity-logs-export`

**File yang Dibuat:**
- Model: `app/Models/ActivityLog.php`
- Controller: `app/Http/Controllers/ActivityLogController.php`
- Views:
  - `resources/views/operator/activity-logs/index.blade.php`
  - `resources/views/operator/activity-logs/show.blade.php`
- Migration: `database/migrations/2026_06_09_000000_create_activity_logs_table.php`

**Fitur:**
- 📋 Daftar log aktivitas dengan filter berdasarkan:
  - Module (Mahasiswa, Role, User, dll)
  - Activity Type (create, update, delete, login, logout)
  - User
  - Rentang Tanggal
- 👁️ Lihat detail activity log dengan data before & after
- 📤 Export data ke CSV
- 🗑️ Hapus log aktivitas lama (> 90 hari)
- 🔍 Pencarian dan sorting

### 2. Manajemen User
Kelola akun pengguna, role, dan hak akses pengguna.

**Lokasi Route:**
- List Users: `/operator/users`
- Create User: `/operator/users/create`
- Edit User: `/operator/users/{id}/edit`
- Delete User: `/operator/users/{id}` (DELETE)
- User Activity Logs: `/operator/users/{id}/activity-logs`
- Reset Password: `/operator/users/{id}/reset-password` (POST)

**File yang Dibuat:**
- Controller: `app/Http/Controllers/UserController.php`
- Views:
  - `resources/views/operator/users/index.blade.php`
  - `resources/views/operator/users/create.blade.php`
  - `resources/views/operator/users/edit.blade.php`
  - `resources/views/operator/users/activity-logs.blade.php`

**Fitur:**
- 👥 Daftar semua user dengan info:
  - Nama
  - Email
  - Role
  - Tanggal dibuat
- ➕ Tambah user baru dengan validasi
- ✏️ Edit data user (nama, email, password, role)
- 🗑️ Hapus user (dengan proteksi agar tidak bisa hapus diri sendiri)
- 📊 Lihat activity log per user
- 🔐 Reset password user
- 🔍 Filter berdasarkan role atau search

### 3. Trait untuk Automatic Logging
Trait untuk memudahkan pencatatan aktivitas otomatis pada model.

**File yang Dibuat:**
- Trait: `app/Traits/LogsActivity.php`

**Penggunaan:**
```php
<?php
namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use LogsActivity;
    // ... model definition
}
```

Trait ini akan otomatis mencatat:
- `created`: Ketika record dibuat
- `updated`: Ketika record diubah
- `deleted`: Ketika record dihapus

## Database Structure

### activity_logs Table
```sql
CREATE TABLE activity_logs (
  id BIGINT PRIMARY KEY,
  user_id BIGINT (FOREIGN KEY),
  activity_type VARCHAR(255), -- create, update, delete, login, logout
  module VARCHAR(255), -- mahasiswa, role, user, pembayaran, etc
  description TEXT,
  data_before JSON,
  data_after JSON,
  ip_address VARCHAR(45),
  user_agent TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  INDEX user_id,
  INDEX activity_type,
  INDEX module,
  INDEX created_at
);
```

## Permissions (Role-Based)

### Admin Role
Pengguna dengan role `admin` dapat mengakses semua fitur operator:

- `activity-log.view` - Lihat log aktivitas
- `activity-log.export` - Export log aktivitas
- `activity-log.delete` - Hapus log lama
- `user.view` - Lihat daftar user
- `user.create` - Membuat user baru
- `user.edit` - Edit user
- `user.delete` - Hapus user
- `user.manage` - Kelola user

## Helper Function

### Logging Manual
Untuk mencatat aktivitas secara manual:

```php
use App\Models\ActivityLog;

// Basic logging
ActivityLog::log(
    'login',
    'auth',
    'User ' . auth()->user()->name . ' logged in'
);

// With data before and after
ActivityLog::log(
    'update',
    'mahasiswa',
    'Updated mahasiswa data',
    $oldData,
    $newData
);
```

## Query Examples

```php
use App\Models\ActivityLog;
use Carbon\Carbon;

// Filter by module
$logs = ActivityLog::byModule('mahasiswa')->get();

// Filter by activity type
$logs = ActivityLog::byActivityType('create')->get();

// Filter by user
$logs = ActivityLog::byUser(1)->get();

// Filter by date range
$logs = ActivityLog::inDateRange(
    Carbon::parse('2026-06-01'),
    Carbon::parse('2026-06-30')
)->get();

// Combined query
$logs = ActivityLog::byUser(1)
    ->byModule('mahasiswa')
    ->byActivityType('create')
    ->latest('created_at')
    ->paginate(50);
```

## Routing Structure

```
/operator
├── /activity-logs (GET) - List activity logs
├── /activity-logs/{id} (GET) - Show activity log detail
├── /activity-logs-export (GET) - Export activity logs CSV
├── /activity-logs/delete-old (DELETE) - Delete old logs
└── /users
    ├── (GET) - List users
    ├── /create (GET) - Create user form
    ├── (POST) - Store user
    ├── /{id}/edit (GET) - Edit user form
    ├── /{id} (PUT) - Update user
    ├── /{id} (DELETE) - Delete user
    ├── /{id}/activity-logs (GET) - User activity logs
    └── /{id}/reset-password (POST) - Reset user password
```

## Seeding Permissions

Jalankan seeder untuk menambahkan permissions:

```bash
php artisan db:seed --class=OperatorPermissionSeeder
```

Atau tambahkan ke `DatabaseSeeder.php`:

```php
$this->call(OperatorPermissionSeeder::class);
```

## Middleware & Security

### Role Middleware
Semua route operator dilindungi dengan middleware `role:admin`:

```php
Route::middleware('role:admin')->group(function () {
    // Operator routes
});
```

### Protected Actions
- User tidak bisa menghapus akun diri sendiri
- Password hanya bisa direset, bukan dilihat
- Semua aktivitas dicatat otomatis

## Best Practices

### 1. Menggunakan Trait untuk Model
Tambahkan trait `LogsActivity` ke semua model yang perlu dicatat aktivitasnya:

```php
use App\Traits\LogsActivity;

class Mahasiswa extends Model
{
    use LogsActivity;
}
```

### 2. Manual Logging
Untuk aktivitas khusus, gunakan helper function:

```php
ActivityLog::log('login', 'auth', 'User logged in');
ActivityLog::log('logout', 'auth', 'User logged out');
```

### 3. Backup Log Lama
Sebelum menghapus log lama, export terlebih dahulu ke CSV:

```bash
GET /operator/activity-logs-export?start_date=2026-01-01&end_date=2026-03-31
```

## Troubleshooting

### Activity logs tidak terkatat
- Pastikan user sudah login (authenticated)
- Periksa apakah trait `LogsActivity` sudah ditambahkan ke model

### Filter tidak bekerja
- Pastikan format tanggal benar (YYYY-MM-DD)
- Periksa apakah data ada di database

### Export CSV error
- Pastikan server memiliki akses write ke `/storage` directory
- Periksa memory limit PHP jika data sangat besar

## Screenshots & UI

Semua halaman menggunakan template SB Admin 2 Bootstrap yang konsisten dengan design existing MyCampus.

## Update Paths

- ✅ Sidebar menu sudah ditambahkan "OPERATOR" section
- ✅ Database migration sudah dijalankan
- ✅ Routes sudah dikonfigurasi
- ✅ Controllers, Models, dan Views sudah dibuat
- ✅ Permissions seeder sudah disiapkan

## Next Steps (Optional)

Untuk enhancement lebih lanjut:

1. **Dashboard Analytics**
   - Grafik aktivitas per hari/bulan
   - Top users by activity
   - Most modified modules

2. **Real-time Notifications**
   - Alert untuk aktivitas sensitif
   - Notification untuk delete operations

3. **Advanced Audit Trail**
   - Compare data sebelum & sesudah
   - Show diff view untuk changes
   - Version history untuk dokumen

4. **Backup Integration**
   - Auto backup saat delete
   - Restore functionality

5. **Two-Factor Authentication**
   - MFA untuk akun admin
   - OTP verification

---

**Version:** 1.0.0  
**Created:** 2026-06-09  
**Last Updated:** 2026-06-09
