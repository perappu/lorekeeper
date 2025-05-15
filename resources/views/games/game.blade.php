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

    @if(isset($game->data))
        @if (isset($gameScore) && ($gameScore->times_played >= $game->times_playable))
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
        /* helper functions for games to check and send scores */
        const submitScore = async (score) => {
            var data = $.ajax({
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
                    return data;
                },
                error: (error) => {
                    console.log("Error with sending score");
                    console.log(error);
                    return error;
                }
            });

            return data;
        }
        const canSubmit = async () => {
            $.ajax({
                url: "{{ url('/games/score/check') }}",
                type: "POST",
                data: {
                    'game_id': "{{ $game->id }}",
                    'user_id': "{{ Auth::user()->id }}",
                },
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                success: (data) => {
                    console.log(data);
                    return true;
                },
                error: (error) => {
                    console.log(error);
                    return false;
                }
            });
        }
        const chargeCurrency = async (currencyID, amount) => {
            $.ajax({
                url: "{{ url('/games/charge') }}",
                type: "POST",
                data: {
                    'game_id': "{{ $game->id }}",
                    'user_id': "{{ Auth::user()->id }}",
                    'currency_id': currencyID,
                    'amount' : amount
                },
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                success: (data) => {
                    console.log(data);
                    return true;
                },
                error: (error) => {
                    console.log(error);
                    return false;
                }
            });
        }
    </script>
@endsection
