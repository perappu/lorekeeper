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


    {!! Form::open(['url' => '/games/score' ]) !!}

    {!! Form::label('user_id') !!}
        {!! Form::text('user_id', Auth::user()->id, ['class' => 'form-control']) !!}

    {!! Form::label('game_id') !!}
        {!! Form::text('game_id', $game->id, ['class' => 'form-control']) !!}

        {!! Form::label('score') !!}
        {!! Form::text('score', '10', ['class' => 'form-control']) !!}

        {!! Form::submit('submit', ['class' => 'btn btn-primary']) !!}

        {!! Form::close() !!}

@endsection

@section('scripts')
    @parent
    <script>
        $(function() {
            $("#includedContent").load("{{ asset($game->htmlUrl) }}");
        });

        /* function to call for sending game scores */
        score = 10;

        const submit_score = () => {
        $.ajax({
            url: "{{ url ('/games/score') }}",
            type: "POST",
            data: {
                'game_id': "{{ $game->id }}",
                'user_id': "{{ Auth::user()->id }}",
                'score': score,
            },
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
            success: (data) => {
                console.log("Score submitted successfully");
            },
            error: (error) => {
                console.log("Error submitting score");
            }
        });
    }
        </script>
@endsection
