@extends('layouts.admin')

@section('title', 'Deposit Methods')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Deposit Methods</h1>
        <a href="{{ route('admin.deposit-methods.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
            <x-icon name="plus" class="w-4 h-4" />
            Add method
        </a>
    </div>

    <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface-raised)]">
                    <tr>
                        <th class="text-left px-6 py-3.5 font-semibold text-[var(--text-muted)]">Name</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-[var(--text-muted)]">Code</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-[var(--text-muted)]">Type</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-[var(--text-muted)]">Status</th>
                        <th class="text-right px-6 py-3.5 font-semibold text-[var(--text-muted)]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse($methods as $method)
                        <tr class="hover:bg-[var(--line-soft)] transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                                        <x-icon name="settings" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-[var(--text)]">{{ $method->name }}</p>
                                        @if($method->description)
                                            <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ Str::limit($method->description, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-[var(--text-muted)]">{{ $method->code }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[var(--line-soft)] text-[var(--text-muted)]">
                                    {{ ucfirst($method->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($method->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.deposit-methods.edit', $method) }}" class="p-2 text-[var(--text-muted)] hover:text-[var(--text)] hover:bg-[var(--line-soft)] rounded-lg transition-colors" aria-label="Edit">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.deposit-methods.destroy', $method) }}" class="inline" onsubmit="return confirm('Delete this deposit method?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" aria-label="Delete">
                                            <x-icon name="trash-2" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[var(--text-muted)]">
                                <p class="text-sm">No deposit methods configured.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($methods->hasPages())
            <div class="px-6 py-4 border-t border-[var(--line)]">
                {{ $methods->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
