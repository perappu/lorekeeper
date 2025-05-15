<div id="game" class="text-center">
    <button id="submitButton" class="btn btn-primary" onclick="submitGame({{ $game->data->data['score'] }})">Submit a Score</button>
</div>

<script>
    const submitGame = async (score) => {

        $("#submitButton").prop("disabled", true);

        var result = await submitScore(score);

        console.log(result);

        $("#game").html("You earned: " + result['reward'] + ` {!! $game->currency->displayName !!}.`);
    }
</script>