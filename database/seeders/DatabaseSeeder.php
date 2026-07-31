<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissionData = [
            ['nama_permission' => 'mahasiswa.view', 'modul' => 'Mahasiswa', 'aksi' => 'Lihat Data'],
            ['nama_permission' => 'mahasiswa.manage', 'modul' => 'Mahasiswa', 'aksi' => 'Tambah/Edit/Hapus'],
            ['nama_permission' => 'role.manage', 'modul' => 'Manajemen Akses', 'aksi' => 'Kelola Role & Hak Akses'],
            ['nama_permission' => 'pembayaran.view', 'modul' => 'Pembayaran SPP', 'aksi' => 'Lihat Data'],
            ['nama_permission' => 'pembayaran.manage', 'modul' => 'Pembayaran SPP', 'aksi' => 'Tambah/Edit/Hapus'],
            ['nama_permission' => 'matakuliah.view', 'modul' => 'Matakuliah', 'aksi' => 'Lihat Data'],
            ['nama_permission' => 'matakuliah.manage', 'modul' => 'Matakuliah', 'aksi' => 'Tambah/Edit/Hapus'],
            ['nama_permission' => 'buku.view', 'modul' => 'Peminjaman Buku', 'aksi' => 'Lihat Data'],
            ['nama_permission' => 'buku.manage', 'modul' => 'Peminjaman Buku', 'aksi' => 'Tambah/Edit/Hapus'],
            ['nama_permission' => 'prestasi.view', 'modul' => 'Prestasi Mahasiswa', 'aksi' => 'Lihat Data'],
            ['nama_permission' => 'prestasi.create', 'modul' => 'Prestasi Mahasiswa', 'aksi' => 'Tambah Data'],
            ['nama_permission' => 'prestasi.manage', 'modul' => 'Prestasi Mahasiswa', 'aksi' => 'Edit/Hapus Data'],
            ['nama_permission' => 'prestasi.verify', 'modul' => 'Prestasi Mahasiswa', 'aksi' => 'Verifikasi'],
            ['nama_permission' => 'prestasi.report', 'modul' => 'Prestasi Mahasiswa', 'aksi' => 'Laporan'],
        ];

        $permissions = collect($permissionData)->mapWithKeys(function (array $permission) {
            $model = Permission::updateOrCreate([
                'nama_permission' => $permission['nama_permission'],
            ], $permission);

            return [$model->nama_permission => $model];
        });

        $adminRole = Role::updateOrCreate([
            'nama_role' => 'admin',
        ], [
            'deskripsi' => 'Admin memiliki semua akses untuk mengelola data, verifikasi, hak akses, dan laporan.',
        ]);

        $dosenRole = Role::updateOrCreate([
            'nama_role' => 'dosen',
        ], [
            'deskripsi' => 'Dosen melihat data mahasiswa, memverifikasi prestasi, dan melihat laporan prestasi.',
        ]);

        $operatorRole = Role::updateOrCreate([
            'nama_role' => 'operator',
        ], [
            'deskripsi' => 'Operator mengelola operasional harian, data akademik, prestasi, laporan, dan hak akses.',
        ]);

        $mahasiswaRole = Role::updateOrCreate([
            'nama_role' => 'mahasiswa',
        ], [
            'deskripsi' => 'Mahasiswa melihat data, menginput prestasi, dan melihat status verifikasi.',
        ]);

        $adminRole->permissions()->sync($permissions->pluck('id')->all());
        $operatorRole->permissions()->sync($permissions->pluck('id')->all());
        $dosenRole->permissions()->sync([
            $permissions['mahasiswa.view']->id,
            $permissions['prestasi.view']->id,
            $permissions['prestasi.verify']->id,
            $permissions['prestasi.report']->id,
        ]);
        $mahasiswaRole->permissions()->sync([
            $permissions['mahasiswa.view']->id,
            $permissions['prestasi.view']->id,
            $permissions['prestasi.create']->id,
        ]);

        User::updateOrCreate([
            'email' => 'admin@mycampus.test',
        ], [
            'name' => 'Admin MyCampus',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        User::updateOrCreate([
            'email' => 'syafiqhusin2905@gmail.com',
        ], [
            'name' => 'Syafiq Husin',
            'password' => Hash::make('bahlil123'),
            'role_id' => $adminRole->id,
        ]);

        User::updateOrCreate([
            'email' => 'dosen@mycampus.test',
        ], [
            'name' => 'Dosen MyCampus',
            'password' => Hash::make('password'),
            'role_id' => $dosenRole->id,
        ]);

        User::where('email', 'editor@mycampus.test')->delete();
        Role::where('nama_role', 'editor')->delete();

        User::updateOrCreate([
            'email' => 'operator@mycampus.test',
        ], [
            'name' => 'Operator MyCampus',
            'password' => Hash::make('password'),
            'role_id' => $operatorRole->id,
        ]);

        User::updateOrCreate([
            'email' => 'mahasiswa@mycampus.test',
        ], [
            'name' => 'Mahasiswa MyCampus',
            'password' => Hash::make('password'),
            'role_id' => $mahasiswaRole->id,
        ]);
    }
}
