<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->where('nama_role', '!=', 'editor')
            ->get();

        return view('data-role', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('modul')->orderBy('aksi')->get()->groupBy('modul');
        return view('create-role', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $role = Role::create([
            'nama_role' => $data['nama_role'],
            'deskripsi' => $data['deskripsi'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('data-role')->with('success', 'Role dan hak akses berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::orderBy('modul')->orderBy('aksi')->get()->groupBy('modul');
        $selectedPermissions = $role->permissions->pluck('id')->toArray();

        return view('edit-role', compact('role', 'permissions', 'selectedPermissions'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->validatedData($request);
        $role = Role::findOrFail($id);

        $role->update([
            'nama_role' => $data['nama_role'],
            'deskripsi' => $data['deskripsi'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('data-role')->with('success', 'Role dan hak akses berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('data-role')->with('success', 'Role berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama_role' => [
                'required',
                'string',
                'max:50',
                Rule::in(['admin', 'operator', 'dosen', 'mahasiswa']),
                Rule::unique('roles', 'nama_role')->ignore($request->route('id')),
            ],
            'deskripsi' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);
    }
}
