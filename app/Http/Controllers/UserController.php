<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        // Filter berdasarkan role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Search berdasarkan name atau email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(50);
        $roles = Role::orderBy('nama_role')->get();

        return view('operator.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::orderBy('nama_role')->get();
        return view('operator.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role_id.required' => 'Role harus dipilih',
            'role_id.exists' => 'Role tidak valid',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        // Log aktivitas
        ActivityLog::log('create', 'user', "Membuat user baru: {$user->name}", null, $user->toArray());

        return redirect()->route('operator.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('nama_role')->get();
        return view('operator.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role_id.required' => 'Role harus dipilih',
            'role_id.exists' => 'Role tidak valid',
        ]);

        $dataBefore = $user->toArray();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ]);

        if ($validated['password']) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        // Log aktivitas
        ActivityLog::log('update', 'user', "Mengubah data user: {$user->name}", $dataBefore, $user->toArray());

        return redirect()->route('operator.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Delete the specified user
     */
    public function destroy(User $user)
    {
        // Jangan bisa menghapus user sedang login
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $userName = $user->name;
        $dataBefore = $user->toArray();

        $user->delete();

        // Log aktivitas
        ActivityLog::log('delete', 'user', "Menghapus user: {$userName}", $dataBefore, null);

        return redirect()->route('operator.users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Show user activity logs
     */
    public function activityLogs(User $user)
    {
        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(50);

        return view('operator.users.activity-logs', compact('user', 'activityLogs'));
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => bcrypt($validated['password'])]);

        ActivityLog::log('update', 'user', "Reset password user: {$user->name}", null, null);

        return redirect()->back()->with('success', 'Password user berhasil direset.');
    }
}
