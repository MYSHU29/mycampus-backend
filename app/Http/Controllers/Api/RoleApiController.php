<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleApiController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')
            ->where('nama_role', '!=', 'editor')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_role' => [
                'required', 'string', 'max:50',
                Rule::in(['admin', 'operator', 'dosen', 'mahasiswa']),
                Rule::unique('roles', 'nama_role'),
            ],
            'deskripsi' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create([
            'nama_role' => $data['nama_role'],
            'deskripsi' => $data['deskripsi'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil ditambahkan',
            'data' => $role->load('permissions'),
        ], 201);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $role->load('permissions'),
        ]);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate([
            'nama_role' => [
                'required', 'string', 'max:50',
                Rule::in(['admin', 'operator', 'dosen', 'mahasiswa']),
                Rule::unique('roles', 'nama_role')->ignore($role->id),
            ],
            'deskripsi' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role->update([
            'nama_role' => $data['nama_role'],
            'deskripsi' => $data['deskripsi'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diperbarui',
            'data' => $role->fresh()->load('permissions'),
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->permissions()->detach();
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus',
        ]);
    }

    public function permissions(): JsonResponse
    {
        $permissions = Permission::orderBy('modul')->orderBy('aksi')->get()->groupBy('modul');

        return response()->json([
            'success' => true,
            'data' => $permissions,
        ]);
    }
}
