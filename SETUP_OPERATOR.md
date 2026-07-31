# SETUP GUIDE: Master Data Log Aktivitas & Manajemen User Role

## 📋 Yang Sudah Dibuat

### 1. Database & Model
✅ **Migration**: `database/migrations/2026_06_09_000000_create_activity_logs_table.php`
- Tabel `activity_logs` sudah dibuat dan termigrasi
- Struktur lengkap dengan indexes

✅ **Model**: `app/Models/ActivityLog.php`
- Query scopes untuk filtering
- Relasi dengan User model
- Helper method `log()` untuk mencatat aktivitas

### 2. Controllers
✅ **ActivityLogController**: `app/Http/Controllers/ActivityLogController.php`
- index() - Tampilkan daftar log dengan filter
- show() - Lihat detail log
- export() - Export ke CSV
- deleteOldLogs() - Hapus log lama

✅ **UserController**: `app/Http/Controllers/UserController.php`
- index() - Daftar user dengan filter
- create() - Form tambah user
- store() - Simpan user baru
- edit() - Form edit user
- update() - Update user
- destroy() - Hapus user
- activityLogs() - Lihat activity per user
- resetPassword() - Reset password user

### 3. Views
✅ **Activity Logs Views**:
- `resources/views/operator/activity-logs/index.blade.php` - Daftar log
- `resources/views/operator/activity-logs/show.blade.php` - Detail log

✅ **User Management Views**:
- `resources/views/operator/users/index.blade.php` - Daftar user
- `resources/views/operator/users/create.blade.php` - Form tambah
- `resources/views/operator/users/edit.blade.php` - Form edit
- `resources/views/operator/users/activity-logs.blade.php` - Activity per user

### 4. Routes
✅ **Operator Routes** di `routes/web.php`:
```php
/operator/activity-logs                    # Daftar log
/operator/activity-logs/{id}              # Detail log
/operator/activity-logs-export            # Export CSV
/operator/activity-logs/delete-old        # Hapus log lama
/operator/users                           # Daftar user
/operator/users/create                    # Form tambah user
/operator/users/{id}/edit                 # Form edit user
/operator/users/{id}/activity-logs        # Activity user
/operator/users/{id}/reset-password       # Reset password
```

### 5. Helper & Support
✅ **Trait**: `app/Traits/LogsActivity.php`
- Auto-log model changes (create/update/delete)
- Gunakan `use LogsActivity` di model

✅ **Seeder**: `database/seeders/OperatorPermissionSeeder.php`
- Permissions untuk operator section

✅ **Helper**: `app/Http/Controllers/DashboardHelper.php`
- Utility functions untuk stats

## 🚀 Setup Instructions

### Step 1: Migrate Database
```bash
cd c:\laragon\www\project1
php artisan migrate
```
✅ **SUDAH DILAKUKAN** - Tabel activity_logs sudah terbuat

### Step 2: Seed Permissions
```bash
php artisan db:seed --class=OperatorPermissionSeeder
```

Atau tambahkan ke `database/seeders/DatabaseSeeder.php`:
```php
$this->call(OperatorPermissionSeeder::class);
```

### Step 3: Update Admin Role
Di database, pastikan Admin user memiliki role_id yang benar dan role tersebut memiliki permissions:
```sql
-- Lihat permissions
SELECT * FROM permissions WHERE modul = 'Operator';

-- Assign permissions ke admin role
-- Lakukan dari UI atau langsung insert ke role_permissions table
```

## 📱 Cara Mengakses

### 1. Login sebagai Admin
- Akses: `http://localhost/project1/`
- Login dengan akun admin

### 2. Buka Operator Section
- Sidebar → **OPERATOR** section
- Pilih:
  - **Log Aktivitas** - Pantau semua aktivitas
  - **Manajemen User** - Kelola user
  - **Manajemen Role** - Kelola role (sudah ada sebelumnya)

## 🔧 Konfigurasi Tambahan

### Menggunakan Automatic Logging pada Model Existing

Edit model, tambahkan trait:

```php
<?php
namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use LogsActivity;
    
    // ... rest of model
}
```

Sekarang setiap create/update/delete pada model ini akan otomatis tercatat di activity_logs.

### Manual Logging

Di controller atau service, gunakan:

```php
use App\Models\ActivityLog;

// Simple logging
ActivityLog::log('login', 'auth', 'User ' . auth()->user()->name . ' logged in');

// With data tracking
ActivityLog::log(
    'update',
    'mahasiswa',
    'Updated mahasiswa',
    $oldData,      // data_before
    $newData       // data_after
);
```

## 🔐 Security Notes

### Permissions yang Perlu Diassign
Ke Role Admin, assign permissions:
- `activity-log.view`
- `activity-log.export`
- `activity-log.delete`
- `user.view`
- `user.create`
- `user.edit`
- `user.delete`
- `user.manage`

### Protections
✅ Route middleware: `role:admin` - Hanya admin yang bisa akses operator section
✅ User tidak bisa menghapus diri sendiri
✅ Password tidak ditampilkan, hanya bisa direset
✅ Semua aktivitas dicatat (ip_address, user_agent, user_id)

## 📊 Fitur-Fitur

### Activity Logs
- ✅ Filter by module, activity_type, user, date range
- ✅ Pagination 50 per halaman
- ✅ Lihat detail dengan before/after data
- ✅ Export ke CSV
- ✅ Delete logs lama
- ✅ Search / Filter capabilities

### User Management
- ✅ CRUD operations
- ✅ Assign role
- ✅ Reset password
- ✅ View activity per user
- ✅ Search by name/email
- ✅ Filter by role

## 📝 Query Examples

```php
use App\Models\ActivityLog;
use Carbon\Carbon;

// Daftar semua aktivitas hari ini
$today = ActivityLog::whereDate('created_at', today())->get();

// Aktivitas satu user
$userLogs = ActivityLog::byUser(1)->latest('created_at')->get();

// Aktivitas module tertentu
$mahasiswaLogs = ActivityLog::byModule('Mahasiswa')->get();

// Update activities terakhir seminggu
$updates = ActivityLog::byActivityType('update')
    ->whereDate('created_at', '>=', now()->subDays(7))
    ->latest('created_at')
    ->paginate(50);

// Aktivitas dalam range tanggal
$logs = ActivityLog::inDateRange(
    Carbon::parse('2026-06-01'),
    Carbon::parse('2026-06-30')
)->paginate(50);
```

## 🐛 Troubleshooting

### Aktivitas tidak terkatat?
1. Pastikan user sudah authenticated
2. Pastikan trait `LogsActivity` ditambahkan ke model
3. Check logs: `storage/logs/laravel.log`

### Permission denied error?
1. Pastikan user memiliki role admin
2. Check role_permissions table
3. Seed permissions: `php artisan db:seed --class=OperatorPermissionSeeder`

### Sidebar menu tidak muncul?
1. Ubah login dengan akun admin
2. Check role user di database
3. Verifikasi role_id di users table

## 📞 File Reference

| File | Purpose |
|------|---------|
| `app/Models/ActivityLog.php` | Model untuk activity logs |
| `app/Http/Controllers/ActivityLogController.php` | Controller untuk logs |
| `app/Http/Controllers/UserController.php` | Controller untuk user |
| `app/Traits/LogsActivity.php` | Trait auto-logging |
| `database/migrations/2026_06_09_000000_create_activity_logs_table.php` | Migration |
| `database/seeders/OperatorPermissionSeeder.php` | Seeder |
| `routes/web.php` | Routes configuration |
| `resources/views/operator/` | All views |

## ✅ Checklist Setup

- [x] Migration database sudah dijalankan
- [x] Controllers sudah dibuat
- [x] Models sudah dibuat
- [x] Views sudah dibuat
- [x] Routes sudah dikonfigurasi
- [ ] Permissions sudah di-seed
- [ ] Admin role sudah punya permissions
- [ ] Sidebar menu sudah di-update (optional)
- [ ] Test akses /operator/activity-logs
- [ ] Test akses /operator/users

## 🎯 Next: Setup Permissions

Jalankan command:
```bash
php artisan db:seed --class=OperatorPermissionSeeder
```

Kemudian di Admin Role, assign semua permissions baru:
- activity-log.*
- user.*

## 📚 Documentation
See `OPERATOR_FEATURES.md` untuk dokumentasi lengkap.

---
**Created**: 2026-06-09
**Status**: Ready for use
