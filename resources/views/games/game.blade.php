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

        /* function to call for sending game scores */
        const submit_score = (score) => {
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
