@extends('layouts.app')

@section('title', 'Tambah Module')
@section('page_title', 'Tambah Module')
@section('page_subtitle', 'Buat module baru untuk permission')

@section('content')
    <form class="panel panel-pad" method="POST" action="{{ route('modules.store') }}">
        @include('modules._form', [
            'submitLabel' => 'Simpan Module',
        ])
    </form>
@endsection
