@extends('admin.layout')

@section('admin-title')
    {{ $image->id ? 'Edit' : 'Create' }} MYO Maker Image
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'MYO Maker Images' => 'admin/data/myomaker',
        ($image->id ? 'Edit' : 'Create') . ' Image' => $image->id ? 'admin/data/myomaker/edit/' . $image->id : 'admin/data/myomaker/create',
    ]) !!}

    <h1>{{ $image->id ? 'Edit' : 'Create' }} MYO Maker Image
        @if ($image->id)
            <a href="#" class="btn btn-danger float-right delete-image-button">Delete Image</a>
        @endif
    </h1>

    {!! Form::open(['url' => 'admin/data/myomaker/create', 'files' => true]) !!}

    <div class="p-4">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', '', ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Category') !!}
            {!! Form::select('category_id', $categories, $image->category_id, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Image') !!}
            <div>{!! Form::file('image') !!}</div>
        </div>

        <div class="text-right">
            {!! Form::submit('Create', ['class' => 'btn btn-primary']) !!}
        </div>

        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $('.selectize').selectize();

        $(document).ready(function() {
            $('.delete-image-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/myomaker/delete') }}/{{ $image->id }}", 'Delete Image');
            });
        });
    </script>
@endsection
