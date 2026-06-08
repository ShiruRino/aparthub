@extends('layouts.app')

@section('title', 'Roles')
@section('page_title', 'Roles')
@section('page_subtitle', 'Kelola role user')

@section('content')
    <div class="toolbar">
        <div class="muted">{{ $roles->total() }} role terdaftar</div>

        @if (auth()->user()->canAccessModule('roles', 'create'))
            <a class="btn" href="{{ route('roles.create') }}">Tambah Role</a>
        @endif
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>User</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td><strong>{{ $role->name }}</strong></td>
                            <td>{{ $role->slug }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                @if ($role->isSystem())
                                    <span class="badge green">Sistem</span>
                                @else
                                    <span class="badge">Custom</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    @if (auth()->user()->canAccessModule('roles', 'update'))
                                        <a class="btn secondary" href="{{ route('roles.edit', $role) }}">Edit</a>
                                    @endif

                                    @if (auth()->user()->canAccessModule('roles', 'delete') && ! $role->isSystem())
                                        <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Hapus role ini?')">
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
                            <td class="empty" colspan="5">Belum ada role.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $roles->links() }}
        </div>
    </div>
@endsection
