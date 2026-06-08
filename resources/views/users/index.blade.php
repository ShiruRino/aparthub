@extends('layouts.app')

@section('title', 'Users')
@section('page_title', 'Users')
@section('page_subtitle', 'Kelola akun dan role user')

@section('content')
    <div class="toolbar">
        <div class="muted">{{ $users->total() }} user terdaftar</div>

        @if (auth()->user()->canAccessModule('users', 'create'))
            <a class="btn" href="{{ route('users.create') }}">Tambah User</a>
        @endif
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->username }}</td>
                            <td>
                                <span @class(['badge', 'green' => $user->isAdmin()])>
                                    {{ $user->role?->name ?? 'Tanpa role' }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    @if (auth()->user()->canAccessModule('access', 'update'))
                                        <a class="btn gold" href="{{ route('users.access.show', $user) }}">Akses</a>
                                    @endif

                                    @if (auth()->user()->canAccessModule('users', 'update'))
                                        <a class="btn secondary" href="{{ route('users.edit', $user) }}">Edit</a>
                                    @endif

                                    @if (auth()->user()->canAccessModule('users', 'delete') && ! auth()->user()->is($user))
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
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
                            <td class="empty" colspan="4">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $users->links() }}
        </div>
    </div>
@endsection
