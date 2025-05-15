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

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $game->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_active', 1, $game->id ? $game->is_active : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the game will not be visible to regular users.') !!}
    </div>

    <hr>

    <div class="form-group">
        {!! Form::label('Game Type') !!} {!! add_help('Whether this game is a full-fledge game page or simply a link that will be included in the games room.') !!}
        {!! Form::select('game_type', ['link' => 'Link', 'game' => 'Game'], 'link', ['class' => 'form-control', 'data-name' => 'game_type']) !!}
    </div>

    <p><strong>Click the "submit" button to be brought to a page where you can add specifics.</strong></p>

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
