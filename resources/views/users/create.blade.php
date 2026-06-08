@extends('layouts.app')

@section('title', 'Tambah User')
@section('page_title', 'Tambah User')
@section('page_subtitle', 'Buat akun baru dengan username')

@section('content')
    <form class="panel panel-pad" method="POST" action="{{ route('users.store') }}">
        @include('users._form', [
            'roles' => $roles,
            'submitLabel' => 'Simpan User',
            'passwordRequired' => true,
        ])
    </form>
@endsection
