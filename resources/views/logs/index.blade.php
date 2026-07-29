@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-stack-md mb-stack-md">
        <div>
            <h2 class="font-display-md text-display-md text-on-surface">Log Aktivitas</h2>
            <p class="text-body-md text-on-surface-variant mt-1">Catatan aktivitas setiap user di aplikasi.</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        <div class="px-stack-md py-4 bg-surface-container/40 border-b border-outline-variant flex items-center justify-between gap-3">
            <h3 class="text-headline-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">manage_history</span>
                Daftar Aktivitas
            </h3>
            <span class="text-xs text-on-surface-variant font-medium">Total: {{ number_format($logs->total()) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface border-b border-outline-variant">
                    <tr>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Waktu</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">User</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Aksi</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Modul</th>
                        <th class="py-3 px-4 font-table-header text-table-header text-on-surface-variant">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-surface-container/40">
                            <td class="py-3 px-4 text-body-sm text-on-surface whitespace-nowrap">
                                {{ $log->created_at?->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-4 text-body-sm text-on-surface">
                                <div class="font-semibold">{{ $log->name ?: '-' }}</div>
                                <div class="text-xs text-on-surface-variant">{{ $log->username ?: '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-body-sm text-on-surface">
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-body-sm text-on-surface">{{ $log->module ?: '-' }}</td>
                            <td class="py-3 px-4 text-body-sm text-on-surface min-w-96">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 px-4 text-center text-on-surface-variant">Belum ada log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-stack-md py-4 border-t border-outline-variant">
            {{ $logs->links() }}
        </div>
    </div>
@endsection