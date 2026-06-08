@extends('layouts.app')

@section('title', 'Edit Module')
@section('page_title', 'Edit Module')
@section('page_subtitle', $module->slug)

@section('content')
    <form class="panel panel-pad" method="POST" action="{{ route('modules.update', $module) }}">
        @include('modules._form', [
            'module' => $module,
            'method' => 'PUT',
            'submitLabel' => 'Perbarui Module',
        ])
    </form>
@endsection
