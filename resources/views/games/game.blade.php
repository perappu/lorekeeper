@extends('games.layout')

@section('games-title')
    {{ $game->name }}
@endsection

@section('games-content')
    <x-admin-edit title="Game" :object="$game" />
    {!! breadcrumbs(['Games' => 'games', $game->name => $game->url]) !!}

    <h1>
        {{ $game->name }}
    </h1>

    <div id="includedContent"></div>
    
@endsection

@section('scripts')
@parent
<script>
    $(function() {
        $("#includedContent").load("{{ asset($game->htmlUrl) }}");
    });
</script>
@endsection
