<p>This is a demo game for Phaser (a flappy bird clone), adapted to Lorekeeper! All original example assets belong to the Phaser developers. Replace them with your own!</p>

<div class="form-group">
    {!! Form::label('start_text', 'Start Text') !!}
    {!! Form::text('start_text', $game->data->data['start_text'] ?? "Tap to Start", ['class' => 'form-control']) !!}
</div>

<div class="row">
    <div class="col-3 form-group">
        Player Image (<a href="/gamefiles/flappy/assets/default/player.png">Example</a>)
        <div>{!! Form::file('player_image') !!}</div>
    </div>
    <div class="col-3 form-group">Player Frame Width: <br>{!! Form::number('player_x', $game->data->data['coin_x'] ?? 48, ['class' => 'form-control d-inline', 'step' => 'any', 'style' => 'width: 100px']) !!} px</div>
    <div class="col-3 form-group">Player Frame Height: <br>{!! Form::number('player_y', $game->data->data['coin_y'] ?? 48, ['class' => 'form-control d-inline', 'step' => 'any', 'style' => 'width: 100px']) !!} px</div>
</div>

<div class="row">
    <div class="col-3 form-group">
        Coin Image (<a href="/gamefiles/flappy/assets/default/coin.png">Example</a>)
        <div>{!! Form::file('coin_image') !!}</div>
    </div>
    <div class="col-3 form-group">Coin Frame Width: <br>{!! Form::number('coin_x', $game->data->data['coin_x'] ?? 36, ['class' => 'form-control d-inline', 'step' => 'any', 'style' => 'width: 100px']) !!} px</div>
    <div class="col-3 form-group">Coin Frame Height: <br>{!! Form::number('coin_y', $game->data->data['coin_y'] ?? 36, ['class' => 'form-control d-inline', 'step' => 'any', 'style' => 'width: 100px']) !!} px</div>
</div>

<div class="row">
    <div class="col-3 form-group">
        Spikes Image (<a href="/gamefiles/flappy/assets/default/spikes.png">Example</a>)
        <div>{!! Form::file('spikes_image') !!}</div>
    </div>
</div>
<div class="row">
    <div class="col-3 form-group">
        Background Image (<a href="/gamefiles/flappy/assets/default/background.png">Example</a>)
        <div>{!! Form::file('background_image') !!}</div>
    </div>
</div>