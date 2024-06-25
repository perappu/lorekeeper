@extends('admin.layout')

@section('admin-title')
    {{ $category->id ? 'Edit' : 'Create' }} Random Generator Category
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Random Generators' => 'admin/data/randomgenerator',
        ($category->id ? 'Edit' : 'Create') . ' Category' => $category->id ? 'admin/data/randomgenerator/category/edit/' . $category->id : 'admin/data/randomgenerator/category/create',
    ]) !!}

    <h1>{{ $category->id ? 'Edit' : 'Create' }} Random Generator Category
        @if ($category->id)
            <a href="#" class="btn btn-danger float-right delete-category-button">Delete Category</a>
        @endif
    </h1>

    {!! Form::open(['url' => $category->id ? 'admin/data/randomgenerator/category/edit/' . $category->id : 'admin/data/randomgenerator/category/create', 'files' => true]) !!}


    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $category->name, ['class' => 'form-control']) !!}
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
                loadModal("{{ url('admin/data/randomgenerator/category/delete') }}/{{ $category->id }}", 'Delete Category');
            });
        });
    </script>
@endsection