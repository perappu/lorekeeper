@extends('admin.layout')

@section('admin-title')
    {{ $category->id ? 'Edit' : 'Create' }} Game Category
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Games' => 'admin/data/games', 'Game Categories' => 'admin/data/game-categories', ($category->id ? 'Edit' : 'Create') . ' Game Category' => $category->id ? 'admin/data/game-categories/edit/' . $category->id : 'admin/data/game-categories/create']) !!}

    <h1>{{ $category->id ? 'Edit' : 'Create' }} Game Category
        @if ($category->id)
            <a href="#" class="btn btn-danger float-right delete-category-button">Delete Category</a>
        @endif
    </h1>

    {!! Form::open(['url' => $category->id ? 'admin/data/game-categories/edit/' . $category->id : 'admin/data/game-categories/create', 'files' => true]) !!}

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $category->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Image (Optional)') !!}
        <div>{!! Form::file('image') !!}</div>
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $category->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="text-right">
        {!! Form::submit($category->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-category-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/game-categories/delete') }}/{{ $category->id }}", 'Delete Category');
            });
        });
    </script>
@endsection
