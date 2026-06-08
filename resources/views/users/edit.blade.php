@extends('layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Edit User')
@section('page_subtitle', $user->username)

@section('content')
    <form class="panel panel-pad" method="POST" action="{{ route('users.update', $user) }}">
        @include('users._form', [
            'user' => $user,
            'roles' => $roles,
            'method' => 'PUT',
            'submitLabel' => 'Perbarui User',
        ])
    </form>
@endsection
