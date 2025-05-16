<script>
    var start_text = "{{ $game->data->data['start_text'] ?? 'Tap to Start' }}";
    var player_x = {{ $game->data->data['player_x'] ?? 48 }};
    var player_y = {{ $game->data->data['player_y'] ?? 48 }};
    var coin_x = {{ $game->data->data['coin_x'] ?? 36 }};
    var coin_y = {{ $game->data->data['coin_y'] ?? 36 }};
</script>

<div id="game-container" style="width: 100%; height: 100%"></div>
<script src="https://cdn.jsdelivr.net/npm/phaser@v3.88.2/dist/phaser.min.js"></script>
<script type="module" src="/gamefiles/flappy/src/main.js"></script>
