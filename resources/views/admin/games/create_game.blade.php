@extends('admin.layout')

@section('admin-title')
{{ $game->id ? 'Edit' : 'Create' }} Game
@endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Games' => 'admin/data/games', ($game->id ? 'Edit' : 'Create') . ' Game' => $game->id ? 'admin/data/games/edit/' . $game->id : 'admin/data/games/create']) !!}

<h1>{{ $game->id ? 'Edit' : 'Create' }} Game
    @if ($game->id)
    <a href="#" class="btn btn-danger float-right delete-game-button">Delete Game</a>
    @endif
</h1>

{!! Form::open(['url' => $game->id ? 'admin/data/games/edit/' . $game->id : 'admin/data/games/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $game->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 200px x 200px</div>
    @if ($game->has_image)
    <div class="form-check">
        {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
        {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
    </div>
    @endif
</div>

<div class="row">
    <div class="col-4">
        <div class="form-group">
            {!! Form::label('Currency') !!} {!! add_help('The currency the game will award.') !!}
            {!! Form::select('currency_id', $currencies, $game->currency_id ? $game->currency_id : null, ['class' => 'form-control game-field', 'data-name' => 'currency_id']) !!}
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            {!! Form::label('Currency Cap') !!} {!! add_help('The max amount of currency the game will award. Prevents people from sending false high scores for infinite currency.') !!}
            {!! Form::number('currency_cap', $game->currency_cap, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            {!! Form::label('Score Multiplier') !!} {!! add_help('A decimal number indicating the currency-to-score ratio. The amount of currency rewarded is the score multiplied by this number. ') !!}
            {!! Form::number('score_ratio', $game->score_ratio, ['class' => 'form-control', 'step'=>'any']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $game->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_active', 1, $game->id ? $game->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the game will not be visible to regular users.') !!}
</div>

<p><strong>Click the "submit" button to be brought to a page where you can upload your game files.</strong></p>

<div class="text-right">
    {!! Form::submit($game->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
    $(document).ready(function() {
        $('.delete-game-button').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/data/games/delete') }}/{{ $game->id }}", 'Delete game');
        });
    });
</script>
@endsection