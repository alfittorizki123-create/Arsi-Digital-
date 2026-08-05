<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,staff'],
            'password' => ['required', 'string', 'min:6'],
        ], [], [
            'name' => 'nama lengkap',
            'username' => 'username',
            'email' => 'email',
            'role' => 'role/hak akses',
            'password' => 'kata sandi',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'email' => $data['email'] ? strtolower($data['email']) : $data['username'] . '@bapenda.riau.go.id',
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        ActivityLogger::log('create', 'User', "Menambahkan pengguna baru: {$user->name} ({$user->role})", [
            'username' => $user->username,
            'role' => $user->role,
        ], $request);

        return redirect()->route('users.index')->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,staff'],
        ], [], [
            'name' => 'nama lengkap',
            'username' => 'username',
            'email' => 'email',
            'role' => 'role/hak akses',
        ]);

        $user->update([
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'email' => $data['email'] ? strtolower($data['email']) : $user->email,
            'role' => $data['role'],
        ]);

        ActivityLogger::log('update', 'User', "Memperbarui pengguna: {$user->name}", [
            'username' => $user->username,
            'role' => $user->role,
        ], $request);

        return redirect()->route('users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ], [], [
            'password' => 'kata sandi baru',
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        ActivityLogger::log('update', 'User', "Mereset kata sandi untuk pengguna: {$user->name}", [
            'username' => $user->username,
        ], $request);

        return redirect()->route('users.index')->with('success', "Kata sandi pengguna {$user->name} berhasil direset.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Gagal: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLogger::log('delete', 'User', "Menghapus pengguna: {$userName}", [
            'user_id' => $user->id,
        ], $request);

        return redirect()->route('users.index')->with('success', "Pengguna {$userName} berhasil dihapus.");
    }
}
