@extends('layouts.app')

@section('title', 'Edit Role')
@section('page_title', 'Edit Role')
@section('page_subtitle', $role->slug)

@section('content')
    <form class="panel panel-pad" method="POST" action="{{ route('roles.update', $role) }}">
        @include('roles._form', [
            'role' => $role,
            'method' => 'PUT',
            'submitLabel' => 'Perbarui Role',
        ])
    </form>
@endsection
