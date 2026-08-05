@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div x-data="{ 
    createModal: false, 
    editModal: false, 
    resetModal: false,
    activeUser: { id: null, name: '', username: '', email: '', role: 'staff' } 
}">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-lg">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Kelola Pengguna</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Kelola akun pegawai, pembagian role (Admin/Staff), dan akses keamanan sistem.</p>
        </div>
        <div>
            <button @click="createModal = true"
                    class="flex items-center justify-center gap-2 h-[42px] px-4 rounded-xl bg-primary text-on-primary font-bold text-xs hover:bg-primary/90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-base">person_add</span>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-stack-md mb-stack-lg">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-stack-md shadow-sm flex items-center justify-between">
            <div>
                <p class="text-label-md text-on-surface-variant font-bold">Total Pengguna</p>
                <p class="text-display-md text-on-surface mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-primary-container/40 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">group</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-stack-md shadow-sm flex items-center justify-between">
            <div>
                <p class="text-label-md text-on-surface-variant font-bold">Administrator</p>
                <p class="text-display-md text-red-600 dark:text-red-400 mt-1">{{ number_format($stats['admin']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-stack-md shadow-sm flex items-center justify-between">
            <div>
                <p class="text-label-md text-on-surface-variant font-bold">Staff / Operator</p>
                <p class="text-display-md text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats['staff']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">badge</span>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-stack-md mb-stack-md shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline" style="font-size: 18px;">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full pl-9 pr-3 py-2 border border-outline-variant rounded-lg bg-surface focus:outline-none focus:border-primary text-body-md"
                       placeholder="Cari Nama, Username, atau Email...">
            </div>

            <div class="w-40">
                <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface focus:outline-none focus:border-primary text-body-md">
                    <option value="">Semua Role</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="staff" @selected(request('role') === 'staff')>Staff</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-bold text-xs hover:bg-primary/90 transition-colors">Cari</button>
            
            @if(request('search') || request('role'))
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-outline-variant text-on-surface-variant rounded-lg font-bold text-xs hover:bg-surface-container transition-colors">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Pengguna --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Pengguna</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Username</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Email</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-center">Role / Hak Akses</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($users as $user)
                        <tr class="hover:bg-surface-container/40 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary font-bold flex items-center justify-center text-xs shrink-0 border border-outline-variant">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-body-md text-on-surface">{{ $user->name }}</p>
                                        @if ($user->id === Auth::id())
                                            <span class="inline-block px-1.5 py-0.2 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded">Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-sm text-on-surface-variant">{{ $user->username }}</td>
                            <td class="py-3 px-4 text-sm text-on-surface-variant">{{ $user->email ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                @if ($user->isAdmin())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 dark:bg-red-950 dark:text-red-300 dark:border-red-800">
                                        <span class="material-symbols-outlined text-xs">admin_panel_settings</span>
                                        <span>ADMIN</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800">
                                        <span class="material-symbols-outlined text-xs">badge</span>
                                        <span>STAFF</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Edit User --}}
                                    <button type="button" 
                                            @click="activeUser = { id: {{ $user->id }}, name: {{ json_encode($user->name) }}, username: {{ json_encode($user->username) }}, email: {{ json_encode($user->email) }}, role: {{ json_encode($user->role) }} }; editModal = true"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-container text-on-primary hover:bg-primary-container/90 transition-colors shadow-sm"
                                            title="Edit Data Pengguna">
                                        <span class="material-symbols-outlined text-xs">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    {{-- Reset Password --}}
                                    <button type="button" 
                                            @click="activeUser = { id: {{ $user->id }}, name: {{ json_encode($user->name) }}, username: {{ json_encode($user->username) }} }; resetModal = true"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-950 dark:text-amber-300 transition-colors shadow-sm"
                                            title="Reset Kata Sandi">
                                        <span class="material-symbols-outlined text-xs">key</span>
                                        <span>Reset Pass</span>
                                    </button>

                                    {{-- Hapus User (Hanya jika bukan akun sendiri) --}}
                                    @if ($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus akun {{ $user->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-error-container text-on-error-container hover:bg-error-container/80 transition-colors shadow-sm" title="Hapus Pengguna">
                                                <span class="material-symbols-outlined text-xs">delete</span>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-on-surface-variant">
                                Belum ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-outline-variant">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH PENGGUNA --}}
    <div x-show="createModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="createModal" x-transition.opacity class="fixed inset-0 bg-black/60 transition-opacity" @click="createModal = false"></div>
            <div x-show="createModal" x-transition.scale class="relative bg-surface rounded-2xl shadow-2xl w-full p-6 border border-outline-variant" style="max-width: 440px;">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-outline-variant/40">
                    <h3 class="text-headline-sm font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">person_add</span>
                        <span>Tambah Pengguna Baru</span>
                    </h3>
                    <button @click="createModal = false" class="text-on-surface-variant hover:text-on-surface p-1 rounded-lg hover:bg-surface-container">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="space-y-3 text-left">
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Nama Lengkap <span class="text-error">*</span></label>
                            <input type="text" name="name" required placeholder="Contoh: Budi Santoso, S.Kom"
                                   class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Username <span class="text-error">*</span></label>
                            <input type="text" name="username" required placeholder="Contoh: budi"
                                   class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Email <span class="text-on-surface-variant font-normal">(Opsional)</span></label>
                            <input type="email" name="email" placeholder="budi@bapenda.riau.go.id"
                                   class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Role / Hak Akses <span class="text-error">*</span></label>
                            <select name="role" required class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary font-bold">
                                <option value="staff">STAFF (Hanya Akses Input & Pinjam)</option>
                                <option value="admin">ADMIN (Akses Penuh + Pengaturan)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Kata Sandi <span class="text-error">*</span></label>
                            <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                                   class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2 pt-3 border-t border-outline-variant/40">
                        <button type="button" @click="createModal = false" class="px-4 py-2 text-xs font-bold text-on-surface-variant hover:bg-surface-container border border-outline-variant rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold bg-primary text-on-primary hover:bg-primary/90 rounded-lg transition-colors shadow-sm">Simpan Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT PENGGUNA --}}
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-black/60 transition-opacity" @click="editModal = false"></div>
            <div x-show="editModal" x-transition.scale class="relative bg-surface rounded-2xl shadow-2xl w-full p-6 border border-outline-variant" style="max-width: 440px;">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-outline-variant/40">
                    <h3 class="text-headline-sm font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">edit</span>
                        <span>Edit Data Pengguna</span>
                    </h3>
                    <button @click="editModal = false" class="text-on-surface-variant hover:text-on-surface p-1 rounded-lg hover:bg-surface-container">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                <form :action="'/users/' + activeUser.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-3 text-left">
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Nama Lengkap <span class="text-error">*</span></label>
                            <input type="text" name="name" x-model="activeUser.name" required
                                   class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Username <span class="text-error">*</span></label>
                            <input type="text" name="username" x-model="activeUser.username" required
                                   class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Email <span class="text-on-surface-variant font-normal">(Opsional)</span></label>
                            <input type="email" name="email" x-model="activeUser.email"
                                   class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1">Role / Hak Akses <span class="text-error">*</span></label>
                            <select name="role" x-model="activeUser.role" required class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary font-bold">
                                <option value="staff">STAFF (Hanya Akses Input & Pinjam)</option>
                                <option value="admin">ADMIN (Akses Penuh + Pengaturan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2 pt-3 border-t border-outline-variant/40">
                        <button type="button" @click="editModal = false" class="px-4 py-2 text-xs font-bold text-on-surface-variant hover:bg-surface-container border border-outline-variant rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold bg-primary text-on-primary hover:bg-primary/90 rounded-lg transition-colors shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL RESET PASSWORD --}}
    <div x-show="resetModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="resetModal" x-transition.opacity class="fixed inset-0 bg-black/60 transition-opacity" @click="resetModal = false"></div>
            <div x-show="resetModal" x-transition.scale class="relative bg-surface rounded-2xl shadow-2xl w-full p-6 border border-outline-variant text-center" style="max-width: 380px;">
                <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 shadow-xs" style="background-color: #fef3c7; color: #d97706;">
                    <span class="material-symbols-outlined text-2xl">key</span>
                </div>
                <h3 class="text-headline-sm font-bold text-on-surface mb-1">Reset Kata Sandi</h3>
                <p class="text-xs text-on-surface-variant mb-5">Masukkan kata sandi baru untuk <strong x-text="activeUser.name" class="text-on-surface"></strong> (<span x-text="activeUser.username"></span>)</p>

                <form :action="'/users/' + activeUser.id + '/reset-password'" method="POST">
                    @csrf
                    <div class="mb-5 text-left">
                        <label class="block text-xs font-bold text-on-surface-variant mb-1">Kata Sandi Baru <span class="text-error">*</span></label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                               class="w-full px-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:outline-none focus:border-primary">
                    </div>

                    <div class="flex justify-center gap-3 pt-2 border-t border-outline-variant/40">
                        <button type="button" @click="resetModal = false" class="px-4 py-2 text-xs font-bold text-on-surface-variant bg-surface hover:bg-surface-container border border-outline-variant rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white rounded-lg shadow-sm transition-colors" style="background-color: #d97706;">Reset Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
