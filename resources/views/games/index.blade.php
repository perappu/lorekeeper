@extends('games.layout')

@section('games-title')
    Shop Index
@endsection

@section('games-content')
    {!! breadcrumbs(['Games' => 'games']) !!}

    <h1>
        Games
    </h1>

    <div class="row shops-row">
        @foreach ($games as $game)
            <div class="col-md-3 col-6 mb-3 text-center">
                @if ($game->has_image)
                    <div class="game-image">
                        <a href="{{ $game->url }}"><img src="{{ $game->gameImageUrl }}" alt="{{ $game->name }}" /></a>
                    </div>
                @endif
                <div class="game-name mt-1">
                    <a href="{{ $game->url }}" class="h5 mb-0">{{ $game->name }}</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
