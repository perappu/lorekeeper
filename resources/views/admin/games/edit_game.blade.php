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
                {!! Form::label('Currency') !!}
                {!! Form::select('currency_id', $currencies, $game->currency_id ? $game->currency_id : null, ['class' => 'form-control game-field', 'data-name' => 'currency_id']) !!}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {!! Form::label('Currency Cap') !!}
                {!! Form::number('currency_cap', $game->currency_cap, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                {!! Form::label('Currency to Score Ratio') !!}
                {!! Form::text('score_ratio', $game->score_ratio, ['class' => 'form-control']) !!}
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

    <div class="text-right">
        {!! Form::submit($game->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}


    <h3>FOR DEVELOPERS</h3>

    <p><b>For non-developers:</b> Someone who has created a game will give you the file to upload into these fields. <b>Please be careful who you accept files from. This puts arbitrary HTML and scripts on your website.</b></p>
    <p>Otherwise, don't touch this unless you're developing your own game!</p>

    <h4>HTML</h4>

    {!! Form::open(['url' => 'admin/data/games/file/'.$game->id.'/upload', 'id' => 'uploadForm', 'class' => 'file-form', 'files' => true]) !!}
    <p>Select a file to upload. (Maximum size {{ min(ini_get('upload_max_filesize'), ini_get('post_max_size')) }}B.)</p>
    <div class="row mb-4">
        <div class="col-6">
            {!! Form::file('files[]', ['class' => 'form-control']) !!}
            {!! Form::hidden('folder', $game->fileDirectory, ['class' => 'edit-folder']) !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Upload', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
    {!! Form::close() !!}

    <h4>Files</h4>

    <p>You can upload any arbitrary files here and they will be automatically placed in the game's directory.</p>
    For this game, that is currently: {{ $game->filesDirectory }}/</p>

    <p>If a specific file structure is needed, such as for certain game engines, please use FTP to upload the files.</p>

    <a href="/admin/data/games/files/{{ $game->id }}" class="btn btn-primary float-right">Upload Files</a>

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