@extends('layouts.app')

@section('title', 'Modules')
@section('page_title', 'Modules')
@section('page_subtitle', 'Daftar module untuk access control')

@section('content')
    <div class="toolbar">
        <div class="muted">{{ $modules->total() }} module terdaftar</div>

        @if (auth()->user()->canAccessModule('modules', 'create'))
            <a class="btn" href="{{ route('modules.create') }}">Tambah Module</a>
        @endif
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Urutan</th>
                        <th>Permission</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modules as $module)
                        <tr>
                            <td>
                                <strong>{{ $module->name }}</strong>
                                @if ($module->description)
                                    <div class="muted">{{ $module->description }}</div>
                                @endif
                            </td>
                            <td>{{ $module->slug }}</td>
                            <td>
                                <span @class(['badge', 'green' => $module->is_active, 'yellow' => ! $module->is_active])>
                                    {{ $module->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>{{ $module->sort_order }}</td>
                            <td>{{ $module->user_modules_count }}</td>
                            <td>
                                <div class="actions">
                                    @if (auth()->user()->canAccessModule('modules', 'update'))
                                        <a class="btn secondary" href="{{ route('modules.edit', $module) }}">Edit</a>
                                    @endif

                                    @if (auth()->user()->canAccessModule('modules', 'delete') && ! $module->isSystem())
                                        <form method="POST" action="{{ route('modules.destroy', $module) }}" onsubmit="return confirm('Hapus module ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn danger" type="submit">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="6">Belum ada module.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $modules->links() }}
        </div>
    </div>
@endsection
