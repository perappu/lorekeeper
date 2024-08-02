@extends('layouts.app')

@section('title')
    Doom
@endsection

@section('content')
    {!! breadcrumbs(['Doom' => 'doom']) !!}

    <!-- js-dos style sheet -->
    <link rel="stylesheet" href="https://v8.js-dos.com/latest/js-dos.css">

    <!-- js-dos -->
    <script src="https://v8.js-dos.com/latest/js-dos.js"></script>

    <div id="dos" style="height: 960px"></div>

    <script>
        Dos(document.getElementById("dos"), {
            url: "https://cdn.dos.zone/custom/dos/doom.jsdos",
            style: "hidden",
            noSocialLinks: true
        });
    </script>
@endsection
