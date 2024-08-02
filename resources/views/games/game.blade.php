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

    <div class="text-center">
        {!! $game->description !!}
        <p>You have played {{ $gameScore->times_played }}/{{ $game->times_playable }} times today.</p>
    </div>

    @if ($gameScore->times_played >= $game->times_playable)
        <div class="text-center">
            Sorry, you've played the maximum number of times today. Come back tomorrow!
        </div>
    @else
        <div id="includedContent"></div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(function() {
            $("#includedContent").load("{{ asset($game->htmlUrl) }}");
        });

        const gameDirectory = "{{ $game->fileDirectory }}";

        /* function to call for sending game scores */
        const submit_score = (score) => {
            $.ajax({
                url: "{{ url('/games/score') }}",
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
                    console.log("Score submitted!");
                },
                error: (error) => {
                    console.log("Error with sending score");
                }
            });
        }
    </script>
@endsection
