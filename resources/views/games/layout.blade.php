@extends('layouts.app')

@section('title')
    Games ::
    @yield('games-title')
@endsection

@section('sidebar')
    @include('games._sidebar')
@endsection

@section('content')
    @yield('games-content')
@endsection

@section('scripts')
    @parent
@endsection
