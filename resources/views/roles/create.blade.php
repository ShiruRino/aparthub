@extends('layouts.app')

@section('title', 'Tambah Role')
@section('page_title', 'Tambah Role')
@section('page_subtitle', 'Buat role baru untuk user non-admin')

@section('content')
    <form class="panel panel-pad" method="POST" action="{{ route('roles.store') }}">
        @include('roles._form', [
            'submitLabel' => 'Simpan Role',
        ])
    </form>
@endsection
