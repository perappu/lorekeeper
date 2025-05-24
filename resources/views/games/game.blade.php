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
        <p>You have played {{ $gameScore ? $gameScore->times_played : 0 }}/{{ $game->times_playable }} times {{ $timeframe }}.</p>
    </div>

    @if (isset($game->data))
        @if (isset($gameScore) && $gameScore->times_played >= $game->times_playable)
            <div class="text-center">
                Sorry, you've played the maximum number of times {{ $timeframe }}. Come back later!
            </div>
        @else
            @include('games.games.' . $game->data->game, ['game' => $game, 'data' => $game->data])
        @endif
    @else
        <div class="text-center">
            This game has not been set up yet. Come back later!
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        /* code is minified */
        const submitScore = async e => $.ajax({
            url: "{{ url('/games/score') }}",
            type: "POST",
            data: {
                game_id: "{{ $game->id }}",
                user_id: "{{ Auth::user()->id }}",
                score: e
            },
            headers: {
                "X-CSRF-Token": "{{ csrf_token() }}"
            },
            success: e => (console.log("Score submitted!"), e),
            error: e => (console.log("Error with sending score"), console.log(e), e)
        }), canSubmit = async () => {
            $.ajax({
                url: "{{ url('/games/score/check') }}",
                type: "POST",
                data: {
                    game_id: "{{ $game->id }}",
                    user_id: "{{ Auth::user()->id }}"
                },
                headers: {
                    "X-CSRF-Token": "{{ csrf_token() }}"
                },
                success: e => (console.log(e), !0),
                error: e => (console.log(e), !1)
            })
        }, chargeCurrency = async (e, r) => {
            $.ajax({
                url: "{{ url('/games/charge') }}",
                type: "POST",
                data: {
                    game_id: "{{ $game->id }}",
                    user_id: "{{ Auth::user()->id }}",
                    currency_id: e,
                    amount: r
                },
                headers: {
                    "X-CSRF-Token": "{{ csrf_token() }}"
                },
                success: e => (console.log(e), !0),
                error: e => (console.log(e), !1)
            })
        };
    </script>
@endsection
