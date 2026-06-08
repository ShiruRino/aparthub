@extends('layouts.app')

@section('title', 'Akses User')

@section('content')
    @php
        $permissions = $user->userModules->keyBy('module_id');
        $isAdmin = $user->isAdmin();
    @endphp

    <div class="toolbar">
        <div>
            <h2 style="margin:0;color:#0b2149;font-size:22px">Hak Akses {{ $user->name }}</h2>
            <div class="muted">{{ $user->username }} - {{ $user->role?->name ?? 'Tanpa role' }}</div>
        </div>
        <a class="btn secondary" href="{{ route('users.index') }}">Kembali ke Users</a>
    </div>

    <form class="panel access-card" method="POST" action="{{ route('users.access.update', $user) }}">
        @csrf
        @method('PUT')

        <header>
            <div>
                <strong>Module Permission</strong>
                <div class="muted">Centang hak create, read, update, dan delete untuk user ini.</div>
            </div>

            @if ($isAdmin)
                <span class="badge green">Full Access</span>
            @else
                <button class="btn gold" type="submit">Simpan Hak Akses</button>
            @endif
        </header>

        <div class="table-wrap">
            <table class="access-table">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Create</th>
                        <th>Read</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modules as $module)
                        @php
                            $permission = $permissions->get($module->id);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $module->name }}</strong>
                                <div class="muted">{{ $module->slug }}{{ $module->is_active ? '' : ' - nonaktif' }}</div>
                            </td>
                            @foreach (['can_create', 'can_read', 'can_update', 'can_delete'] as $ability)
                                <td>
                                    <input
                                        type="checkbox"
                                        name="permissions[{{ $module->id }}][{{ $ability }}]"
                                        value="1"
                                        @checked($isAdmin || (bool) ($permission?->{$ability}))
                                        @disabled($isAdmin)
                                        aria-label="{{ $user->username }} {{ $module->slug }} {{ $ability }}"
                                    >
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="5">Belum ada module.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
@endsection
