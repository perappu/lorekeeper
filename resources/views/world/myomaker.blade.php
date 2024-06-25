@extends('world.layout')

@section('world-title')
    Subtypes
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'MYO Maker' => 'myomaker']) !!}
    <h1>MYO Maker</h1>

    @include('world._image_maker')
@endsection
